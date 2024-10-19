<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLessonQuestion extends Model
{
    public function courseLesson()
    {
        return $this->belongsTo(CourseLesson::class);
    }
}
