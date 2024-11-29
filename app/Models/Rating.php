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

    // public function ratings() {
    //     return $this->belongsTo(Course::class, 'id_course');
    // }


    public function course() {
        return $this->belongsTo(Course::class, 'id_course');
    }

    //Người nào comment
    public function user() {
        return $this->belongsTo(User::class, 'id_user');
    }

    // phản hồi của đánh giá
    public function rating_replies() {
        return $this->hasMany(RatingReply::class, 'rating_id');
    }
}
