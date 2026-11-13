<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('change_request_id')->constrained('change_requests')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->string('term_name'); // Contoh: Termin 1 (DP 30%)
            $table->decimal('percentage', 5, 2); // Contoh: 30.00
            $table->decimal('nominal', 15, 2);
            $table->text('job_description')->nullable();
            
            $table->date('invoice_date')->nullable();
            $table->date('planned_payment_date')->nullable();
            $table->date('actual_payment_date')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('pending');
            
            // Dokumen Keuangan Pendukung
            $table->string('gr_file')->nullable(); // Goods Receipt
            $table->string('gr_number')->nullable();
            $table->string('journal_file')->nullable();
            $table->string('journal_number')->nullable();
            $table->string('ba_file')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};