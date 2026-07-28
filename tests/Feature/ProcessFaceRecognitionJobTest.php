<?php

use App\Jobs\ProcessFaceRecognitionJob;
use App\Models\FaceRecord;
use App\Models\FaceRecordDetail;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Models\VenueCategoryCounter;
use App\Models\VenueCategoryGroup;
use App\Models\VenueCategoryRange;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function makeBookableVenue(): Venue
{
    $venue = Venue::create([
        'id' => (string) Str::uuid(),
        'venue_name' => 'Test Venue',
        'venue_code' => 'TV1',
        'user_id' => 1,
        'start_date' => now(),
        'end_date' => now()->addDays(30),
        'location_group_id' => (string) Str::uuid(),
        'general_dua_token' => 100,
        'general_dum_token' => 100,
        'working_lady_dua_token' => 100,
        'venue_address_eng' => 'Test Address',
        'venue_address_urdu' => 'Test Address',
        'status_page_note_eng' => 'Note',
        'status_page_note_urdu' => 'Note',
        'status' => 'Active',
    ]);

    $group = VenueCategoryGroup::create(['id' => (string) Str::uuid(), 'name' => 'General', 'code' => 'NP']);
    $category = VenueCategory::create(['id' => (string) Str::uuid(), 'name' => 'DUA']);

    VenueCategoryCounter::create([
        'id' => (string) Str::uuid(),
        'venue_id' => $venue->id,
        'venue_category_group_id' => $group->id,
        'venue_category_id' => $category->id,
        'last_issued_no' => 0,
    ]);

    VenueCategoryRange::create([
        'id' => (string) Str::uuid(),
        'venue_id' => $venue->id,
        'venue_category_group_id' => $group->id,
        'venue_category_id' => $category->id,
        'range_start' => 1,
        'range_end' => 100,
    ]);

    return $venue;
}

test('booking a token queues face recognition instead of running it inline', function () {
    Queue::fake();
    Storage::fake('s3');

    $venue = makeBookableVenue();

    $request = Request::create('/api/tokens/generate', 'POST', [
        'user_image' => 'data:image/png;base64,' . base64_encode('fake-image-bytes'),
        'user_type' => 'normal_person',
        'venue_id' => $venue->id,
        'service_type' => 'dua',
        'user_name' => 'Ali',
        'city' => 'Lahore',
        'phone_number' => '03001234567',
    ]);

    $result = app(TokenService::class)->generateToken($request);

    expect($result['success'])->toBeTrue();
    Queue::assertPushed(ProcessFaceRecognitionJob::class, 1);
});

test('the queued job performs the actual face recognition when it runs', function () {
    Http::fake(fn () => Http::response([
        'recognized' => false,
        'face_encoding' => array_fill(0, 128, 0.1),
        'message' => 'No match found, face encoding generated',
    ], 200));

    $venue = makeBookableVenue();
    $token = \App\Models\Token::create([
        'venue_id' => $venue->id,
        'venue_category_group_id' => (string) Str::uuid(),
        'venue_category_id' => (string) Str::uuid(),
        'token_number' => '1',
        'status' => 'Pending',
        'user_type' => 'normal_person',
        'service_type' => 'dua',
        'user_name' => 'Ali',
        'city' => 'Lahore',
        'phone_number' => '03001234567',
    ]);

    $job = new ProcessFaceRecognitionJob(
        'data:image/png;base64,' . base64_encode('fake-image-bytes'),
        'Ali',
        $token->id,
    );

    $job->handle(app(\App\Services\FaceRecognitionService::class));

    expect(FaceRecord::count())->toBe(1);
    expect(FaceRecordDetail::count())->toBe(1);
});
