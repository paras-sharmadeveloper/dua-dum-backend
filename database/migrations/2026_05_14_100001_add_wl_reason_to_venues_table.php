<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->unsignedBigInteger('dua_reason_id')->nullable()->after('dua_reason');
            $table->unsignedBigInteger('dum_reason_id')->nullable()->after('dum_reason');
            $table->unsignedBigInteger('wl_reason_id')->nullable()->after('dum_reason_id');
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['dua_reason_id', 'dum_reason_id', 'wl_reason_id']);
        });
    }
};
