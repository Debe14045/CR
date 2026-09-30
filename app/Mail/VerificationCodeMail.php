<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $email;
    public string $purpose;
    public int $expiryMinutes;

    public function __construct(string $code, string $email, string $purpose = 'login', int $expiryMinutes = 10)
    {
        $this->code = $code;
        $this->email = $email;
        $this->purpose = $purpose;
        $this->expiryMinutes = $expiryMinutes;
    }

    public function envelope(): Envelope
    {
        $action = match ($this->purpose) {
            'register' => 'Pendaftaran Akun',
            'link'     => 'Tautan Akun Google',
            default    => 'Masuk Portal',
        };

        return new Envelope(
            subject: "[ITPI Enterprise] Kode Verifikasi {$this->code} untuk {$action}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification_code',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
