<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'rating_id',
        'user_id',
        'reply',
    ];

    public function rating()
    {
        return $this->belongsTo(Rating::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
