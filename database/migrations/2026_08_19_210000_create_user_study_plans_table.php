<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_study_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('goal', 50)->default('balanced');
            $table->string('difficulty', 20)->default('medium');
            $table->unsignedSmallInteger('daily_goal')->default(3);
            $table->date('deadline')->nullable();
            $table->unsignedSmallInteger('total_problems')->default(0);
            $table->unsignedSmallInteger('completed_problems')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_study_plan_problems', function (Blueprint $table) {
            $table->unsignedBigInteger('user_study_plan_id');
            $table->foreign('user_study_plan_id')->references('id')->on('user_study_plans')->cascadeOnDelete();
            $table->unsignedBigInteger('problem_id');
            $table->foreign('problem_id')->references('id')->on('problems')->cascadeOnDelete();
            $table->unsignedSmallInteger('order_num')->default(0);
            $table->boolean('is_solved')->default(false);
            $table->unsignedSmallInteger('time_spent_ms')->default(0);
            $table->timestamp('solved_at')->nullable();
            $table->primary(['user_study_plan_id', 'problem_id']);
        });

        Schema::create('user_study_plan_daily_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_study_plan_id');
            $table->foreign('user_study_plan_id')->references('id')->on('user_study_plans')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedSmallInteger('target')->default(3);
            $table->unsignedSmallInteger('completed')->default(0);
            $table->boolean('is_met')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_study_plan_daily_goals');
        Schema::dropIfExists('user_study_plan_problems');
        Schema::dropIfExists('user_study_plans');
    }
};
