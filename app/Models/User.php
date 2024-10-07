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
    //Một user có nhiều bài viết
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
