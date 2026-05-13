<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // 'boolean' | 'string'
            $table->timestamps();
        });

        // Seed defaults
        DB::table('settings')->insert([
            [
                'key'        => 'book_dua_desktop_allowed',
                'value'      => 'false',
                'type'       => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'system_timezone',
                'value'      => 'Asia/Karachi',
                'type'       => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
