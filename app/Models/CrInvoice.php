<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'change_request_id',
        // 3 Inputs
        'invoice_number',
        'invoice_date',
        'nominal',
        'job_description',
        'planned_payment_date',
        'actual_payment_date',
        'term_name',
        'percentage',
        // 3-4 Uploads
        'gr_number',
        'gr_file',
        'journal_number',
        'journal_file',
        'ba_date',
        'ba_file',
        'quotation_file',
        'signed_quotation_file',
        'payment_status',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'planned_payment_date' => 'date',
        'actual_payment_date' => 'date',
        'ba_date' => 'date',
        'nominal' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
