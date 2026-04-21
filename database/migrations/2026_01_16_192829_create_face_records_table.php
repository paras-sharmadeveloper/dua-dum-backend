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
        Schema::create('face_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('face_id')->unique(); // UUID from Python API
            $table->string('name');
            $table->integer('face_count')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('face_records');
    }
};
