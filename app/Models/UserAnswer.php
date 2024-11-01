<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;
    // Đặt tên bảng nếu nó không theo quy tắc mặc định của Laravel
    protected $table = 'user_answers';

    // Các thuộc tính có thể được gán hàng loạt
    protected $fillable = [
        'user_id',
        'quiz_id',
        'question_id',
        'option_id',
    ];

    // Quan hệ với model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với model Quiz
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Quan hệ với model Question
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Quan hệ với model Option
    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
