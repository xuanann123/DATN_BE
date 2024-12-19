<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawMoney extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'coin',
        'amount',
        'bank_name',
        'account_number',
        'account_holder',
        'status',
        'note',
        'photo_evidence',
        'id_depositor'
    ];
}
