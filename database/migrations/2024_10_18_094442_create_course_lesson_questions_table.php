<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_lesson_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_lesson_id');
            $table->foreign('course_lesson_id')->references('id')->on('course_lessons')->onDelete('cascade');
            $table->string('text');
            $table->string('question_type');
            $table->jsonb('options')->nullable();
            $table->jsonb('correct_answer')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lesson_questions');
    }
};
