<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseLessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($lesson_id)
    {
        $user = Auth::user();
        $lesson = CourseLesson::where('id', $lesson_id)->first();

        $hasCourse = $user->courses()->where('course_id', $lesson->course_id)->exists();

        if (!$hasCourse) {
            return response()->json([
                'message' => 'Вы не приобрели этот курс.',
            ], 403);
        }

        return response()->json([
            'lessons' => $lesson
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
