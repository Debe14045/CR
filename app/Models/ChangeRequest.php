<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ChangeRequest extends Model
{
    use HasFactory;

    public const RATE_PER_MINDESK = 1500000; // Standar IDR 1.500.000 per mandays

    protected $table = 'change_requests';

    protected $fillable = [
        'kode_cr',
        'user_id',
        'client_id',
        'project_id',
        'pm_id',
        'judul',
        'prioritas',
        'deskripsi',
        'alasan',
        'catatan_pengajuan',
        'tanggal_pengajuan',
        'target_selesai',
        'dokumen_cr',          // Path file upload PDF dokumen CR utama
        'dokumen_lainnya',     // Path file upload dokumen pendukung opsional
        'solution_paper_required',
        'solution_paper_file', // Path file upload PDF Solution Paper dari PM
        'mindesk_analisis',
        'mindesk_development',
        'mindesk_testing',
        'catatan_analisis',
        'harga_penawaran',
        'biaya_pengerjaan',
        'quotation_file',
        'pmh_approved_at',
        'pmh_approved_by',
        'catatan_approval',
        'catatan_revisi',
        'reject_reason',
        'status',
        'berita_acara_file',
        'berita_acara_no',
        'berita_acara_date',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'target_selesai' => 'date',
        'berita_acara_date' => 'date',
        'pmh_approved_at' => 'datetime',
        'solution_paper_required' => 'boolean',
        'harga_penawaran' => 'decimal:2',
        'biaya_pengerjaan' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (ChangeRequest $cr) {
            if ($cr->project_id) {
                $cr->pm_id = Project::whereKey($cr->project_id)->value('pm_id');
            } else {
                $cr->pm_id = null;
            }
        });

        static::saved(function (ChangeRequest $cr) {
            if ($cr->wasRecentlyCreated || $cr->wasChanged('project_id')) {
                $cr->syncPmsFromProject();
            }
        });
    }

    // ── RELASI ──
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * PM utama CR (selalu disinkronkan dengan projects.pm_id).
     */
    public function pm()
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    /**
     * Seluruh PM (maksimal 2 PM) yang bertanggung jawab atas CR ini, sinkron dengan project_pm.
     */
    public function pms()
    {
        return $this->belongsToMany(User::class, 'change_request_pm', 'change_request_id', 'pm_id')->withTimestamps();
    }

    /**
     * Ambil koleksi PM yang bertanggung jawab pada CR ini (dengan fallback ke PM proyek).
     */
    public function assignedPms(): Collection
    {
        $this->loadMissing(['pms.pegawai', 'pm.pegawai', 'project.pms.pegawai', 'project.pm.pegawai']);

        if ($this->pms->isNotEmpty()) {
            $primaryId = $this->pm_id ?: $this->project?->pm_id;
            if ($primaryId && $this->pms->contains('id', $primaryId)) {
                return $this->pms->sortBy(fn ($u) => (int) $u->id === (int) $primaryId ? 0 : 1)->values();
            }

            return $this->pms->values();
        }

        if ($this->pm) {
            return collect([$this->pm]);
        }

        if ($this->project) {
            return $this->project->assignedPms();
        }

        return collect();
    }

    /**
     * Sinkronkan pm_id dan pivot change_request_pm dari proyek induknya.
     */
    public function syncPmsFromProject(?Project $project = null): void
    {
        $project = $project ?: ($this->project_id ? Project::with('pms')->find($this->project_id) : null);

        if (!$project) {
            if ($this->pm_id !== null) {
                $this->pm_id = null;
                $this->saveQuietly();
            }
            $this->pms()->sync([]);
            return;
        }

        $pmIds = $project->pms->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (empty($pmIds) && $project->pm_id) {
            $pmIds = [(int) $project->pm_id];
        }
        $pmIds = array_slice(array_values(array_unique($pmIds)), 0, 2);
        $primaryPmId = $project->pm_id ? (int) $project->pm_id : ($pmIds[0] ?? null);

        if ((int) $this->pm_id !== (int) $primaryPmId || ($this->pm_id === null) !== ($primaryPmId === null)) {
            $this->pm_id = $primaryPmId;
            $this->saveQuietly();
        }

        $this->pms()->sync($pmIds);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'change_request_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'change_request_id');
    }

    public function pmhApprover()
    {
        return $this->belongsTo(User::class, 'pmh_approved_by');
    }

    // ── HELPER METHODS ──
    public function statusBadgeConfig(): array
    {
        return match (strtolower($this->status ?? '')) {
            'draft' => [
                'text' => 'Draft',
                'bg' => '#F1F5F9',
                'color' => '#64748B',
            ],
            'diajukan' => [
                'text' => 'Diajukan',
                'bg' => '#FEF3C7',
                'color' => '#D97706',
            ],
            'awaiting_pm', 'analisa', 'dianalisis', 'analysis' => [
                'text' => 'Review PM',
                'bg' => '#DBEAFE',
                'color' => '#2563EB',
            ],
            'awaiting_pmh', 'review_pmh' => [
                'text' => 'Approval PM Head',
                'bg' => '#E0E7FF',
                'color' => '#4F46E5',
            ],
            'revision_needed' => [
                'text' => 'Revisi PM Head',
                'bg' => '#FFEDD5',
                'color' => '#C2410C',
            ],
            'validated', 'disetujui' => [
                'text' => (session('active_role') === 'presales' || (auth()->check() && auth()->user()->role === 'presales')) ? 'Menunggu Quotation' : 'Disetujui',
                'bg' => (session('active_role') === 'presales' || (auth()->check() && auth()->user()->role === 'presales')) ? '#FEF9C3' : '#DCFCE7',
                'color' => (session('active_role') === 'presales' || (auth()->check() && auth()->user()->role === 'presales')) ? '#CA8A04' : '#16A34A',
            ],
            'quotation' => [
                'text' => 'Quotation',
                'bg' => '#FEF9C3',
                'color' => '#CA8A04',
            ],
            'development', 'dikerjakan', 'develop' => [
                'text' => 'Develop',
                'bg' => '#EDE9FE',
                'color' => '#7C3AED',
            ],
            'sit' => [
                'text' => 'SIT',
                'bg' => '#FCE7F3',
                'color' => '#DB2777',
            ],
            'uat' => [
                'text' => 'UAT',
                'bg' => '#FCE7F3',
                'color' => '#DB2777',
            ],
            'training' => [
                'text' => 'Training',
                'bg' => '#CCFBF1',
                'color' => '#0F766E',
            ],
            'golive', 'go-live', 'go_live' => [
                'text' => 'Go-Live',
                'bg' => '#D1FAE5',
                'color' => '#059669',
            ],
            'invoicing' => [
                'text' => 'Invoicing',
                'bg' => '#CFFAFE',
                'color' => '#0891B2',
            ],
            'selesai' => [
                'text' => 'Selesai',
                'bg' => '#D1FAE5',
                'color' => '#059669',
            ],
            'ditolak' => [
                'text' => 'Ditolak',
                'bg' => '#FEE2E2',
                'color' => '#DC2626',
            ],
            default => [
                'text' => ucfirst($this->status ?? 'Diajukan'),
                'bg' => '#FEF3C7',
                'color' => '#D97706',
            ],
        };
    }

    public function statusLabel(): string
    {
        return match (strtolower($this->status)) {
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'awaiting_pm' => 'Menunggu Review PM',
            'analisa', 'dianalisis' => 'Analisa',
            'awaiting_pmh' => 'Menunggu Approval PM Head',
            'validated', 'disetujui' => 'Disetujui',
            'quotation' => 'Quotation',
            'development', 'dikerjakan' => 'Sedang Dikerjakan (Development)',
            'sit' => 'SIT (System Integration Testing)',
            'uat' => 'UAT (User Acceptance Testing)',
            'training' => 'Training',
            'golive', 'selesai' => 'Selesai Go-Live',
            'invoicing' => 'Invoicing',
            'ditolak' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function isPmhApproved(): bool
    {
        $status = strtolower($this->status ?? '');
        if (in_array($status, ['draft', 'diajukan', 'awaiting_pm', 'analisa', 'dianalisis', 'awaiting_pmh', 'revision_needed', 'ditolak'])) {
            return false;
        }

        return !is_null($this->pmh_approved_at) || in_array($status, ['validated', 'disetujui', 'quotation', 'development', 'dikerjakan', 'sit', 'uat', 'training', 'golive', 'selesai', 'invoicing']);
    }

    public function canBeQuoted(): bool
    {
        return $this->isPmhApproved();
    }

    public function isReadyForInvoicing(): bool
    {
        return in_array(strtolower((string)$this->status), ['golive', 'go-live', 'go_live', 'selesai', 'invoicing']);
    }

    public function saranHargaPenawaran(): float
    {
        $totalMandays = (int)($this->mindesk_analisis ?? 0) + (int)($this->mindesk_development ?? 0) + (int)($this->mindesk_testing ?? 0);
        return (float)($totalMandays * self::RATE_PER_MINDESK);
    }
}