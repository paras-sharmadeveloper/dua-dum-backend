<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueCategoryGroup extends Model
{
    protected $table = 'venue_category_groups';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'code'];
}
