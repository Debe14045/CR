<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            if (!Schema::hasColumn('pegawais', 'divisi')) {
                $table->string('divisi', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('pegawais', 'departemen')) {
                $table->string('departemen', 100)->nullable()->after('divisi');
            }
            $table->string('position')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $dropCols = array_filter(
                ['divisi', 'departemen'],
                fn (string $col) => Schema::hasColumn('pegawais', $col)
            );
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};

