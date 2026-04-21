<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationGroup extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'country_id', 'cities', 'status', 'created_at', 'updated_at'];

    public $incrementing = false; // Critical for UUIDs
    protected $keyType = 'string'; // Critical for UUIDs
    protected $casts = [
        'id' => 'string', // Ensures UUID stays as string
    ];

    public static function getColumns()
    {
        return [
            ['name' => 'name', 'searchable' => true],
            ['name' => 'status', 'searchable' => true],
        ];
    }

    protected $table = 'location_groups';
}