<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'discount',
        'start_time',
        'end_time',
        'count',
        'used_count',
        'is_active',
    ];
    //mối quan hệ nhiều nhiều với khóa học thông qua bảng trung gian
    public function courses() {
return $this->belongsToMany(Course::class,'id_user','','id_course', 'course_vouchers');
    }
}
