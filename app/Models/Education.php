<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_profile',
        'institution_name',
        'degree',
        'major',
        'start_date',
        'end_date',
    ];
}
