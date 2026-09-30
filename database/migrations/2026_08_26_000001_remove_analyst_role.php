<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'analyst')->update(['role' => 'pm']);
    }

    public function down(): void
    {
    }
};
