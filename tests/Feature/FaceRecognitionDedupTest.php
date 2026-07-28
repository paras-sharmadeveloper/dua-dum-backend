<?php

use App\Models\FaceRecord;
use App\Models\FaceRecordDetail;
use App\Models\Token;
use App\Services\FaceRecognitionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

function makeToken(string $phoneNumber): Token
{
    return Token::create([
        'venue_id' => (string) Str::uuid(),
        'venue_category_group_id' => (string) Str::uuid(),
        'venue_category_id' => (string) Str::uuid(),
        'token_number' => '1',
        'status' => 'Pending',
        'user_type' => 'normal_person',
        'service_type' => 'dua',
        'user_name' => 'Ali',
        'city' => 'Lahore',
        'phone_number' => $phoneNumber,
    ]);
}

test('same face booked under a different phone number reuses the same face record', function () {
    $fakeEncoding = array_fill(0, 128, 0.12345);
    $callCount = 0;

    Http::fake(function () use (&$callCount, $fakeEncoding) {
        $callCount++;

        if ($callCount === 1) {
            // First booking: no known faces yet, Python returns a fresh encoding.
            return Http::response([
                'recognized' => false,
                'face_encoding' => $fakeEncoding,
                'distance' => null,
                'message' => 'No match found, face encoding generated',
            ], 200);
        }

        // Second booking (different phone number, same face): Python matches
        // it against the FaceRecord created by the first call.
        $faceRecord = FaceRecord::first();

        return Http::response([
            'recognized' => true,
            'id' => FaceRecordDetail::first()->id,
            'face_record_id' => $faceRecord->id,
            'face_encoding' => $fakeEncoding,
            'distance' => 0.0,
            'message' => 'Face recognized',
        ], 200);
    });

    $service = app(FaceRecognitionService::class);

    $tokenA = makeToken('03001234567');
    $service->recognizeFace(base64_encode('fake-image-a'), 'Ali', $tokenA->id, 'faces/a.jpg');

    $tokenB = makeToken('03009999999');
    $service->recognizeFace(base64_encode('fake-image-b'), 'Ali', $tokenB->id, 'faces/b.jpg');

    expect(FaceRecord::count())->toBe(1);
    expect(FaceRecordDetail::count())->toBe(2);

    $detailA = FaceRecordDetail::where('token_id', $tokenA->id)->firstOrFail();
    $detailB = FaceRecordDetail::where('token_id', $tokenB->id)->firstOrFail();

    expect($detailA->face_record_id)->toBe($detailB->face_record_id);
    expect($detailB->status)->toBe('Found');

    expect(FaceRecord::first()->face_count)->toBe(2);
});

test('a Python API failure does not fabricate a new face record', function () {
    Http::fake(fn () => Http::response('Service Unavailable', 500));

    $service = app(FaceRecognitionService::class);
    $token = makeToken('03001112222');

    $service->recognizeFace(base64_encode('fake-image'), 'Ali', $token->id, 'faces/a.jpg');

    expect(FaceRecord::count())->toBe(0);
    expect(FaceRecordDetail::count())->toBe(0);
});
