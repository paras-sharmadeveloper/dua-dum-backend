<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VenueCategoryGroup;
use App\Models\VenueCategory;
use Illuminate\Support\Str;

class VenueCategorySeeder extends Seeder
{
    public function run()
    {
        // Create Category Groups
        VenueCategoryGroup::create([
            'id' => (string) Str::uuid(),
            'name' => 'General',
            'code' => 'NP'
        ]);

        VenueCategoryGroup::create([
            'id' => (string) Str::uuid(),
            'name' => 'Working Lady',
            'code' => 'WL'
        ]);

        // Create Categories
        VenueCategory::create([
            'id' => (string) Str::uuid(),
            'name' => 'DUA'
        ]);

        VenueCategory::create([
            'id' => (string) Str::uuid(),
            'name' => 'DUM'
        ]);
    }
}