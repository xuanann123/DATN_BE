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
        'description',
        'learned',
        'slug',
        'duration',
        'sort_description',
        'price',
        'price_sale',
        'total_student',
        'is_active',
        'is_free',
        'is_trending',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function modules () {
        return $this->hasMany(Module::class, 'id_course');
    }
    //Một khoá học có nhiều tags
    public function tags() {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
