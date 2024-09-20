<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];
    //Một tags thuộc về nhiều bài học
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_tags');
    }
}
