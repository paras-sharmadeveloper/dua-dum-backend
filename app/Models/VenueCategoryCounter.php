<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueCategoryCounter extends Model
{
    protected $table = 'venue_category_counters';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'venue_id',
        'venue_category_group_id',
        'venue_category_id',
        'last_issued_no',
        'requested_token_count',
        'assigned_token_count'
    ];

    public function venueCategoryGroup()
    {
        return $this->belongsTo(VenueCategoryGroup::class, 'venue_category_group_id');
    }

    public function venueCategory()
    {
        return $this->belongsTo(VenueCategory::class, 'venue_category_id');
    }
}
