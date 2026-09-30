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
            $table->string('kode_cr')->unique();
            $table->string('judul');
            $table->string('klien');
            $table->string('proyek_terkait');
            $table->text('deskripsi');
            $table->text('alasan')->nullable();
            $table->text('catatan_pengajuan')->nullable();
            $table->boolean('solution_paper_required')->default(false);
            $table->text('solution_paper_note')->nullable();
            $table->date('tanggal_pengajuan');

            $table->string('status')->default('diajukan');

            $table->string('estimasi_waktu')->nullable();
            $table->string('estimasi_resource')->nullable();
            $table->string('main_desk')->nullable();
            $table->date('mulai_dikerjakan_pada')->nullable();
            $table->date('selesai_dikerjakan_pada')->nullable();
            $table->decimal('harga_penawaran', 15, 2)->nullable();
            $table->text('potensi_penjualan')->nullable();
            $table->text('catatan_analisis')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->string('approved_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};
