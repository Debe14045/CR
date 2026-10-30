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

    /**
     * Mutator to safely clamp and format percentage values,
     * preventing decimal(5,2) numeric field overflow in PostgreSQL.
     */
    public function setPercentageAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['percentage'] = null;
            return;
        }

        if (is_string($value)) {
            if (preg_match('/(\d+(?:[\.,]\d+)?)\s*%/i', $value, $matches)) {
                $val = (float) str_replace(',', '.', $matches[1]);
            } elseif (preg_match('/(\d+(?:[\.,]\d+)?)/', $value, $matches)) {
                $val = (float) str_replace(',', '.', $matches[1]);
            } else {
                $val = (float) $value;
            }
        } else {
            $val = (float) $value;
        }

        if ($val > 100) {
            // Guard against legacy/multiplier artifacts e.g. 10000 -> 100
            if ($val == 10000 || $val == 1000) {
                $val = 100.0;
            } else {
                $val = min(100.0, $val);
            }
        }

        $this->attributes['percentage'] = max(0, round($val, 2));
    }

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
