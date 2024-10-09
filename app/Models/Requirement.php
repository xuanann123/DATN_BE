<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'requirement',
        'course_id',
        'position',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
