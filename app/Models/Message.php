<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'message',
    ];
    //Tin nhắn thuộc về người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
