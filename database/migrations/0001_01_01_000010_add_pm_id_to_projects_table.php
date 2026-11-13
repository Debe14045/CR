<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'pm_id')) {
                $table->foreignId('pm_id')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete();
            }
        });

        // Tetapkan PM default untuk proyek lama yang belum memiliki PM penanggung jawab
        $defaultPmId = DB::table('users')
            ->where('role', 'pm')
            ->orderBy('id')
            ->value('id');

        if ($defaultPmId) {
            DB::table('projects')
                ->whereNull('pm_id')
                ->update(['pm_id' => $defaultPmId]);
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'pm_id')) {
                $table->dropForeign(['pm_id']);
                $table->dropColumn('pm_id');
            }
        });
    }
};

