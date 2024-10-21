<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_course',
        'voucher_code',
        'voucher_discount',
        'total_coin',
        'total_coin_after_discount',
        'status',
    ];
}
