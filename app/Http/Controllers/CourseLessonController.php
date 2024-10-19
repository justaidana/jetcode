<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseLessonComplete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseLessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function sendAnswers(Request $request, $lesson_id)
    {
        $user = Auth::user();
        $lesson = CourseLesson::where('id', $lesson_id)->with('questions')->first();

        $userAnswers = $request->input('answers');

        $hasCourse = $user->courses()->where('course_id', $lesson->course_id)->exists();

        if (!$hasCourse) {
            return response()->json([
                'message' => 'Вы не приобрели этот курс.',
            ], 403);
        }

        $correctAnswers = 0;
        $totalQuestions = $lesson->questions->count();

        foreach ($lesson->questions as $question) {
            $questionId = $question->id;
            $correctAnswer = json_decode($question->correct_answer, true); // Decode JSON to array

            if (
                isset($userAnswers[$questionId]) &&
                isset($correctAnswer['answer']) &&
                $userAnswers[$questionId] == $correctAnswer['answer']
            ) {
                $correctAnswers++;
            }
        }

        Auth::user()->tokens += $correctAnswers * 10;
        $user->save();
        return response()->json([
            'message' => 'Ответы успешно проверены.',
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'score' => "$correctAnswers из $totalQuestions",
        ]);
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
        $lesson = CourseLesson::where('id', $lesson_id)->with("questions")->first();

        $hasCourse = $user->courses()->where('course_id', $lesson->course_id)->exists();

        if (!$hasCourse) {
            return response()->json([
                'message' => 'Вы не приобрели этот курс.',
            ], 403);
        }

        return response()->json(
            $lesson
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function complete(int $lesson_id)
    {
        $user = Auth::user();
        $lesson = CourseLesson::where('id', $lesson_id)->first();

        $hasCourse = $user->courses()->where('course_id', $lesson->course_id)->exists();

        if (!$hasCourse) {
            return response()->json([
                'message' => 'Вы не приобрели этот курс.',
            ], 403);
        }


        $complete = CourseLessonComplete::firstOrCreate(
            [
                'course_lesson_id' => $lesson_id,
                'user_id' => Auth::id(),
            ],
            [
                'course_lesson_id' => $lesson_id,
                'user_id' => Auth::id(),
            ]
        );

        return response()->json([
            $complete
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
