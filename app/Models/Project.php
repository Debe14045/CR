<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'client_id',
        'pm_id',
        'kode_proyek',
        'nama_proyek',
        'deskripsi',
    ];

    public bool $skipPmAutoSync = false;

    protected static function booted(): void
    {
        static::saved(function (Project $project) {
            if ($project->skipPmAutoSync) {
                return;
            }

            if ($project->wasRecentlyCreated || $project->wasChanged('pm_id')) {
                $currentIds = $project->pms()->pluck('users.id')->map(fn ($id) => (int) $id)->all();
                $oldPmId = $project->wasChanged('pm_id') ? (int) ($project->getOriginal('pm_id') ?? 0) : 0;

                if ($oldPmId > 0) {
                    $currentIds = array_values(array_filter($currentIds, fn ($id) => $id !== $oldPmId));
                }

                if ($project->pm_id && !in_array((int) $project->pm_id, $currentIds, true)) {
                    array_unshift($currentIds, (int) $project->pm_id);
                }

                $currentIds = array_slice(array_values(array_unique($currentIds)), 0, 2);
                $project->pms()->sync($currentIds);
                $project->syncChangeRequestsPms($currentIds, $project->pm_id ? (int) $project->pm_id : ($currentIds[0] ?? null));
            }
        });
    }

    // ── RELASI ──
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * PM utama (Project Manager 1) yang bertanggung jawab atas proyek ini.
     */
    public function pm()
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    /**
     * Seluruh PM (maksimal 2 PM) yang bertanggung jawab atas proyek ini.
     */
    public function pms()
    {
        return $this->belongsToMany(User::class, 'project_pm', 'project_id', 'pm_id')->withTimestamps();
    }

    public function changeRequests()
    {
        return $this->hasMany(ChangeRequest::class, 'project_id');
    }

    /**
     * Ambil koleksi PM yang bertanggung jawab (PM utama di urutan pertama), dengan fallback ke relasi pm tunggal.
     */
    public function assignedPms(): Collection
    {
        $this->loadMissing(['pms.pegawai', 'pm.pegawai']);
        $pms = $this->pms;

        if ($pms->isEmpty() && $this->pm) {
            return collect([$this->pm]);
        }

        if ($this->pm_id && $pms->contains('id', $this->pm_id)) {
            return $pms->sortBy(fn ($u) => (int) $u->id === (int) $this->pm_id ? 0 : 1)->values();
        }

        return $pms->values();
    }

    /**
     * Sinkronkan 1-2 PM pada proyek, lalu propagasikan ke seluruh CR milik proyek ini.
     */
    public function syncPms(array $pmIds): void
    {
        $normalized = collect($pmIds)
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->take(2)
            ->values()
            ->all();

        $primaryPmId = $normalized[0] ?? null;

        $this->skipPmAutoSync = true;
        try {
            if ((int) $this->pm_id !== (int) $primaryPmId || ($this->pm_id === null) !== ($primaryPmId === null)) {
                $this->pm_id = $primaryPmId;
                $this->saveQuietly();
            }

            $this->pms()->sync($normalized);
            $this->syncChangeRequestsPms($normalized, $primaryPmId);
        } finally {
            $this->skipPmAutoSync = false;
        }
    }

    /**
     * Pastikan kolom pm_id dan pivot change_request_pm pada semua CR proyek selalu sama dengan proyeknya.
     */
    public function syncChangeRequestsPms(?array $pmIds = null, ?int $primaryPmId = null): void
    {
        if ($pmIds === null) {
            $pmIds = $this->pms()->pluck('users.id')->map(fn ($id) => (int) $id)->all();
            if (empty($pmIds) && $this->pm_id) {
                $pmIds = [(int) $this->pm_id];
            }
            $pmIds = array_slice(array_values(array_unique($pmIds)), 0, 2);
            $primaryPmId = $this->pm_id ? (int) $this->pm_id : ($pmIds[0] ?? null);
        }

        ChangeRequest::where('project_id', $this->id)->update(['pm_id' => $primaryPmId]);

        $crIds = ChangeRequest::where('project_id', $this->id)->pluck('id');
        if ($crIds->isEmpty()) {
            return;
        }

        DB::table('change_request_pm')->whereIn('change_request_id', $crIds)->delete();

        if (!empty($pmIds)) {
            $now = now();
            $rows = [];
            foreach ($crIds as $crId) {
                foreach ($pmIds as $pmId) {
                    $rows[] = [
                        'change_request_id' => $crId,
                        'pm_id' => $pmId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            DB::table('change_request_pm')->insert($rows);
        }
    }
}
