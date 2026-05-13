<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotHappenReason extends Model
{
    protected $fillable = ['label', 'description_en', 'description_ur', 'status'];
}
