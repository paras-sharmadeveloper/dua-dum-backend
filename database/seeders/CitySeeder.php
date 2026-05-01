<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $sql = file_get_contents(database_path('seeders/sql/cities.sql'));

        DB::unprepared($sql);
    }
}
