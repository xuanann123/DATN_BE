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
        'is_banned',
        'status',
        'views',
        'allow_comments',
        'published_at',
        'user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post');
    }

    // ham search
    public function scopeSearch($query, $searchQuery)
    {
        $query->when($searchQuery, function ($query) use ($searchQuery) {
            $query->whereFullText('title', $searchQuery)
                ->orWhereFullText('description', $searchQuery)
                ->orWhereFullText('content', $searchQuery)
                ->orWhere('title', 'LIKE', "%{$searchQuery}%")
                ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                ->orWhere('content', 'LIKE', "%{$searchQuery}%")
                ->orWhereHas('categories', function ($subQuery) use ($searchQuery) {
                    $subQuery->where('name', 'LIKE', "%{$searchQuery}%");
                })
                ->orWhereHas('tags', function ($subQuery) use ($searchQuery) {
                    $subQuery->where('name', 'LIKE', "%{$searchQuery}%");
                });

        });
    }

    // ham status filter
    public function scopeStatusFilter($query, $statusFilter)
    {
        $query->when($statusFilter && $statusFilter != 'all', function ($query) use ($statusFilter) {
            if ($statusFilter == 'private') {
                $query->where('is_banned', 1);
            } else {
                $query->where('status', $statusFilter);
            }
        });
    }

    // ham time filter
    public function scopeTimeFilter($query, $timeFilter)
    {
        return $query->when($timeFilter && $timeFilter !== 'all', function ($query) use ($timeFilter) {
            $date = now();
            match ($timeFilter) {
                'today' => $query->whereDate('created_at', $date),
                'yesterday' => $query->whereDate('created_at', $date->subDay()),
                'this_week' => $query->whereBetween('created_at', [
                    $date->startOfWeek()->toDateTimeString(),
                    $date->endOfWeek()->toDateTimeString()
                ]),
                'this_month' => $query->whereMonth('created_at', $date->month),
                'this_year' => $query->whereYear('created_at', $date->year),
                default => null
            };
        });
    }
    //Đinh nghĩa mối quan hệ nhiều nhiều với comment
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

}
