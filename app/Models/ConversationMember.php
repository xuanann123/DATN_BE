<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'conversation_id',
        'user_id',
        'is_owner',
        'role',
    ];
    //Phục thuộc vào một conversation
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
    //Phục thuộc vào một user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
