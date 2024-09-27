<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'content',
        'slug',
        'thumbnail',
        'is_active',
        'id_banned',
        'status',
        'views',
        'allow_comments',
        'published_at',
        'user_id',
    ];

    public function user () {
        return $this->belongsTo(User::class);
    }

    public function tags () {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function categories () {
        return $this->belongsToMany(Category::class, 'category_post');
    }
}
