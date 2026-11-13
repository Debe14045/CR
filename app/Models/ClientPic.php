<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPic extends Model
{
    use HasFactory;

    protected $table = 'client_pics';

    protected $fillable = [
        'client_id',
        'user_id',
        'role_type', // 'marketing', 'procurement', 'it'
        'name',
        'email',
        'phone',
    ];

    // ── RELASI ──
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}