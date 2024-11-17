<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    const TYPE_ADMIN = 'admin';
    const TYPE_MEMBER = 'member';
    const TYPE_TEACHER = 'teacher';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'is_active',
        'user_verify',
        'user_type',
        'verification_token',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    //Một tài khoản có một profile
    public function profile()
    {
        return $this->hasOne(Profile::class, 'id_user');
    }

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }
    //Một user có thể đăng nhiều khoá học
    public function courses()
    {
        return $this->hasMany(Course::class, 'id_user');
    }
    public function posts() {
        return $this->hasMany(Post::class, 'user_id');
    }
    //Những khoá học đã đăng kí
    public function userCourses()
    {
        return $this->belongsToMany(Course::class, 'user_courses', 'id_user', 'id_course');
    }
    //Định nghĩa mối quan hệ người được theo dõi
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }
    //Người mà user này đang theo dõi
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }
    //Danh sách yêu thích khoá học
    public function wishlists()
    {
        return $this->belongsToMany(Course::class, 'wish_lists', 'id_user', 'id_course');
    }

    public function certificates() {
        return $this->hasMany(Certificate::class);
    }
    public function ratings()
    {
        return $this->hasManyThrough(Rating::class, Course::class, 'id_user', 'id_course');
    }
}
