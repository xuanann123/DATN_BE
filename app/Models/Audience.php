<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audience extends Model
{
    use HasFactory;

    protected $fillable = [
        'audience',
        'position',
        'course_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
