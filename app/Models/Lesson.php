<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_module',
        'title',
        'thumbnail',
        'description',
        'content_type',
        'lessonable_id',
        'lessonable_type',
        'position',
        'is_active',
    ];

    public function lessonable()
    {
        return $this->morphTo();
    }
}
