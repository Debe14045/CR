<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('change_requests', 'pm_id')) {
            Schema::table('change_requests', function (Blueprint $table) {
                $table->foreignId('pm_id')
                    ->nullable()
                    ->after('project_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasTable('project_pm')) {
            Schema::create('project_pm', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
                $table->foreignId('pm_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['project_id', 'pm_id']);
            });
        }

        if (!Schema::hasTable('change_request_pm')) {
            Schema::create('change_request_pm', function (Blueprint $table) {
                $table->id();
                $table->foreignId('change_request_id')->constrained('change_requests')->cascadeOnDelete();
                $table->foreignId('pm_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['change_request_id', 'pm_id']);
            });
        }

        $now = now();

        // Backfill project_pm dari projects.pm_id yang sudah ada
        $projects = DB::table('projects')->select('id', 'pm_id')->get();
        $projectPmMap = [];

        foreach ($projects as $project) {
            if ($project->pm_id) {
                DB::table('project_pm')->insertOrIgnore([
                    'project_id' => $project->id,
                    'pm_id' => $project->pm_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            $projectPmMap[$project->id] = $project->pm_id;
        }

        // Backfill change_requests.pm_id & change_request_pm agar selalu sinkron dengan proyeknya
        $projectPivots = DB::table('project_pm')->select('project_id', 'pm_id')->get()->groupBy('project_id');
        $changeRequests = DB::table('change_requests')->select('id', 'project_id')->get();

        foreach ($changeRequests as $cr) {
            $primaryPmId = $projectPmMap[$cr->project_id] ?? null;

            DB::table('change_requests')
                ->where('id', $cr->id)
                ->update(['pm_id' => $primaryPmId]);

            $pivotRows = $projectPivots->get($cr->project_id, collect());
            foreach ($pivotRows as $pivot) {
                DB::table('change_request_pm')->insertOrIgnore([
                    'change_request_id' => $cr->id,
                    'pm_id' => $pivot->pm_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('change_request_pm');
        Schema::dropIfExists('project_pm');

        if (Schema::hasColumn('change_requests', 'pm_id')) {
            Schema::table('change_requests', function (Blueprint $table) {
                $table->dropForeign(['pm_id']);
                $table->dropColumn('pm_id');
            });
        }
    }
};

