<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_category',
        'code',
        'name',
        'thumbnail',
        'trailer',
        'description',
        'learned',
        'slug',
        'level',
        'duration',
        'sort_description',
        'price',
        'price_sale',
        'total_student',
        'is_active',
        'is_free',
        'is_trending',
        'status',
        'submited_at',
        'admin_comments'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'id_course');
    }
    //Một khoá học có nhiều tags
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    // muc tieu, yeu cau, doi tuong khi tao khoa hoc
    public function goals()
    {
        return $this->hasMany(Goal::class, 'course_id');
    }

    public function requirements()
    {
        return $this->hasMany(Requirement::class, 'course_id');
    }
    public function audiences()
    {
        return $this->hasMany(Audience::class, 'course_id');
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

}
