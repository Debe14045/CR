<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'change_request_id',
        'invoice_number',
        'term_name',
        'percentage',
        'nominal',
        'job_description',
        'invoice_date',
        'planned_payment_date',
        'actual_payment_date',
        'payment_status',
        'gr_file',
        'gr_number',
        'journal_file',
        'journal_number',
        'ba_file',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'planned_payment_date' => 'date',
        'actual_payment_date' => 'date',
        'percentage' => 'decimal:2',
        'nominal' => 'decimal:2',
    ];

    // ── RELASI ──
    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class, 'change_request_id');
    }
}