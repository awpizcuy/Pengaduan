<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Mengubah tipe kolom untuk bisa menyimpan waktu
            $table->dateTime('tanggal_pengajuan')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Mengembalikan ke tipe data semula
            $table->date('tanggal_pengajuan')->change();
        });
    }
};