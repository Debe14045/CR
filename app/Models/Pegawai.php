<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawais';

    public const DIVISI_OPTIONS = [
        'PMO',
        'Back Office',
        'Maintenance & Support',
    ];

    public const DEPARTEMEN_OPTIONS = [
        'Development',
        'Support',
        'RnD',
    ];

    public const JABATAN_OPTIONS = [
        'Programmer',
        'System Analyst',
        'Project Manager',
        'Project Manager Head',
        'PMO Head',
        'QA',
        'Marketing',
    ];

    protected $fillable = [
        'user_id',
        'nip',
        'name',
        'email',
        'phone',
        'divisi',
        'departemen',
        'position',
        'role',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── RELASI ──
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hasWebAccount(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Ringkasan jabatan, departemen, dan divisi untuk ditampilkan di tabel admin.
     */
    public function organizationSummary(): string
    {
        $parts = array_filter([$this->position, $this->departemen, $this->divisi]);

        return !empty($parts) ? implode(' • ', $parts) : '-';
    }

    /**
     * Metadata tampilan badge role pegawai pada tabel admin & dashboard.
     */
    public function roleBadgeMeta(bool $compact = false): array
    {
        return match ($this->role) {
            'pm' => [
                'bg' => '#ECFDF5',
                'text' => '#059669',
                'border' => '#A7F3D0',
                'label' => $compact ? 'PM' : 'Project Manager',
                'icon' => 'bi-kanban-fill',
            ],
            'pmh' => [
                'bg' => '#FFF1F2',
                'text' => '#E11D48',
                'border' => '#FECDD3',
                'label' => 'PM Head',
                'icon' => 'bi-shield-check',
            ],
            'presales' => [
                'bg' => '#EFF6FF',
                'text' => '#0063D7',
                'border' => '#BFDBFE',
                'label' => 'Presales',
                'icon' => 'bi-briefcase-fill',
            ],
            'admin' => [
                'bg' => '#EEF2FF',
                'text' => '#4F46E5',
                'border' => '#C7D2FE',
                'label' => $compact ? 'Admin' : 'Administrator',
                'icon' => 'bi-gear-wide-connected',
            ],
            'programmer' => [
                'bg' => '#F0FDFA',
                'text' => '#0F766E',
                'border' => '#99F6E4',
                'label' => 'Programmer',
                'icon' => 'bi-code-slash',
            ],
            'staff' => [
                'bg' => '#F1F5F9',
                'text' => '#475569',
                'border' => '#CBD5E1',
                'label' => $compact ? 'Tim Teknis' : 'Tim Teknis / Staff',
                'icon' => 'bi-person-gear',
            ],
            'non_login' => [
                'bg' => '#F8FAFC',
                'text' => '#64748B',
                'border' => '#CBD5E1',
                'label' => 'Tanpa Role',
                'icon' => 'bi-dash-circle',
            ],
            default => [
                'bg' => '#F1F5F9',
                'text' => '#475569',
                'border' => '#E2E8F0',
                'label' => ucfirst((string) $this->role),
                'icon' => 'bi-person',
            ],
        };
    }
}

