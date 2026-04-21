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
            if (!Schema::hasColumn('tokens', 'user_name')) {
                $table->string('user_name')->after('service_type');
            }
            if (!Schema::hasColumn('tokens', 'city')) {
                $table->string('city')->after('user_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            if (Schema::hasColumn('tokens', 'user_name')) {
                $table->dropColumn('user_name');
            }
            if (Schema::hasColumn('tokens', 'city')) {
                $table->dropColumn('city');
            }
        });
    }
};
