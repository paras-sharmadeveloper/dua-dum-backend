<?php

namespace App\Services;

use App\Models\FaceRecord;
use App\Models\FaceRecordDetail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FaceRecognitionService
{
    protected $apiUrl;
    protected $indexAddUrl;
    protected $indexRemoveUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.face_recognition.url');
        $this->indexAddUrl = config('services.face_recognition.index_add_url');
        $this->indexRemoveUrl = config('services.face_recognition.index_remove_url');
    }

    /**
     * Send image to Python API for face recognition, then decide match vs.
     * new person and persist. Wrapped in a lock: two concurrent leads for
     * the same brand-new face could otherwise both get "no match" from the
     * search below and each create a separate FaceRecord for the same real
     * person - the lock serializes the whole search-decide-persist sequence
     * so that can't happen. The critical section itself is fast (one FAISS
     * lookup + a couple of inserts), so this doesn't meaningfully hurt
     * throughput.
     */
    public function recognizeFace($imageBase64, $userName, $tokenId, $imagePath)
    {
        try {
            return Cache::lock('face-recognition-write', 10)->block(5, function () use ($imageBase64, $userName, $tokenId, $imagePath) {
                // Send request to Python API
                $response = Http::timeout(30)->post($this->apiUrl, [
                    'image' => $imageBase64,
                    'name' => $userName
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    Log::info('Face recognition response:', $result);

                    // Process the response, save to database, and keep the
                    // live match index in sync with the new row.
                    // Every visit's encoding is added to the index (not just
                    // first-encounters) - matches the old brute-force scan,
                    // which compared against every stored encoding, and
                    // gives future lookups more reference points per person.
                    $detail = $this->saveFaceRecognitionResult($result, $userName, $tokenId, $imagePath);
                    if ($detail) {
                        $this->addToIndex($detail, $result['face_encoding']);
                    }

                    return $result;
                } else {
                    Log::error('Face recognition API error: ' . $response->body());
                    // API failure — no encoding to work with, don't fabricate a face record
                    $this->saveFaceRecognitionResult(['recognized' => false], $userName, $tokenId, $imagePath);
                    return null;
                }
            });
        } catch (\Illuminate\Contracts\Cache\LockTimeoutException $e) {
            Log::error('Face recognition lock timed out, skipping this attempt', ['token_id' => $tokenId]);
            return null;
        } catch (\Exception $e) {
            Log::error('Face recognition exception: ' . $e->getMessage());
            $this->saveFaceRecognitionResult(['recognized' => false], $userName, $tokenId, $imagePath);
            return null;
        }
    }

    /**
     * Look up a person by photo without mutating any data — used by the admin
     * "search by photo" tool. Returns the raw Python API response.
     */
    public function search(string $imageBase64): array
    {
        try {
            $response = Http::timeout(30)->post($this->apiUrl, [
                'image' => $imageBase64,
            ]);

            if (!$response->successful()) {
                Log::error('Face recognition search API error: ' . $response->body());
                return ['recognized' => false];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Face recognition search exception: ' . $e->getMessage());
            return ['recognized' => false];
        }
    }

    /**
     * Save face recognition result to database. Returns the created
     * FaceRecordDetail (refreshed so its DB-generated faiss_id is
     * populated) so the caller can add its encoding to the live index, or
     * null if nothing was persisted.
     */
    protected function saveFaceRecognitionResult($result, $userName, $tokenId, $imagePath): ?FaceRecordDetail
    {
        try {
            // No encoding means the Python service failed (outage, no face detected,
            // etc.) rather than genuinely evaluating the photo — skip persistence
            // entirely instead of fabricating a new FaceRecord for a failed attempt.
            if (!isset($result['face_encoding'])) {
                Log::warning('Face recognition result has no encoding, skipping persistence', [
                    'token_id' => $tokenId,
                ]);
                return null;
            }

            $faceEncoding = json_encode($result['face_encoding']);
            $faceRecordId = null;
            $status = 'Not Found';

            if (isset($result['recognized']) && $result['recognized'] === true) {
                // Face was recognized - find or create face record by face_record_id from Python API
                $faceId = $result['face_record_id'] ?? null;
                $faceName = $result['name'] ?? $userName;

                if ($faceId) {
                    // Try to find existing face record by face_id
                    $faceRecord = FaceRecord::where('id', $faceId)->first();

                    if ($faceRecord) {
                        // Increment face_count
                        $faceRecord->increment('face_count');
                    } else {
                        // Create new face record with face_id from Python API
                        $faceRecord = FaceRecord::create([
                            'id' => (string) Str::uuid(),
                            'face_id' => $faceId,
                            'name' => $faceName,
                            'face_count' => 1
                        ]);
                    }

                    $faceRecordId = $faceRecord->id;
                    $status = 'Found';
                } else {
                    // face_record_id is null, create new face record
                    $newFaceId = (string) Str::uuid();
                    $faceRecord = FaceRecord::create([
                        'id' => (string) Str::uuid(),
                        'face_id' => $newFaceId,
                        'name' => $faceName,
                        'face_count' => 1
                    ]);

                    $faceRecordId = $faceRecord->id;
                    $status = 'Found';
                }

            } else {
                // Face not recognized - create a new face record for this unknown face
                $newFaceId = (string) Str::uuid();
                $faceRecord = FaceRecord::create([
                    'id' => (string) Str::uuid(),
                    'face_id' => $newFaceId,
                    'name' => $userName,
                    'face_count' => 1
                ]);

                $faceRecordId = $faceRecord->id;
                $status = 'Not Found';
            }

            // Create face record detail with image_path and face_encoding
            $detail = FaceRecordDetail::create([
                'id' => (string) Str::uuid(),
                'face_record_id' => $faceRecordId,
                'token_id' => $tokenId,
                'status' => $status,
                'image_path' => null,
                'face_encoding' => $faceEncoding
            ]);
            // faiss_id is DB-generated (auto_increment), not set by create()
            // above - refresh to pull it back before the caller needs it.
            $detail->refresh();

            Log::info('Face recognition result saved', [
                'token_id' => $tokenId,
                'face_record_id' => $faceRecordId,
                'status' => $status,
                'image_path' => $imagePath,
                'has_encoding' => !is_null($faceEncoding)
            ]);

            return $detail;
        } catch (\Exception $e) {
            Log::error('Error saving face recognition result: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Add a newly-persisted encoding to the live FAISS index so future
     * lookups can match against it immediately. Best-effort: a failure here
     * just means this one encoding is missing from the index until the
     * service's next DB-reconciled rebuild - the DB row (source of truth)
     * is already safely committed regardless.
     */
    protected function addToIndex(FaceRecordDetail $detail, array $faceEncoding): void
    {
        try {
            $response = Http::timeout(10)->post($this->indexAddUrl, [
                'faiss_id' => $detail->faiss_id,
                'face_encoding' => $faceEncoding,
            ]);

            if (!$response->successful()) {
                Log::error('Failed to add encoding to face match index: ' . $response->body(), [
                    'face_record_detail_id' => $detail->id,
                    'faiss_id' => $detail->faiss_id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception adding encoding to face match index: ' . $e->getMessage(), [
                'face_record_detail_id' => $detail->id,
                'faiss_id' => $detail->faiss_id,
            ]);
        }
    }

    /**
     * Remove encodings from the live FAISS index - called when a FaceRecord
     * (and its cascaded FaceRecordDetail rows) is deleted, so a removed
     * person's photo stops matching immediately instead of lingering until
     * the next index rebuild.
     */
    public function removeFromIndex(array $faissIds): void
    {
        $faissIds = array_values(array_filter($faissIds, fn ($id) => !is_null($id)));
        if (empty($faissIds)) {
            return;
        }

        try {
            $response = Http::timeout(10)->post($this->indexRemoveUrl, [
                'faiss_ids' => $faissIds,
            ]);

            if (!$response->successful()) {
                Log::error('Failed to remove encodings from face match index: ' . $response->body(), [
                    'faiss_ids' => $faissIds,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception removing encodings from face match index: ' . $e->getMessage(), [
                'faiss_ids' => $faissIds,
            ]);
        }
    }
}
