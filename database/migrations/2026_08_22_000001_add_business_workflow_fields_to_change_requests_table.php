<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'owner_cr' => fn (Blueprint $table) => $table->string('owner_cr')->nullable(),
            'google_drive_url' => fn (Blueprint $table) => $table->string('google_drive_url')->nullable(),
            'solution_paper_url' => fn (Blueprint $table) => $table->string('solution_paper_url')->nullable(),
            'mindesk_analisis' => fn (Blueprint $table) => $table->decimal('mindesk_analisis', 8, 2)->nullable(),
            'mindesk_development' => fn (Blueprint $table) => $table->decimal('mindesk_development', 8, 2)->nullable(),
            'mindesk_testing' => fn (Blueprint $table) => $table->decimal('mindesk_testing', 8, 2)->nullable(),
            'pmh_approved_by' => fn (Blueprint $table) => $table->string('pmh_approved_by')->nullable(),
            'pmh_approved_at' => fn (Blueprint $table) => $table->timestamp('pmh_approved_at')->nullable(),
            'quotation_approved_client_at' => fn (Blueprint $table) => $table->timestamp('quotation_approved_client_at')->nullable(),
            'quotation_approved_tp_at' => fn (Blueprint $table) => $table->timestamp('quotation_approved_tp_at')->nullable(),
            'invoicing_status' => fn (Blueprint $table) => $table->string('invoicing_status')->default('pending'),
        ];

        foreach ($columns as $name => $definition) {
            if (! Schema::hasColumn('change_requests', $name)) {
                Schema::table('change_requests', $definition);
            }
        }

    }

    public function down(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn([
                'owner_cr', 'google_drive_url', 'solution_paper_url',
                'mindesk_analisis', 'mindesk_development', 'mindesk_testing',
                'pmh_approved_by', 'pmh_approved_at',
                'quotation_approved_client_at', 'quotation_approved_tp_at',
                'invoicing_status',
            ]);
        });
    }
};