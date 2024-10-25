<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'id_module',
        'description',
        'total_points',
    ];
    //Một quiz có nhiều question
    public function questions() {
        return $this->hasMany(Question::class, "id_quiz", "id");
    }

    public function module() {
        return $this->belongsTo(Module::class, 'id_module');
    }

    public function quizProgress() {
        return $this->hasMany(QuizProgress::class);
    }
}
