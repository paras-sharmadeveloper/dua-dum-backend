<?php

namespace App\Services;

use App\Models\FaceRecord;
use App\Models\FaceRecordDetail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FaceRecognitionService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.face_recognition.url');
    }

    /**
     * Send image to Python API for face recognition
     */
    public function recognizeFace($imageBase64, $userName, $tokenId, $imagePath)
    {
        try {
            // Send request to Python API
            $response = Http::timeout(30)->post($this->apiUrl, [
                'image' => $imageBase64,
                'name' => $userName
            ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Face recognition response:', $result);

                // Process the response and save to database
                $this->saveFaceRecognitionResult($result, $userName, $tokenId, $imagePath);

                return $result;
            } else {
                Log::error('Face recognition API error: ' . $response->body());
                // API failure — no encoding to work with, don't fabricate a face record
                $this->saveFaceRecognitionResult(['recognized' => false], $userName, $tokenId, $imagePath);
                return null;
            }
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
     * Save face recognition result to database
     */
    protected function saveFaceRecognitionResult($result, $userName, $tokenId, $imagePath)
    {
        try {
            // No encoding means the Python service failed (outage, no face detected,
            // etc.) rather than genuinely evaluating the photo — skip persistence
            // entirely instead of fabricating a new FaceRecord for a failed attempt.
            if (!isset($result['face_encoding'])) {
                Log::warning('Face recognition result has no encoding, skipping persistence', [
                    'token_id' => $tokenId,
                ]);
                return;
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
            FaceRecordDetail::create([
                'id' => (string) Str::uuid(),
                'face_record_id' => $faceRecordId,
                'token_id' => $tokenId,
                'status' => $status,
                'image_path' => null,
                'face_encoding' => $faceEncoding
            ]);

            Log::info('Face recognition result saved', [
                'token_id' => $tokenId,
                'face_record_id' => $faceRecordId,
                'status' => $status,
                'image_path' => $imagePath,
                'has_encoding' => !is_null($faceEncoding)
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving face recognition result: ' . $e->getMessage());
        }
    }
}
