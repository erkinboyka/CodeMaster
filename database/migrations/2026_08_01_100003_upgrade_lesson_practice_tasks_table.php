<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_practice_tasks', function (Blueprint $table) {
            $table->text('expected_output')->nullable()->after('prompt');
            $table->integer('time_limit')->default(60)->after('is_required')->comment('Time limit in minutes');
            $table->text('hints')->nullable()->after('time_limit');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium')->after('hints');
            $table->longText('test_runner_json')->nullable()->after('difficulty')->comment('Tests for auto-runner');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_practice_tasks', function (Blueprint $table) {
            $table->dropColumn(['expected_output', 'time_limit', 'hints', 'difficulty', 'test_runner_json']);
        });
    }
};
