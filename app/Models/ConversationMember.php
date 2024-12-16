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
        'role',
        'is_owner',
        'is_muted',
        'banned_at',
        'banned_by',
        'joined_at',
        'left_at',
    ];

    protected $dates = [
        'banned_at',
        'joined_at',
        'left_at'
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

    // check xem co bi ban khong
    public function isBanned()
    {
        return !is_null($this->banned_at);
    }

    // tat thong bao
    public function isMuted()
    {
        return $this->is_muted;
    }

    // Nguoi ban member khoi group
    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

}
