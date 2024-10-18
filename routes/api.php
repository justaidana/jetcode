<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseLessonController;

Route::group(['prefix' => 'courses', 'as' => 'courses.'], function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{id}', [CourseController::class, 'show'])->name('show');
});
Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
    Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
});

