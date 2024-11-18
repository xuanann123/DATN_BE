<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_course',
        'progress_percent',
        'completed_at',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
