<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Surrogate int64 key for the FAISS vector index (which needs
        // numeric IDs) - the real primary key stays the UUID `id`. Laravel's
        // ->autoIncrement() column modifier forces a PRIMARY KEY clause in
        // the MySQL grammar, which would collide with the existing UUID
        // primary key, so this is added via raw SQL instead: MySQL allows an
        // AUTO_INCREMENT column keyed by UNIQUE rather than PRIMARY KEY, as
        // long as it's the only auto-increment column on the table.
        DB::statement(
            'ALTER TABLE face_record_details ADD COLUMN faiss_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE AFTER id'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('face_record_details', function (Blueprint $table) {
            $table->dropColumn('faiss_id');
        });
    }
};
