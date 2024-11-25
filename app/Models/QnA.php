<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QnA extends Model
{
    use HasFactory;
    protected $table = 'qna';
    protected $fillable = ['user_id', 'question', 'answer'];
}
