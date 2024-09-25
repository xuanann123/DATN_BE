<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_lesson',
        'content',
        'parent_id',
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function parent () {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function chilren () {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
