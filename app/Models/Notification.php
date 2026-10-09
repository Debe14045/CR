<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function changeRequest(): BelongsTo
    {
        return $this->belongsTo(ChangeRequest::class);
    }

    /**
     * Scope to fetch notifications intended for a specific user or their role
     */
    public function scopeForUser($query, ?User $user)
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere(function ($sq) use ($user) {
                  $sq->whereNull('user_id')
                     ->where(function ($rq) use ($user) {
                         $rq->whereNull('target_role')
                            ->orWhere('target_role', $user->role)
                            ->orWhere('target_role', 'all');
                     });
              });
        });
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Static helper to easily create and dispatch notification
     */
    public static function sendNotification(array $attributes): self
    {
        return self::create([
            'user_id'           => $attributes['user_id'] ?? null,
            'target_role'       => $attributes['target_role'] ?? null,
            'change_request_id' => $attributes['change_request_id'] ?? null,
            'title'             => $attributes['title'],
            'message'           => $attributes['message'],
            'type'              => $attributes['type'] ?? 'info',
            'category'          => $attributes['category'] ?? 'system',
            'action_url'        => $attributes['action_url'] ?? null,
            'is_read'           => false,
        ]);
    }

    /**
     * Get visual icon based on notification type and category
     */
    public function getIconAttribute(): string
    {
        return match ($this->category) {
            'cr_rejection'  => 'bi-x-circle-fill',
            'cr_approval'   => 'bi-check-circle-fill',
            'cr_submitted'  => 'bi-file-earmark-plus-fill',
            'status_update' => 'bi-arrow-repeat',
            'quotation'     => 'bi-cash-coin',
            'invoice'       => 'bi-receipt-cutoff',
            'message'       => 'bi-chat-left-dots-fill',
            'deadline'      => 'bi-exclamation-triangle-fill',
            default         => match ($this->type) {
                'danger'  => 'bi-exclamation-octagon-fill',
                'warning' => 'bi-exclamation-triangle-fill',
                'success' => 'bi-check-circle-fill',
                default   => 'bi-bell-fill',
            },
        };
    }

    /**
     * Color theme attributes for UI styling
     */
    public function getColorTheme(): array
    {
        return match ($this->type) {
            'danger' => [
                'bg'     => '#FEF2F2',
                'border' => '#FECDCA',
                'text'   => '#991B1B',
                'subtext'=> '#DC2626',
                'badge'  => 'bg-danger-subtle text-danger',
                'icon_bg'=> '#FEE2E2',
                'icon_color' => '#DC2626',
            ],
            'warning' => [
                'bg'     => '#FFFBEB',
                'border' => '#FEDF89',
                'text'   => '#92400E',
                'subtext'=> '#B45309',
                'badge'  => 'bg-warning-subtle text-warning',
                'icon_bg'=> '#FEF3C7',
                'icon_color' => '#D97706',
            ],
            'success' => [
                'bg'     => '#F0FDF4',
                'border' => '#BBF7D0',
                'text'   => '#166534',
                'subtext'=> '#15803D',
                'badge'  => 'bg-success-subtle text-success',
                'icon_bg'=> '#DCFCE7',
                'icon_color' => '#16A34A',
            ],
            default => [
                'bg'     => '#EFF6FF',
                'border' => '#BFDBFE',
                'text'   => '#1E40AF',
                'subtext'=> '#2563EB',
                'badge'  => 'bg-primary-subtle text-primary',
                'icon_bg'=> '#DBEAFE',
                'icon_color' => '#2563EB',
            ],
        };
    }

    /**
     * Human-readable label for category
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'cr_rejection'  => 'Penolakan CR',
            'cr_approval'   => 'Persetujuan CR',
            'cr_submitted'  => 'Pengajuan Baru',
            'status_update' => 'Perubahan Status',
            'quotation'     => 'Quotation & Biaya',
            'invoice'       => 'Invoice & Tagihan',
            'message'       => 'Pesan Diskusi',
            'deadline'      => 'Peringatan Batas Waktu',
            default         => 'Notifikasi Sistem',
        };
    }

    /**
     * Indonesian formatted relative time
     */
    public function getFormattedTimeAttribute(): string
    {
        $created = $this->created_at ?? now();
        $diffMinutes = (int) floor(abs($created->diffInMinutes(now())));

        if ($diffMinutes < 1) {
            return 'Baru saja';
        }
        if ($diffMinutes < 60) {
            return $diffMinutes . ' menit yang lalu';
        }

        $diffHours = (int) floor(abs($created->diffInHours(now())));
        if ($diffHours < 24) {
            return $diffHours . ' jam yang lalu';
        }

        $diffDays = (int) floor(abs($created->diffInDays(now())));
        if ($diffDays === 1) {
            return 'Kemarin, ' . $created->format('H:i');
        }
        if ($diffDays < 7) {
            return $diffDays . ' hari yang lalu, ' . $created->format('H:i');
        }

        return $created->format('d M Y H:i');
    }
}
