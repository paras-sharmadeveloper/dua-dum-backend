<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueCategory extends Model
{
    protected $table = 'venue_categories';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name'];
}
