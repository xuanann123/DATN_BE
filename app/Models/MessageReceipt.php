<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'user_id',
        'is_read',
        'read_at'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
