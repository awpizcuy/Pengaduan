<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // untuk query history: where user_id + status + order/tanggal
            $table->index(['user_id', 'status', 'created_at'], 'reports_user_status_created_idx');

            // untuk filter dashboard/history: status/kategori/tanggal_pengajuan
            $table->index(['status', 'kategori', 'tanggal_pengajuan'], 'reports_status_kategori_tanggal_idx');

            // untuk teknisi (task list / filter)
            $table->index(['assigned_technician_id', 'status'], 'reports_technician_status_idx');

            // untuk komplain tracking
            $table->index(['status', 'last_complaint_at'], 'reports_status_lastcomplaint_idx');
        });

        Schema::table('complaints', function (Blueprint $table) {
            // untuk latestComplaint: where report_id order by sequence desc
            $table->index(['report_id', 'sequence'], 'complaints_report_sequence_idx');

            // untuk ambil komplain aktif: where report_id + status
            $table->index(['report_id', 'status'], 'complaints_report_status_idx');

            // untuk laporan keputusan (opsional)
            $table->index(['status', 'decided_at'], 'complaints_status_decided_idx');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_user_status_created_idx');
            $table->dropIndex('reports_status_kategori_tanggal_idx');
            $table->dropIndex('reports_technician_status_idx');
            $table->dropIndex('reports_status_lastcomplaint_idx');
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropIndex('complaints_report_sequence_idx');
            $table->dropIndex('complaints_report_status_idx');
            $table->dropIndex('complaints_status_decided_idx');
        });
    }
};
