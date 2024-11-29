<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coding extends Model
{
    use HasFactory;

    protected $fillable = [
        'language',
        'statement',
        'hints',
        'sample_code',
        'output',
    ];

    public function lesson()
    {
        return $this->morphOne(Lesson::class, 'lessonable');
    }
}
