<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $sql = file_get_contents(database_path('seeders/sql/cities.sql'));

        // The dump is ~1.8MB; sending it as a single DB::unprepared() call
        // exceeds MySQL's default max_allowed_packet (1MB) and drops the
        // connection ("MySQL server has gone away"). Run it statement-by-statement
        // instead — every statement in this dump ends with ";" at end-of-line.
        $statements = array_filter(array_map('trim', explode(";\n", $sql)));

        foreach ($statements as $statement) {
            DB::unprepared($statement);
        }
    }
}
