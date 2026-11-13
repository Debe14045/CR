<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('notifications')
            ->where('target_role', 'pm')
            ->update(['target_role' => null]);
    }

    public function down(): void
    {
        // Biarkan target_role null agar notifikasi PM tetap spesifik per user_id proyek
    }
};

