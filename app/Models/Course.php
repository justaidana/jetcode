<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function course_modules()
    {
        return $this->hasMany(CourseModule::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_courses');
    }

}
