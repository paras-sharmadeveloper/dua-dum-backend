<?php

use App\Models\Setting;
use App\Models\Venue;
use Illuminate\Support\Str;

function makeTestVenue(array $overrides = []): Venue
{
    return Venue::create(array_merge([
        'id' => (string) Str::uuid(),
        'venue_name' => 'Test Venue',
        'venue_code' => 'TV-' . Str::random(6),
        'user_id' => 1,
        'start_date' => '2026-05-14 01:00:00',
        'end_date' => '2026-05-15 01:00:00',
        'location_group_id' => (string) Str::uuid(),
        'general_dua_token' => 10,
        'general_dum_token' => 10,
        'working_lady_dua_token' => 10,
        'venue_address_eng' => 'Test',
        'venue_address_urdu' => 'Test',
        'status_page_note_eng' => 'Note',
        'status_page_note_urdu' => 'Note',
        'status' => 'Active',
    ], $overrides));
}

test('venue dates are interpreted in the admin-configured system_timezone, not the fixed app timezone', function () {
    Setting::set('system_timezone', 'Asia/Kolkata', 'string');

    $venue = makeTestVenue();

    // 1:00 AM IST is 12:30 AM Pakistan time (Asia/Karachi, config('app.timezone')) — the
    // raw DB value must reflect that shift so the absolute instant is correct.
    $raw = \Illuminate\Support\Facades\DB::table('venues')->where('id', $venue->id)->first();
    expect($raw->start_date)->toBe('2026-05-14 00:30:00');

    // Reading it back should round-trip to 1:00 AM in the configured system timezone.
    expect($venue->start_date->format('Y-m-d H:i:s'))->toBe('2026-05-14 01:00:00');
    expect($venue->start_date->timezoneName)->toBe('Asia/Kolkata');
});

test('venue dates are unaffected when system_timezone matches the app default', function () {
    // No system_timezone setting saved — falls back to config('app.timezone').
    $venue = makeTestVenue();

    $raw = \Illuminate\Support\Facades\DB::table('venues')->where('id', $venue->id)->first();
    expect($raw->start_date)->toBe('2026-05-14 01:00:00');
});
