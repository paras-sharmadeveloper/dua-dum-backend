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
        Schema::table('tokens', function (Blueprint $table) {
            $table->string('user_type')->after('token_number'); // normal_person, working_lady
            $table->string('service_type')->after('user_type'); // dua, dum
            $table->string('phone_number')->after('service_type');
            $table->string('user_image_path')->nullable()->after('phone_number');
            $table->string('qr_code_path')->nullable()->after('user_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            $table->dropColumn([
                'user_type',
                'service_type',
                'phone_number',
                'user_image_path',
                'qr_code_path'
            ]);
        });
    }
};
