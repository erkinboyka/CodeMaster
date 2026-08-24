<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->string('activity_type', 50)->change();
        });

        Schema::table('contest_submissions', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->unsignedInteger('task_id')->nullable()->change();
            $table->foreign('task_id')->references('id')->on('contest_problems')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->enum('activity_type', ['course', 'vacancy', 'application', 'lesson', 'certificate'])->change();
        });

        Schema::table('contest_submissions', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->foreign('task_id')->references('id')->on('lesson_practice_tasks')->onDelete('set null');
        });
    }
};
