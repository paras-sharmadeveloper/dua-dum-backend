<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $sql = file_get_contents(database_path('seeders/sql/countries.sql'));
        DB::unprepared($sql);
    }
}
