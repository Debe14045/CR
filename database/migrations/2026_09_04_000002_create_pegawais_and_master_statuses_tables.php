<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pegawais')) {
            Schema::create('pegawais', function (Blueprint $table) {
                $table->id();
                $table->string('nip')->unique();
                $table->string('name');
                $table->text('address')->nullable();
                $table->string('position'); // e.g. PM, Developer, Tester, Analyst
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('master_statuses')) {
            Schema::create('master_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('badge_color')->default('secondary');
                $table->integer('sort_order')->default(0);
                $table->text('description')->nullable();
                $table->timestamps();
            });

            // Seed initial statuses
            DB::table('master_statuses')->insert([
                ['code' => 'awaiting_pm', 'name' => 'Awaiting PM Verification', 'badge_color' => 'warning', 'sort_order' => 1, 'description' => 'CR baru diajukan oleh Client dan menunggu verifikasi PM.'],
                ['code' => 'awaiting_pmh', 'name' => 'Awaiting PMH Validation', 'badge_color' => 'info', 'sort_order' => 2, 'description' => 'CR telah diverifikasi PM dan menunggu validasi PM Head.'],
                ['code' => 'revision_needed', 'name' => 'Revision Needed', 'badge_color' => 'danger', 'sort_order' => 3, 'description' => 'CR ditolak oleh PM Head dan dikembalikan ke PM untuk revisi.'],
                ['code' => 'validated', 'name' => 'Validated', 'badge_color' => 'primary', 'sort_order' => 4, 'description' => 'CR telah divalidasi oleh PM Head dan siap dikerjakan.'],
                ['code' => 'analisa', 'name' => 'Analisa', 'badge_color' => 'info', 'sort_order' => 5, 'description' => 'Tahap analisis kebutuhan teknis dan proses.'],
                ['code' => 'development', 'name' => 'Development', 'badge_color' => 'primary', 'sort_order' => 6, 'description' => 'Tahap pengembangan dan koding.'],
                ['code' => 'sit', 'name' => 'SIT (System Integration Testing)', 'badge_color' => 'warning', 'sort_order' => 7, 'description' => 'Tahap pengujian integrasi sistem oleh tim internal.'],
                ['code' => 'uat', 'name' => 'UAT (User Acceptance Testing)', 'badge_color' => 'warning', 'sort_order' => 8, 'description' => 'Tahap pengujian penerimaan bersama pengguna/klien.'],
                ['code' => 'training', 'name' => 'Training', 'badge_color' => 'info', 'sort_order' => 9, 'description' => 'Tahap pelatihan penggunaan sistem kepada klien.'],
                ['code' => 'awaiting_golive_validation', 'name' => 'Awaiting Go-Live Validation', 'badge_color' => 'secondary', 'sort_order' => 10, 'description' => 'Pekerjaan selesai, BA diunggah PM, menunggu validasi PM Head.'],
                ['code' => 'golive', 'name' => 'Go-Live', 'badge_color' => 'success', 'sort_order' => 11, 'description' => 'Proyek resmi Go-Live dan validasi disetujui PM Head.'],
                ['code' => 'invoicing', 'name' => 'Invoicing', 'badge_color' => 'success', 'sort_order' => 12, 'description' => 'Masuk antrean Marketing / Keuangan untuk penerbitan Invoice.'],
                ['code' => 'ditolak', 'name' => 'Ditolak', 'badge_color' => 'danger', 'sort_order' => 13, 'description' => 'Pengajuan ditolak permanen.'],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('master_statuses');
        Schema::dropIfExists('pegawais');
    }
};
