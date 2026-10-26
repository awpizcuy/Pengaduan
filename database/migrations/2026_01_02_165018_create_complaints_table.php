<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete(); // admin_gedung
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete(); // kepala_it

            // nomor komplain
            $table->unsignedInteger('sequence'); // 1,2,3 per report
            $table->string('complaint_no')->unique(); // contoh: CMP-000012-001

            $table->text('message'); // isi komplain admin
            $table->enum('status', ['submitted', 'approved', 'rejected'])->default('submitted');
            $table->text('decision_note')->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->timestamps();

            $table->unique(['report_id', 'sequence']); // pastikan urutan unik per report
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
