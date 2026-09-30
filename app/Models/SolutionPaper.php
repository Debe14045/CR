<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolutionPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'change_request_id',
        'status', // Draft, Completed, Needs Revision
        'pegawai_id',
        'creator_name',
        'target_date_start',
        'target_date_end',
        'actual_completion_date',
        'solution_paper_file',
        'notes',
    ];

    protected $casts = [
        'target_date_start' => 'date',
        'target_date_end' => 'date',
        'actual_completion_date' => 'date',
    ];

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
