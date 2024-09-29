<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'video_youtube_id',
        'duration',
    ];

    public function lessons()
    {
        return $this->morphMany(Lesson::class, 'lessonable');
    }
}
