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
}
