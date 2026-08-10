<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_exams', function (Blueprint $table) {
            $table->longText('question_bank_json')->nullable()->after('exam_json')->comment('Pool of 50-100 questions');
            $table->integer('questions_per_exam')->default(30)->after('pass_percent')->comment('How many questions to pick from bank');
        });
    }

    public function down(): void
    {
        Schema::table('course_exams', function (Blueprint $table) {
            $table->dropColumn(['question_bank_json', 'questions_per_exam']);
        });
    }
};
