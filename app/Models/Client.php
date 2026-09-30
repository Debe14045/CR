<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'nickname',
        'email',
        'address',
        'phone',
        'active',
        // PIC Marketing
        'pic_marketing_name',
        'pic_marketing_phone',
        'pic_marketing_email',
        'pic_marketing_desc',
        // PIC IT / Programmer
        'pic_it_name',
        'pic_it_phone',
        'pic_it_email',
        'pic_it_desc',
        // PIC Procurement
        'pic_procurement_name',
        'pic_procurement_phone',
        'pic_procurement_email',
        'pic_procurement_desc',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function changeRequests()
    {
        return $this->hasMany(ChangeRequest::class, 'client_id');
    }
}
