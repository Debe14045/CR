<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('change_requests')->whereIn('status', ['revisi', 'development', 'running', 'hold'])->update(['status' => 'dikerjakan']);
        DB::table('change_requests')->where('status', 'done')->update(['status' => 'selesai']);
    }

    public function down(): void
    {
    }
};
