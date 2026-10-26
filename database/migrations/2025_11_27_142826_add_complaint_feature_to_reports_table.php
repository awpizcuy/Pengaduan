<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Menambah kolom untuk menyimpan alasan komplain
            $table->text('complaint_note')->nullable()->after('status_note');
        });

        // Mengubah tipe data kolom ENUM untuk menambahkan status 'complain'
        // Perhatikan: Sintaks ini spesifik untuk MySQL
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('pending', 'accepted', 'on_process', 'completed', 'rated', 'rejected', 'hold', 'complain') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('complaint_note');
        });

        // Mengembalikan definisi ENUM ke kondisi semula
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('pending', 'accepted', 'on_process', 'completed', 'rated', 'rejected', 'hold') NOT NULL DEFAULT 'pending'");
    }
};