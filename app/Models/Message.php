<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'type',
        'deleted_at'
    ];

    // Quan hệ: Tin nhắn thuộc một hội thoại
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    // Quan hệ: Người gửi là một user
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // trang thai doc tin nhan
    public function receipts()
    {
        return $this->hasMany(MessageReceipt::class);
    }
}
