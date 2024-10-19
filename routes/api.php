<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseLessonController;

Route::group(['prefix' => 'courses', 'as' => 'courses.'], function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{id}', [CourseController::class, 'show'])->name('show');
    Route::post('/buy/{id}', [CourseController::class, 'buy'])->name('buy');
    Route::post('/{id}/certificate', [CourseController::class, 'certificate'])->name('certificate');
});
Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
    Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
    Route::get('/', [CategoryController::class, 'index'])->name('index');
});

Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::get('/me', [UserController::class, 'show'])->name('show');
    Route::get('/me/courses', [UserController::class, 'userCourses'])->name('courses');
    Route::post('/transfer', [UserController::class, 'transfer'])->name('transfer');
});

Route::group(['prefix' => 'lessons', 'as' => 'lessons.'], function () {
    Route::get('/{id}', [CourseLessonController::class, 'show'])->name('show');
    Route::post('/{lesson_id}/sendAnswers', [CourseLessonController::class, 'sendAnswers'])->name('sendAnswers');
    Route::get('/{id}/complete', [CourseLessonController::class, 'complete'])->name('complete');
});


