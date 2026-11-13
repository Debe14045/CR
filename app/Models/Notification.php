<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_role',
        'change_request_id',
        'title',
        'message',
        'type',
        'category',
        'action_url',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Filter notifikasi untuk user (notifikasi PM selalu spesifik per user_id penanggung jawab proyek)
     */
    public function scopeForUser($query, $user)
    {
        if (is_object($user)) {
            $userId = $user->id;
            $activeRole = session()->isStarted()
                ? session('active_role', $user->role ?? null)
                : ($user->role ?? null);

            return $query->where(function ($q) use ($userId, $activeRole) {
                $q->where('user_id', $userId);
                if ($activeRole && $activeRole !== 'pm') {
                    $q->orWhere('target_role', $activeRole);
                }
            });
        }

        return $query->where('user_id', $user);
    }

    /**
     * Scope untuk notifikasi yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope untuk notifikasi yang sudah dibaca
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Tandai notifikasi ini telah dibaca
     */
    public function markAsRead()
    {
        $this->is_read = true;
        return $this->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class, 'change_request_id');
    }

    /**
     * Bootstrap icon class based on type/category
     */
    public function getIconAttribute()
    {
        return match (strtolower($this->type ?? 'info')) {
            'success' => 'bi-check-circle-fill',
            'warning' => 'bi-exclamation-triangle-fill',
            'danger', 'error' => 'bi-exclamation-octagon-fill',
            'review', 'approval' => 'bi-clipboard-check',
            default => 'bi-bell-fill',
        };
    }

    /**
     * Styling badge & icon colors
     */
    public function getColorTheme()
    {
        return match (strtolower($this->type ?? 'info')) {
            'success' => [
                'icon_bg' => '#ECFDF5',
                'icon_color' => '#10B981',
                'badge' => 'bg-success text-white',
            ],
            'warning' => [
                'icon_bg' => '#FFFBEB',
                'icon_color' => '#F59E0B',
                'badge' => 'bg-warning text-dark',
            ],
            'danger', 'error' => [
                'icon_bg' => '#FEF2F2',
                'icon_color' => '#EF4444',
                'badge' => 'bg-danger text-white',
            ],
            default => [
                'icon_bg' => '#EFF6FF',
                'icon_color' => '#0063D7',
                'badge' => 'bg-primary text-white',
            ],
        };
    }

    /**
     * Human-friendly category label
     */
    public function getCategoryLabelAttribute()
    {
        if ($this->category) {
            return ucfirst($this->category);
        }

        return match (strtolower($this->type ?? 'info')) {
            'success' => 'Selesai',
            'warning' => 'Perhatian',
            'danger', 'error' => 'Penting',
            default => 'Informasi',
        };
    }

    /**
     * Relative formatted time string
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '-';
    }
}
