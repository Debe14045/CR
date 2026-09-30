<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->text('catatan_revisi')->nullable()->after('catatan_approval');
            $table->string('lampiran_revisi')->nullable()->after('catatan_revisi');
            $table->timestamp('direvisi_pada')->nullable()->after('lampiran_revisi');
        });
    }

    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn(['catatan_revisi', 'lampiran_revisi', 'direvisi_pada']);
        });
    }
};