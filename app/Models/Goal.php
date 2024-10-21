<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal',
        'course_id',
        'position',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
