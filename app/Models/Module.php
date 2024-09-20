<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_course',
        'title',
        'description',
        'position',
    ];

    public function course () {
        return $this->belongsTo(Course::class, 'id_course');
    }

    public function lessons () {
        return $this->hasMany(Lesson::class, 'id_module');
    }
}
