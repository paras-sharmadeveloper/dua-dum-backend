<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TokenCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'type_name',
        'start_range',
        'end_range',
        'venue_token_count',
        'current_token_number',
        'assigned_token_count',
        'created_at',
        'updated_at',
    ];

}