<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->after('id');
            $table->enum('type', ['admin', 'teacher', 'private'])->default('admin')->after('user_id');
            $table->boolean('ai_generated')->default(false)->after('type');
            $table->string('topic')->nullable()->after('ai_generated');
            $table->string('course_level', 50)->nullable()->after('level');
            $table->integer('freetime')->nullable()->after('course_level');
            $table->integer('total_steps')->default(0)->after('freetime');
            $table->integer('total_experience')->default(0)->after('total_steps');
            $table->string('logo')->nullable()->after('image_url');
            $table->unsignedInteger('students_count')->default(0)->after('logo');

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id', 'type', 'ai_generated', 'topic', 'course_level',
                'freetime', 'total_steps', 'total_experience', 'logo', 'students_count',
            ]);
        });
    }
};
