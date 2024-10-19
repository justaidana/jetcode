<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    public function module()
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
