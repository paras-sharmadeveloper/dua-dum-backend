<?php

namespace App\Casts;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Stores datetimes in the app's storage timezone (APP_TIMEZONE) for a
 * consistent DB representation, but reads/writes them from the caller's
 * perspective in the admin-configured `system_timezone` setting instead of
 * the fixed APP_TIMEZONE. Lets an admin-editable timezone actually govern
 * when a schedule (e.g. a venue's booking window) opens/closes, without
 * changing how the rest of the app interprets timestamps.
 */
class SystemTimezoneDateTime implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?Carbon
    {
        if ($value === null) {
            return null;
        }

        return Carbon::parse($value, config('app.timezone'))->setTimezone($this->systemTimezone());
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return Carbon::parse($value, $this->systemTimezone())
            ->setTimezone(config('app.timezone'))
            ->format('Y-m-d H:i:s');
    }

    protected function systemTimezone(): string
    {
        return Setting::get('system_timezone', config('app.timezone'));
    }
}
