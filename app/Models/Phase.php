<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;
    protected $fillable = [
        'order',
        'id_roadmap',
        'name',
        'description',
    ];
    //Trong thằng này thì có thể chứa nhiều khoá học
    public function courses() {
        return $this->belongsToMany(Course::class, 'phase_course', 'id_phase', 'id_course');
    }
    //Một thằng này nó thuộc về một roadmap
    public function roadmap() {
        return $this->belongsTo(Roadmap::class, 'id_roadmap');
    }
}
