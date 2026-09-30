<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_cr',
        'judul',
        'owner_cr',
        'pic_sales',
        'client_id',
        'klien',
        'proyek_terkait',
        'deskripsi',
        'alasan',
        'reject_reason',
        'catatan_pengajuan',
        'pesan_client',
        'google_drive_url',
        'lampiran_pengajuan',
        'solution_paper_required',
        'solution_paper_note',
        'solution_paper_url',
        'tanggal_pengajuan',
        'bap_date',
        'status',
        'prioritas',
        'estimasi_waktu',
        'mindesk_analisis',
        'mindesk_development',
        'mindesk_testing',
        'target_selesai',
        'target_start_date',
        'actual_completion_date',
        'estimasi_resource',
        'biaya_pengerjaan',
        'harga_penawaran',
        'potensi_penjualan',
        'main_desk',
        'mulai_dikerjakan_pada',
        'selesai_dikerjakan_pada',
        'catatan_analisis',
        'catatan_approval',
        'catatan_revisi',
        'lampiran_revisi',
        'direvisi_pada',
        'approved_by',
        'pmh_approved_by',
        'pmh_approved_at',
        'berita_acara_file',
        'berita_acara_date',
        'berita_acara_no',
        'golive_validated_at',
        'golive_validated_by',
        'quotation_approved_client_at',
        'quotation_approved_tp_at',
        'invoicing_status',
        'user_id',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'bap_date' => 'date',
        'target_selesai' => 'date',
        'target_start_date' => 'date',
        'actual_completion_date' => 'date',
        'berita_acara_date' => 'date',
        'direvisi_pada' => 'datetime',
        'biaya_pengerjaan' => 'decimal:2',
        'harga_penawaran' => 'decimal:2',
        'solution_paper_required' => 'boolean',
        'mulai_dikerjakan_pada' => 'date',
        'selesai_dikerjakan_pada' => 'date',
        'mindesk_analisis' => 'decimal:2',
        'mindesk_development' => 'decimal:2',
        'mindesk_testing' => 'decimal:2',
        'pmh_approved_at' => 'datetime',
        'golive_validated_at' => 'datetime',
        'quotation_approved_client_at' => 'datetime',
        'quotation_approved_tp_at' => 'datetime',
    ];

    // Status sequential tracking list for execution
    public const SEQUENTIAL_STATUSES = [
        'analisa' => 'Analisa (Analysis)',
        'development' => 'Development',
        'sit' => 'SIT (System Integration Testing)',
        'uat' => 'UAT (User Acceptance Testing)',
        'training' => 'Training',
        'golive' => 'Go-Live',
    ];

    // Complete status mapping
    public static function statusList(): array
    {
        return [
            'awaiting_pm'                => 'Awaiting PM Verification',
            'diajukan'                   => 'Awaiting PM Verification',
            'awaiting_pmh'               => 'Awaiting PMH Validation',
            'dianalisis'                 => 'Awaiting PMH Validation',
            'revision_needed'            => 'Revision Needed',
            'validated'                  => 'Validated',
            'disetujui'                  => 'Validated',
            'analisa'                    => 'Analisa',
            'development'                => 'Development',
            'dikerjakan'                 => 'Development',
            'sit'                        => 'SIT (System Integration Testing)',
            'uat'                        => 'UAT (User Acceptance Testing)',
            'training'                   => 'Training',
            'awaiting_golive_validation' => 'Awaiting Go-Live Validation',
            'golive'                     => 'Go-Live',
            'selesai'                    => 'Go-Live',
            'invoicing'                  => 'Invoicing',
            'ditolak'                    => 'Ditolak',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            'awaiting_pm'                => 'Awaiting PM Verification',
            'awaiting_pmh'               => 'Awaiting PMH Validation',
            'revision_needed'            => 'Revision Needed',
            'validated'                  => 'Validated',
            'analisa'                    => 'Analisa',
            'development'                => 'Development',
            'sit'                        => 'SIT',
            'uat'                        => 'UAT',
            'training'                   => 'Training',
            'awaiting_golive_validation' => 'Awaiting Go-Live Validation',
            'golive'                     => 'Go-Live',
            'invoicing'                  => 'Invoicing',
            'ditolak'                    => 'Ditolak',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusList()[$this->status] ?? ucfirst($this->status);
    }

    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            'awaiting_pm', 'diajukan' => 'warning',
            'awaiting_pmh', 'dianalisis' => 'info',
            'revision_needed', 'ditolak' => 'danger',
            'validated', 'disetujui' => 'primary',
            'analisa' => 'info',
            'development', 'dikerjakan' => 'primary',
            'sit' => 'warning',
            'uat' => 'warning',
            'training' => 'info',
            'awaiting_golive_validation' => 'secondary',
            'golive', 'selesai' => 'success',
            'invoicing' => 'success',
            default => 'light',
        };
    }

    public function isOverdue(): bool
    {
        if (in_array($this->status, ['golive', 'selesai', 'invoicing', 'ditolak'], true)) {
            return false;
        }

        $dueDate = $this->target_selesai ?? $this->actual_completion_date;
        if (! $dueDate) {
            return false;
        }

        return $dueDate->startOfDay()->isPast();
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function solutionPaper()
    {
        return $this->hasOne(SolutionPaper::class, 'change_request_id');
    }

    public function invoices()
    {
        return $this->hasMany(CrInvoice::class, 'change_request_id');
    }

    public function totalMindesk(): float
    {
        return (float) ($this->mindesk_analisis ?? 0)
            + (float) ($this->mindesk_development ?? 0)
            + (float) ($this->mindesk_testing ?? 0);
    }

    public const RATE_PER_MINDESK = 4500000;

    public function saranHargaPenawaran(): float
    {
        return $this->totalMindesk() * self::RATE_PER_MINDESK;
    }

    public function isPmhApproved(): bool
    {
        if ($this->status === 'ditolak' || $this->status === 'revision_needed') {
            return false;
        }

        return $this->pmh_approved_at !== null
            || in_array($this->status, ['validated', 'disetujui', 'analisa', 'development', 'dikerjakan', 'sit', 'uat', 'training', 'awaiting_golive_validation', 'golive', 'selesai', 'invoicing'], true);
    }

    public function isReadyForInvoicing(): bool
    {
        return in_array($this->status, ['golive', 'selesai', 'invoicing'], true)
            || $this->golive_validated_at !== null;
    }

    public function canBeQuoted(): bool
    {
        return $this->isPmhApproved() && ! in_array($this->status, ['ditolak', 'revision_needed'], true);
    }

    public static function generateKode(): string
    {
        $year = date('Y');
        $lastNumber = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('CR-%s-%03d', $year, $lastNumber);
    }
}
