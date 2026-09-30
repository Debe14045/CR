<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = [
        'email',
        'code',
        'purpose',
        'attempts',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
        'attempts'    => 'integer',
    ];

    public const MAX_ATTEMPTS = 5;
    public const CODE_LIFETIME_MINUTES = 10;
    public const RESEND_COOLDOWN_SECONDS = 60;

    /**
     * Generate or renew an OTP code for an email.
     */
    public static function createOtp(string $email, string $purpose = 'login'): self
    {
        $normalizedEmail = strtolower(trim($email));

        // Invalidate old active OTPs for this email and purpose
        static::where('email', $normalizedEmail)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->delete();

        // 6-digit numeric OTP code
        $code = (string) random_int(100000, 999999);

        return static::create([
            'email'      => $normalizedEmail,
            'code'       => $code,
            'purpose'    => $purpose,
            'attempts'   => 0,
            'expires_at' => Carbon::now()->addMinutes(self::CODE_LIFETIME_MINUTES),
        ]);
    }

    /**
     * Check if a cooldown is currently active for resending.
     */
    public static function hasActiveCooldown(string $email, string $purpose = 'login'): bool
    {
        $normalizedEmail = strtolower(trim($email));
        $latest = static::where('email', $normalizedEmail)
            ->where('purpose', $purpose)
            ->latest()
            ->first();

        if (! $latest) {
            return false;
        }

        $secondsSinceCreation = Carbon::now()->diffInSeconds($latest->created_at);
        return $secondsSinceCreation < self::RESEND_COOLDOWN_SECONDS;
    }

    /**
     * Remaining cooldown seconds.
     */
    public static function getRemainingCooldown(string $email, string $purpose = 'login'): int
    {
        $normalizedEmail = strtolower(trim($email));
        $latest = static::where('email', $normalizedEmail)
            ->where('purpose', $purpose)
            ->latest()
            ->first();

        if (! $latest) {
            return 0;
        }

        $secondsSinceCreation = Carbon::now()->diffInSeconds($latest->created_at);
        $remaining = self::RESEND_COOLDOWN_SECONDS - $secondsSinceCreation;

        return max(0, (int) $remaining);
    }

    /**
     * Validate the provided code.
     */
    public function verify(string $inputCode): bool
    {
        if ($this->isExpired() || $this->isVerified() || $this->attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        $this->increment('attempts');

        if (hash_equals((string) $this->code, trim($inputCode))) {
            $this->update(['verified_at' => Carbon::now()]);
            return true;
        }

        return false;
    }

    public function isExpired(): bool
    {
        return Carbon::now()->isAfter($this->expires_at);
    }

    public function isVerified(): bool
    {
        return ! is_null($this->verified_at);
    }
}
