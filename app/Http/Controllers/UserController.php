<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
            "tg_id"  =>  1231,

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
        $user = auth()->user(); // Get the authenticated user

        $courses = $user->courses()->with('category')->get(); // Fetch courses with category

        return response()->json($courses); // Return courses as JSON
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
