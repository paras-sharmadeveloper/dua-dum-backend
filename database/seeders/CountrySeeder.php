<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('countries')->truncate();

        $sql = file_get_contents(database_path('seeders/sql/countries.sql'));

        preg_match_all('/^INSERT INTO.*?;$/ms', $sql, $matches);

        foreach ($matches[0] as $insert) {
            DB::unprepared($insert);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
