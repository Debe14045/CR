<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cr')->unique(); // Contoh: CR-2026-001
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pembuat CR
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            
            $table->string('judul');
            $table->enum('prioritas', ['normal', 'urgent'])->default('normal');
            $table->text('deskripsi');
            $table->text('alasan')->nullable();
            $table->text('catatan_pengajuan')->nullable();
            
            $table->date('tanggal_pengajuan');
            $table->date('target_selesai')->nullable();
            
            // Upload Dokumen (Berbasis file PDF/DOC, bukan GDrive)
            $table->string('dokumen_cr')->nullable();
            $table->string('dokumen_lainnya')->nullable();
            
            // Kolom Analisis & Solution Paper oleh PM
            $table->boolean('solution_paper_required')->default(true);
            $table->string('solution_paper_file')->nullable();
            $table->integer('mindesk_analisis')->default(0);
            $table->integer('mindesk_development')->default(0);
            $table->integer('mindesk_testing')->default(0);
            $table->text('catatan_analisis')->nullable();
            
            // Aspek Komersial (Presales)
            $table->decimal('harga_penawaran', 15, 2)->default(0);
            $table->decimal('biaya_pengerjaan', 15, 2)->default(0);
            $table->string('quotation_file')->nullable();
            
            // Approval PM Head
            $table->timestamp('pmh_approved_at')->nullable();
            $table->foreignId('pmh_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('catatan_approval')->nullable();
            $table->text('catatan_revisi')->nullable();
            $table->text('reject_reason')->nullable();
            
            // Status Alur Utama & Teknis
            $table->string('status')->default('draft'); // draft, diajukan, analisa, development, sit, uat, golive, invoicing, dll.
            
            // Berita Acara (Go-Live)
            $table->string('berita_acara_file')->nullable();
            $table->string('berita_acara_no')->nullable();
            $table->date('berita_acara_date')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};