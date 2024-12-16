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
        "last_message_id",
        "is_active",
    ];
    //Một cuộc hội thoại có nhiều thành viên tham gia
    public function members()
    {
        return $this->belongsToMany(User::class, "conversation_members", "conversation_id", "user_id")
            ->withPivot('role', 'is_owner', 'is_muted', 'banned_at', 'joined_at', 'left_at');
    }
    //Trong cuộc hoại thoạ có nhiều tin nhắn nhóm
    public function messages()
    {
        return $this->hasMany(Message::class, "conversation_id");
    }
    //Thuộc về một khoá học
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    // tin nhắn gần nhất
    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }
}
