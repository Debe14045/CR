<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (! Schema::hasColumn('clients', 'nickname')) {
                $table->string('nickname')->nullable()->after('name');
            }
            if (! Schema::hasColumn('clients', 'address')) {
                $table->text('address')->nullable()->after('nickname');
            }
            if (! Schema::hasColumn('clients', 'phone')) {
                $table->string('phone')->nullable()->after('address');
            }

            // PIC Marketing
            if (! Schema::hasColumn('clients', 'pic_marketing_name')) {
                $table->string('pic_marketing_name')->nullable();
                $table->string('pic_marketing_phone')->nullable();
                $table->string('pic_marketing_email')->nullable();
                $table->text('pic_marketing_desc')->nullable();
            }

            // PIC IT / Programmer
            if (! Schema::hasColumn('clients', 'pic_it_name')) {
                $table->string('pic_it_name')->nullable();
                $table->string('pic_it_phone')->nullable();
                $table->string('pic_it_email')->nullable();
                $table->text('pic_it_desc')->nullable();
            }

            // PIC Procurement
            if (! Schema::hasColumn('clients', 'pic_procurement_name')) {
                $table->string('pic_procurement_name')->nullable();
                $table->string('pic_procurement_phone')->nullable();
                $table->string('pic_procurement_email')->nullable();
                $table->text('pic_procurement_desc')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'nickname',
                'address',
                'phone',
                'pic_marketing_name',
                'pic_marketing_phone',
                'pic_marketing_email',
                'pic_marketing_desc',
                'pic_it_name',
                'pic_it_phone',
                'pic_it_email',
                'pic_it_desc',
                'pic_procurement_name',
                'pic_procurement_phone',
                'pic_procurement_email',
                'pic_procurement_desc',
            ]);
        });
    }
};
