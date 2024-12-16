<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "type",
        "course_id",
    ];
    //Một cuộc hội thoại có nhiều thành viên tham gia
    public function members() {
        return $this->belongsToMany(User::class, "conversation_members", "conversation_id", "user_id");
    }
    //Trong cuộc hoại thoạ có nhiều tin nhắn nhóm
    public function messages() {
        return $this->hasMany(Message::class, "conversation_id");
    }
    //Thuộc về một khoá học
    public function course() {
        return $this->belongsTo(Course::class);
    }
}
