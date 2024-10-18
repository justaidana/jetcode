<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function course_modules()
    {
        return $this->hasMany(CourseModule::class);
    }
}
