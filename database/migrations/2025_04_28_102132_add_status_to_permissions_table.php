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
        Schema::table('permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions', 'status')) {
                Schema::table('permissions', function (Blueprint $table) {
                    $table->enum('status', ['Active', 'In Active'])
                        ->default('Active')
                        ->after('guard_name');
                });
            } else {
                // Column exists but has no default — fix it
                Schema::table('permissions', function (Blueprint $table) {
                    $table->enum('status', ['Active', 'In Active'])
                        ->default('Active')
                        ->after('guard_name')
                        ->change();
                });
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            if (Schema::hasColumn('permissions', 'status')) {
                Schema::table('permissions', function (Blueprint $table) {
                    $table->dropColumn('status');
                });
            }
        });
    }
};
