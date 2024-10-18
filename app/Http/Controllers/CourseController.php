<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::all();
        return response()->json($courses);
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
    public function show(string $id)
    {
        $course = Course::with([
            'category:id,title',
            'course_modules.lessons:id,course_module_id,title'
        ])->findOrFail($id);

        return response()->json($course);
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

    public function buy($id)
    {
        $course = Course::findOrFail($id);
        $user = Auth::user();

        if ($user->courses()->where('course_id', $id)->exists()) {
            return response()->json([
                'message' => 'У вас уже есть этот курс.'
            ], 400);
        }

        if ($user->balance < $course->price) {
            return response()->json([
                'message' => 'Недостаточно средств.'
            ], 400);
        }

        $user->balance -= $course->price;
        $user->save();

        $user->courses()->attach($course);

        return response()->json([
            'message' => 'Курс успешно куплен.',
            'course' => $course,
            'remaining_balance' => $user->balance
        ], 200);
    }
}
