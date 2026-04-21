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
            if (!Schema::hasColumn('tokens', 'token_code')) {
                $table->string('token_code')->nullable()->after('token_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            if (Schema::hasColumn('tokens', 'token_code')) {
                $table->dropColumn('token_code');
            }
        });
    }
};
