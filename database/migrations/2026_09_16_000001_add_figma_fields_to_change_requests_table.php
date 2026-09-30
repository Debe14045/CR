<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('change_requests', 'pic_sales')) {
                $table->string('pic_sales')->nullable()->after('owner_cr');
            }
            if (! Schema::hasColumn('change_requests', 'bap_date')) {
                $table->date('bap_date')->nullable()->after('tanggal_pengajuan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            if (Schema::hasColumn('change_requests', 'pic_sales')) {
                $table->dropColumn('pic_sales');
            }
            if (Schema::hasColumn('change_requests', 'bap_date')) {
                $table->dropColumn('bap_date');
            }
        });
    }
};
