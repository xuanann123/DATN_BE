<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'phone',
        'address',
        'following',
        'experience',
        'bio',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function education()
    {
        return $this->hasOne(Education::class, 'id_profile');
    }
}
