<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueCategoryRange extends Model
{
    protected $table = 'venue_category_ranges';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'venue_id',
        'venue_category_group_id',
        'venue_category_id',
        'range_start',
        'range_end'
    ];
}
