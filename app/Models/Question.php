<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_quiz',
        'question',
        'type',
        'image_url',
        'points',
    ];
    //Một question lại có nhiều option
    public function options(){
        return $this->hasMany(Option::class, "id_question", "id");
    }
}
