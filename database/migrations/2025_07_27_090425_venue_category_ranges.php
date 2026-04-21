<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('venue_category_ranges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('venue_id');
            $table->string('venue_category_group_id');
            $table->string('venue_category_id');
            $table->integer('range_start');
            $table->integer('range_end');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
