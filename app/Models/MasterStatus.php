<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'badge_color',
        'sort_order',
        'description',
    ];
}
