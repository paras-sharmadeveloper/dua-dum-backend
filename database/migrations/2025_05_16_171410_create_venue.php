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
        Schema::create('venues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('venue_name');
            $table->string('venue_code');
            $table->integer('user_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('location_group_id');
            $table->integer('general_dua_token');
            $table->integer('general_dum_token');
            $table->integer('working_lady_dua_token');
            $table->string('venue_address_eng');
            $table->string('venue_address_urdu');
            $table->string('status_page_note_eng');
            $table->string('status_page_note_urdu');
            $table->string('dua_reason')->nullable();
            $table->string('dum_reason')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};