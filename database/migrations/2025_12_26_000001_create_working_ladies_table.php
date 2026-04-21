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
        Schema::create('working_ladies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('designation');
            $table->string('company_name');
            $table->string('place_of_work');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->text('remarks')->nullable();
            $table->string('case_type'); // normal or critical
            $table->string('status')->nullable(); // Approved, Disapproved, or empty
            $table->string('qr_code_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_ladies');
    }
};
