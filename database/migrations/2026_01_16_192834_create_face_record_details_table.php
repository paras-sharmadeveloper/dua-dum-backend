<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('face_record_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('face_record_id')->nullable();
            $table->uuid('token_id');
            $table->string('status'); // 'Found' or 'Not Found'
            $table->string('image_path')->nullable(); // Path to uploaded image
            $table->text('face_encoding')->nullable(); // 128D face encoding as JSON
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('face_record_id')->references('id')->on('face_records')->onDelete('cascade');
            $table->foreign('token_id')->references('id')->on('tokens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('face_record_details');
    }
};
