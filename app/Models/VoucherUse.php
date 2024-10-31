<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class VoucherUse extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_voucher',
        'id_user',
        'applied_at',
        'expires_at',
        'is_used',
    ];
}
