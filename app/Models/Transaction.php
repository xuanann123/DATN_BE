<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transactionable_id',
        'transactionable_type',
        'coin_unit',
        'amount',
        'coin',
        'status',
    ];

    public function transactionable()
    {
        return $this->morphTo();
    }
}
