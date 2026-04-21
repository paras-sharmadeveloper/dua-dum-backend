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
        Schema::table('venue_category_counters', function (Blueprint $table) {
            if (!Schema::hasColumn('venue_category_counters', 'requested_token_count')) {
                $table->integer('requested_token_count')->default(0)->after('last_issued_no');
            }
            if (!Schema::hasColumn('venue_category_counters', 'assigned_token_count')) {
                $table->integer('assigned_token_count')->default(0)->after('requested_token_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venue_category_counters', function (Blueprint $table) {
            if (Schema::hasColumn('venue_category_counters', 'requested_token_count')) {
                $table->dropColumn('requested_token_count');
            }
            if (Schema::hasColumn('venue_category_counters', 'assigned_token_count')) {
                $table->dropColumn('assigned_token_count');
            }
        });
    }
};
