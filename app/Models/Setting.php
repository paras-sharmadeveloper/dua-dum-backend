<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = ['key', 'value', 'type'];

    // Return value cast to its declared type
    public function getCastedValue(): mixed
    {
        return $this->type === 'boolean'
            ? filter_var($this->value, FILTER_VALIDATE_BOOLEAN)
            : $this->value;
    }

    // Convenience: get a setting value (with optional default)
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);
        return $setting ? $setting->getCastedValue() : $default;
    }

    // Convenience: set a setting value
    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? 'true' : 'false') : (string) $value, 'type' => $type]
        );
    }
}
