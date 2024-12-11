<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roadmap extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'sort_description',
        'thumbnail'
    ];
    //Nó thuốc vệ một user tạo ra
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    //Một thằng này có nhiều giai đoạn
    public function phases() {
        return $this->hasMany(Phase::class, 'id_roadmap');
    }
}
