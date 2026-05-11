<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Location;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'venue_name',
        'venue_code',
        'user_id',
        'start_date',
        'end_date',
        'location_group_id',
        'general_dua_token',
        'general_dum_token',
        'working_lady_dua_token',
        'venue_address_eng',
        'venue_address_urdu',
        'status_page_note_eng',
        'status_page_note_urdu',
        'dua_reason',
        'dum_reason',
        'status'
    ];

    public $incrementing = false; // Critical for UUIDs
    protected $keyType = 'string'; // Critical for UUIDs
    protected $casts = [
        'id' => 'string', // Ensures UUID stays as string
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            if (empty($model->venue_code)) {
                // Find the highest existing numeric suffix (e.g. V12 → 12).
                // Using MAX on the cast value is correct even if records were
                // deleted or created out of chronological order.
                $max = static::whereRaw("venue_code REGEXP '^V[0-9]+$'")
                    ->selectRaw('MAX(CAST(SUBSTRING(venue_code, 2) AS UNSIGNED)) as max_num')
                    ->value('max_num');

                $model->venue_code = 'V' . ((int) $max + 1);
            }
        });
    }

    /**
     * Get the countries that restrict access to this venue.
     */
    public function countries()
    {
        return $this->morphToMany(Country::class, 'locationable');
    }

    /**
     * Get the cities that restrict access to this venue.
     */
    public function cities()
    {
        return $this->morphToMany(City::class, 'locationable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function locationGroup()
    {
        return $this->belongsTo(LocationGroup::class);
    }

    /**
     * Check if a user has access to this venue based on their location groups.
     *
     * @param User $user
     * @return bool
     */
    public function userHasAccess(User $user): bool
    {
        // If no geo restriction, everyone has access
        if ($this->geo_restriction === 'none') {
            return true;
        }

        $countryIds = $this->countries->pluck('id')->toArray();
        $cityIds = $this->cities->pluck('id')->toArray();

        // Check if any of the user's location groups contain allowed countries or cities
        foreach ($user->locationGroups as $locationGroup) {
            // For country restriction
            if ($this->geo_restriction === 'country' || $this->geo_restriction === 'both') {
                $groupCountryIds = $locationGroup->countries->pluck('id')->toArray();
                if (count(array_intersect($countryIds, $groupCountryIds)) > 0) {
                    // If checking only countries or if city check isn't required
                    if ($this->geo_restriction === 'country') {
                        return true;
                    }
                } else if ($this->geo_restriction === 'both') {
                    // If country check fails in 'both' mode, continue to next group
                    continue;
                }
            }

            // For city restriction
            if ($this->geo_restriction === 'city' || $this->geo_restriction === 'both') {
                $groupCityIds = $locationGroup->cities->pluck('id')->toArray();
                if (count(array_intersect($cityIds, $groupCityIds)) > 0) {
                    return true;
                }
            }
        }

        return false;
    }
}