<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cr_invoices')) {
            Schema::create('cr_invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('change_request_id')->constrained('change_requests')->cascadeOnDelete();
                
                // 3 Inputs
                // Input 1: Nomor Invoice & Tanggal Invoice
                $table->string('invoice_number')->nullable();
                $table->date('invoice_date')->nullable();

                // Input 2: Nominal Pekerjaan & Deskripsi Pekerjaan
                $table->decimal('nominal', 15, 2)->default(0);
                $table->text('job_description')->nullable();

                // Input 3: Plan Tanggal Bayar (Rencana) & Tanggal Bayar Aktual
                $table->date('planned_payment_date')->nullable();
                $table->date('actual_payment_date')->nullable();

                // Optional Term of Payment
                $table->string('term_name')->nullable(); // e.g. DP 30%, Pelunasan 70%
                $table->decimal('percentage', 5, 2)->nullable();

                // 3-4 File Uploads & Metadata
                // 1. Dokumen GR (Good Receipt): Nomor GR & File Bukti GR
                $table->string('gr_number')->nullable();
                $table->string('gr_file')->nullable();

                // 2. Dokumen J (Jurnal): Nomor J & File Bukti J
                $table->string('journal_number')->nullable();
                $table->string('journal_file')->nullable();

                // 3. Dokumen BA (Berita Acara): File BA & Tanggal BA
                $table->date('ba_date')->nullable();
                $table->string('ba_file')->nullable();

                // 4. Dokumen Quotation: Dokumen Penawaran & Dokumen Tertandatangan
                $table->string('quotation_file')->nullable();
                $table->string('signed_quotation_file')->nullable();

                $table->enum('payment_status', ['pending', 'paid'])->default('pending');
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cr_invoices');
    }
};
