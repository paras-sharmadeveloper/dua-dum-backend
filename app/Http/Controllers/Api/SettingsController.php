<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private const DEFAULTS = [
        'book_dua_desktop_allowed' => false,
        'system_timezone'          => 'Asia/Karachi',
    ];

    private const BOOL_KEYS = ['book_dua_desktop_allowed'];

    private const TIMEZONE_OPTIONS = [
        'Asia/Karachi',
        'Asia/Dubai',
        'Europe/London',
        'Asia/Kolkata',
    ];

    // Public — no auth required
    public function publicSettings(): JsonResponse
    {
        $settings = [];
        foreach (self::DEFAULTS as $key => $default) {
            $settings[$key] = Setting::get($key, $default);
        }
        return response()->json($settings);
    }

    // Protected — admin only
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string|in:' . implode(',', array_keys(self::DEFAULTS)),
        ]);

        $key = $request->key;

        if (in_array($key, self::BOOL_KEYS)) {
            $request->validate(['value' => 'required|boolean']);
            $value = (bool) $request->value;
            Setting::set($key, $value, 'boolean');
        } else {
            $request->validate(['value' => 'required|string']);
            if ($key === 'system_timezone') {
                $request->validate(['value' => 'in:' . implode(',', self::TIMEZONE_OPTIONS)]);
            }
            $value = $request->value;
            Setting::set($key, $value, 'string');
        }

        return response()->json(['success' => true, $key => $value]);
    }
}
