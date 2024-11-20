<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Course extends Model
{
    use HasFactory, Notifiable;
    //Định nghĩa level cho khoá học
    const LEVEL_BEGINNER = 'Sơ Cấp';
    //Trung cấp
    const LEVEL_INTERMEDIATE = 'Trung Cấp';
    //Chuyên gia
    const LEVEL_MASTER = 'Chuyên Gia';


    //Trạng thái của khoá học
    const COURSE_STATUS_DRAFT = "draft";
    //pending
    const COURSE_STATUS_PENDING = "pending";
    //approved
    const COURSE_STATUS_APPROVED = "approved";
    //rejected
    const COURSE_STATUS_REJECTED = "rejected";
    const LEVEL_ARRAY = [
        self::LEVEL_BEGINNER,
        self::LEVEL_INTERMEDIATE,
        self::LEVEL_MASTER
    ];

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

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'id_course');
    }
    //Danh sách người đăng kí khoá học
    public function userCourses()
    {
        return $this->belongsToMany(User::class, 'user_courses', 'id_course', 'id_user');
    }
    //Lấy cái tiến độ của khoá học này ra
    public function progress()
    {
        //Lấy ra cái thằng số lượng progress_percen
        return $this->hasMany(UserCourse::class, 'id_course');
    }

    public function getCommentsCountAttribute(): int
    {
        // Lấy tất cả bình luận liên quan đến giảng viên
        return $this->comments()->count();
    }



    public function getAverageRatingAttribute(): float
    {
        // Tránh trường hợp không có rating, trả về 0 nếu không có đánh giá
        $averageRating = $this->ratings()->avg('rating');

        // Nếu không có đánh giá, trả về 0.0 để tránh trả về null
        return $averageRating !== null ? $averageRating : 0.0;
    }

    //Danh sách yêu thích khoá học
    public function wishlists()
    {
        return $this->belongsToMany(User::class, 'wish_lists', 'id_course', 'id_user');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'id_course');
    }

}
