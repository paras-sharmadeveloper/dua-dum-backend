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
        Schema::create('token_counter', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type_name');
            $table->integer('start_range');
            $table->integer('end_range');
            $table->integer('venue_token_count')->default(0);
            $table->integer('current_token_number')->default(0);
            $table->integer('assigned_token_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_counter');
    }
};