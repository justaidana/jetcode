<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLessonComplete extends Model
{
    protected $fillable = ["course_lesson_id", "user_id"];
}
