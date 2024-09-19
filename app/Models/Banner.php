<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;


class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'redirect_url',
        'image',
        'content',
        'position',
        'start_time',
        'end_time',
        'is_active',
    ];
    //Khai báo cách search từ khoá như sau
    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            return $query->whereFullText(['title', 'content'], $keyword);
        }
        return $query;
    }
}
