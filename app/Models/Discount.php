<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'original_coins',
        'discount_percent',
        'discounted_coins',
        'final_coins',
    ];
}
