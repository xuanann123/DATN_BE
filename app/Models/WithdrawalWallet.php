<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'balance',
        'status',
    ];

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
}
