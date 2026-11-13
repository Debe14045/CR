<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'company',
        'nickname',
        'address',
    ];

    // ── RELASI ──
    public function pics()
    {
        return $this->hasMany(ClientPic::class, 'client_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function changeRequests()
    {
        return $this->hasMany(ChangeRequest::class, 'client_id');
    }
}