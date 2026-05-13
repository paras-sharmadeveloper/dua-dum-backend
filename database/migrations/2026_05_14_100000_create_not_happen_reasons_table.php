<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('not_happen_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->text('description_en');
            $table->text('description_ur');
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('not_happen_reasons');
    }
};
