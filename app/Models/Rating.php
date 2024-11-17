<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_course',
        'content',
        'rate',
    ];

    public function ratings() {
        return $this->belongsTo(Course::class, 'id_course');
    }

    public function course() {
        return $this->belongsTo(Course::class, 'id_course');
    }
}
