<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $existingColumns = Schema::getColumnListing('change_requests');

        Schema::table('change_requests', function (Blueprint $table) use ($existingColumns) {
            if (! in_array('catatan_pengajuan', $existingColumns, true)) {
                $table->text('catatan_pengajuan')->nullable();
            }
            if (! in_array('solution_paper_required', $existingColumns, true)) {
                $table->boolean('solution_paper_required')->default(false);
                $table->text('solution_paper_note')->nullable();
            }
            if (! in_array('main_desk', $existingColumns, true)) {
                $table->string('main_desk')->nullable();
                $table->date('mulai_dikerjakan_pada')->nullable();
                $table->date('selesai_dikerjakan_pada')->nullable();
            }
            if (! in_array('harga_penawaran', $existingColumns, true)) {
                $table->decimal('harga_penawaran', 15, 2)->nullable();
                $table->text('potensi_penjualan')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn([
                'catatan_pengajuan', 'solution_paper_required', 'solution_paper_note',
                'main_desk', 'mulai_dikerjakan_pada', 'selesai_dikerjakan_pada',
                'harga_penawaran', 'potensi_penjualan',
            ]);
        });
    }
};