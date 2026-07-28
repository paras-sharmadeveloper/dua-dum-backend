<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FaceRecord;
use App\Models\FaceRecordDetail;
use App\Models\Token;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FacialRecognitionController extends Controller
{
    protected FaceRecognitionService $faceRecognitionService;

    public function __construct(FaceRecognitionService $faceRecognitionService)
    {
        $this->faceRecognitionService = $faceRecognitionService;
    }

    /**
     * Verification tab — paginated list of enrolled face records.
     */
    public function records(Request $request)
    {
        try {
            $query = FaceRecord::withCount('details');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('face_id', 'like', "%{$search}%");
                });
            }

            $perPage = $request->input('per_page', 10);
            $records = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json($records);
        } catch (\Exception $e) {
            Log::error('Facial recognition records index error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load face records'], 500);
        }
    }

    /**
     * Logs tab — paginated history of recognition attempts (face_record_details).
     */
    public function logs(Request $request)
    {
        try {
            $query = FaceRecordDetail::with(['faceRecord', 'token']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('token', function ($t) use ($search) {
                        $t->where('user_name', 'like', "%{$search}%")
                            ->orWhere('token_code', 'like', "%{$search}%");
                    })->orWhereHas('faceRecord', function ($f) use ($search) {
                        $f->where('name', 'like', "%{$search}%")
                            ->orWhere('face_id', 'like', "%{$search}%");
                    });
                });
            }

            $perPage = $request->input('per_page', 10);
            $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json($logs);
        } catch (\Exception $e) {
            Log::error('Facial recognition logs index error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load recognition logs'], 500);
        }
    }

    /**
     * Enrollment tab — token picker (scoped to facial-recognition-mapping so it
     * doesn't require the unrelated token-list permission).
     */
    public function searchTokens(Request $request)
    {
        $search = $request->input('search');

        $query = Token::query()->select('id', 'token_code', 'user_name', 'phone_number', 'status');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('token_code', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $tokens = $query->orderBy('created_at', 'desc')->limit(20)->get();

        return response()->json(['data' => $tokens]);
    }

    /**
     * Enrollment tab — manually map a token to a face record.
     */
    public function storeMapping(Request $request)
    {
        $data = $request->validate([
            'token_id' => 'required|uuid|exists:tokens,id',
            'name'     => 'required|string|max:255',
            'face_id'  => 'nullable|string|max:255',
        ]);

        try {
            $detail = DB::transaction(function () use ($data) {
                if (!empty($data['face_id'])) {
                    $faceRecord = FaceRecord::where('face_id', $data['face_id'])->first();
                    if ($faceRecord) {
                        $faceRecord->increment('face_count');
                    } else {
                        $faceRecord = FaceRecord::create([
                            'id'         => (string) Str::uuid(),
                            'face_id'    => $data['face_id'],
                            'name'       => $data['name'],
                            'face_count' => 1,
                        ]);
                    }
                } else {
                    $faceRecord = FaceRecord::create([
                        'id'         => (string) Str::uuid(),
                        'face_id'    => (string) Str::uuid(),
                        'name'       => $data['name'],
                        'face_count' => 1,
                    ]);
                }

                return FaceRecordDetail::create([
                    'id'              => (string) Str::uuid(),
                    'face_record_id'  => $faceRecord->id,
                    'token_id'        => $data['token_id'],
                    'status'          => 'Found',
                    'image_path'      => null,
                    'face_encoding'   => null,
                ])->load('faceRecord', 'token');
            });

            return response()->json(['data' => $detail, 'message' => 'Face mapped successfully.'], 201);
        } catch (\Exception $e) {
            Log::error('Facial recognition manual mapping error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to save mapping'], 500);
        }
    }

    /**
     * Search tab — look up a person by photo and return their full visit history.
     * Never mutates data; a lookup is not a booking.
     */
    public function search(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $imageBase64 = base64_encode(file_get_contents($request->file('image')->getRealPath()));
            $result = $this->faceRecognitionService->search($imageBase64);

            $faceRecordId = $result['face_record_id'] ?? null;

            if (empty($result['recognized']) || !$faceRecordId) {
                return response()->json(['matched' => false, 'message' => $result['message'] ?? 'No matching face found.']);
            }

            $faceRecord = FaceRecord::with(['details' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }, 'details.token'])->find($faceRecordId);

            if (!$faceRecord) {
                return response()->json(['matched' => false, 'message' => 'No matching face found.']);
            }

            return response()->json([
                'matched' => true,
                'face_record' => [
                    'id' => $faceRecord->id,
                    'face_id' => $faceRecord->face_id,
                    'name' => $faceRecord->name,
                    'face_count' => $faceRecord->face_count,
                ],
                'history' => $faceRecord->details->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'status' => $detail->status,
                        'created_at' => $detail->created_at,
                        'token' => $detail->token ? [
                            'id' => $detail->token->id,
                            'token_code' => $detail->token->token_code,
                            'user_name' => $detail->token->user_name,
                            'phone_number' => $detail->token->phone_number,
                            'status' => $detail->token->status,
                        ] : null,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            Log::error('Facial recognition search error: ' . $e->getMessage());
            return response()->json(['message' => 'Face search failed'], 500);
        }
    }

    /**
     * Delete an enrolled face record (cascades its recognition logs).
     */
    public function destroyRecord(string $id)
    {
        $faceRecord = FaceRecord::findOrFail($id);
        $faceRecord->delete();

        return response()->json(['message' => 'Face record deleted.']);
    }
}
