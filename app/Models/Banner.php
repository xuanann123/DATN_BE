<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;


class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'redirect_url',
        'image',
        'position',
        'start_time',
        'end_time',
        'is_active',
    ];
}
