<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('change_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('change_requests', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('user_id')->constrained('clients')->nullOnDelete();
            }
            if (! Schema::hasColumn('change_requests', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('alasan');
            }
            if (! Schema::hasColumn('change_requests', 'target_start_date')) {
                $table->date('target_start_date')->nullable()->after('target_selesai');
            }
            if (! Schema::hasColumn('change_requests', 'actual_completion_date')) {
                $table->date('actual_completion_date')->nullable()->after('target_start_date');
            }
            if (! Schema::hasColumn('change_requests', 'berita_acara_file')) {
                $table->string('berita_acara_file')->nullable()->after('lampiran_pengajuan');
            }
            if (! Schema::hasColumn('change_requests', 'berita_acara_date')) {
                $table->date('berita_acara_date')->nullable()->after('berita_acara_file');
            }
            if (! Schema::hasColumn('change_requests', 'berita_acara_no')) {
                $table->string('berita_acara_no')->nullable()->after('berita_acara_date');
            }
            if (! Schema::hasColumn('change_requests', 'golive_validated_at')) {
                $table->timestamp('golive_validated_at')->nullable()->after('pmh_approved_at');
            }
            if (! Schema::hasColumn('change_requests', 'golive_validated_by')) {
                $table->string('golive_validated_by')->nullable()->after('golive_validated_at');
            }
        });

        if (! Schema::hasTable('solution_papers')) {
            Schema::create('solution_papers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('change_request_id')->constrained('change_requests')->cascadeOnDelete();
                $table->string('status')->default('Draft'); // Draft, Completed, Needs Revision
                $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
                $table->string('creator_name')->nullable();
                $table->date('target_date_start')->nullable();
                $table->date('target_date_end')->nullable();
                $table->date('actual_completion_date')->nullable();
                $table->string('solution_paper_file')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('solution_papers');

        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn([
                'client_id',
                'reject_reason',
                'target_start_date',
                'actual_completion_date',
                'berita_acara_file',
                'berita_acara_date',
                'berita_acara_no',
                'golive_validated_at',
                'golive_validated_by',
            ]);
        });
    }
};
