<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function createUser()
    {
        User::create([
            "name" => "John Doe",
            "email" => "john@doe.com",
            "password" => "password",
            "tg_id" => 1231,

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
    public function show()
    {
        $user = auth()->user();

        return response()->json($user);
    }

    // Show the courses for the authenticated user
    public function userCourses()
    {
        $user = auth()->user();

        $courses = $user->courses()->with('category')->withCount('lessons', 'course_modules')->get();

        return response()->json($courses);
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

    public function transfer(Request $request)
    {
        $user = Auth::user();

        if ($user->tokens <= 0) {
            return response()->json([
                'message' => 'Нет доступных курсов.',
            ], 400);
        }

        $amount = $user->tokens;

        $user->balance += $amount;
        $user->tokens = 0;
        $user->save();

        return response()->json([
            'message' => 'All tokens transferred successfully.',
            'new_balance' => $user->balance,
            'remaining_tokens' => $user->tokens,
        ]);
    }
}
