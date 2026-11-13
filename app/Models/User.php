<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const DEFAULT_PASSWORD = '123456';

    public const ROLE_ADMIN = 'admin';
    public const ROLE_CLIENT = 'client';
    public const ROLE_PM = 'pm';
    public const ROLE_PMH = 'pmh';
    public const ROLE_PRESALES = 'presales';

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'admin', 'client', 'pm', 'pmh', 'presales'
        'phone',
        'can_act_as_pm', // Flag khusus PMH yang bisa merangkap PM (0 atau 1)
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'can_act_as_pm' => 'boolean',
        ];
    }

    // ── RELASI ──
    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'user_id');
    }

    public function clientPic()
    {
        return $this->hasOne(ClientPic::class, 'user_id');
    }

    public function client()
    {
        return $this->hasOneThrough(Client::class, ClientPic::class, 'user_id', 'id', 'id', 'client_id');
    }

    public function changeRequests()
    {
        return $this->hasMany(ChangeRequest::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Daftar proyek di mana user ini menjadi PM utama (projects.pm_id).
     */
    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'pm_id');
    }

    /**
     * Daftar seluruh proyek yang ditanggungjawabi user ini sebagai PM (relasi many-to-many project_pm).
     */
    public function projectsAsPm()
    {
        return $this->belongsToMany(Project::class, 'project_pm', 'pm_id', 'project_id')->withTimestamps();
    }

    /**
     * Daftar seluruh Change Request yang ditanggungjawabi user ini sebagai PM (relasi many-to-many change_request_pm).
     */
    public function changeRequestsAsPm()
    {
        return $this->belongsToMany(ChangeRequest::class, 'change_request_pm', 'pm_id', 'change_request_id')->withTimestamps();
    }

    /**
     * Ambil daftar user aktif yang dapat bertugas sebagai PM (role 'pm' atau 'pmh' yang merangkap PM).
     */
    public static function projectManagers(int|array|null $ensureIncludedIds = null)
    {
        $managers = static::with('pegawai')
            ->where(function ($q) {
                $q->where('role', self::ROLE_PM)
                  ->orWhere(function ($sub) {
                      $sub->where('role', self::ROLE_PMH)->where('can_act_as_pm', true);
                  });
            })
            ->where(function ($q) {
                $q->whereDoesntHave('pegawai')
                  ->orWhereHas('pegawai', function ($pq) {
                      $pq->where('is_active', true);
                  });
            })
            ->orderBy('name')
            ->get();

        // Saat edit proyek, pastikan PM lama yang masih terpasang (meski nonaktif) tetap muncul
        $extraIds = array_values(array_filter(is_array($ensureIncludedIds) ? $ensureIncludedIds : [$ensureIncludedIds]));
        if (!empty($extraIds)) {
            $missingIds = array_diff($extraIds, $managers->pluck('id')->all());
            if (!empty($missingIds)) {
                $existing = static::with('pegawai')->whereIn('id', $missingIds)->get();
                $managers = $managers->merge($existing);
            }
        }

        return $managers;
    }

    /**
     * Label jabatan/role ringkas untuk tampilan PM penanggung jawab.
     */
    public function pmRoleLabel(): string
    {
        if ($this->pegawai?->position) {
            return $this->pegawai->position;
        }

        return $this->role === self::ROLE_PMH ? 'PM Head (Merangkap PM)' : 'Project Manager';
    }

    // ── HELPER PENGCEKAN ROLE & PIC ──
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isMarketingPic(): bool
    {
        return $this->isClient() && optional($this->clientPic)->role_type === 'marketing';
    }

    public function isProcurementPic(): bool
    {
        return $this->isClient() && optional($this->clientPic)->role_type === 'procurement';
    }

    public function isItPic(): bool
    {
        return $this->isClient() && optional($this->clientPic)->role_type === 'it';
    }

    public function isPm(): bool
    {
        return $this->role === 'pm' || ($this->role === 'pmh' && session('active_role') === 'pm');
    }

    public function isPmh(): bool
    {
        return $this->role === 'pmh';
    }

    public function isPresales(): bool
    {
        return $this->role === 'presales';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canSwitchToPm(): bool
    {
        return $this->role === 'pmh' && $this->can_act_as_pm === true;
    }
}
