<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_CLIENT = 'client';
    public const ROLE_PM = 'pm';
    public const ROLE_PRESALES = 'presales';
    public const ROLE_FINANCE = 'finance';
    public const ROLE_PMH = 'pmh';
    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function roleOptions(): array
    {
        return [
            self::ROLE_CLIENT => 'Client',
            self::ROLE_PM => 'Project Manager (PM)',
            self::ROLE_PMH => 'PM Head (PMH)',
            self::ROLE_FINANCE => 'Marketing / Keuangan (Finance)',
            self::ROLE_ADMIN => 'Administrator',
        ];
    }

    public function isRole(string $role): bool
    {
        if ($role === 'finance' && $this->role === 'presales') {
            return true;
        }
        if ($role === 'presales' && $this->role === 'finance') {
            return true;
        }
        return $this->role === $role;
    }

    public function isClient(): bool
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function isPm(): bool
    {
        return $this->role === self::ROLE_PM;
    }

    public function isPmh(): bool
    {
        return $this->role === self::ROLE_PMH;
    }

    public function isFinance(): bool
    {
        return in_array($this->role, [self::ROLE_FINANCE, self::ROLE_PRESALES], true);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isInternal(): bool
    {
        return in_array($this->role, [self::ROLE_PM, self::ROLE_PMH, self::ROLE_FINANCE, self::ROLE_PRESALES, self::ROLE_ADMIN], true);
    }
}
