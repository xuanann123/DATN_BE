<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'id_lesson',
        'content'
    ];

    // public function lessons()
    // {
    //     return $this->morphMany(Lesson::class, 'lessonable');
    // }

    public function lesson () {
        return $this->belongsTo(Lesson::class, 'id_lesson');
    }
}
