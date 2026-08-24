<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_lists', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('fa-list-ol');
            $table->string('color', 20)->default('var(--accent)');
            $table->unsignedInteger('problems_count')->default(0);
            $table->timestamps();
        });

        Schema::create('problem_list_problem', function (Blueprint $table) {
            $table->unsignedBigInteger('problem_list_id');
            $table->foreign('problem_list_id')->references('id')->on('problem_lists')->cascadeOnDelete();
            $table->unsignedBigInteger('problem_id');
            $table->foreign('problem_id')->references('id')->on('problems')->cascadeOnDelete();
            $table->unsignedSmallInteger('order_num')->default(0);
            $table->primary(['problem_list_id', 'problem_id']);
        });

        Schema::create('problem_list_user', function (Blueprint $table) {
            $table->unsignedBigInteger('problem_list_id');
            $table->foreign('problem_list_id')->references('id')->on('problem_lists')->cascadeOnDelete();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('solved_count')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->primary(['problem_list_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_list_user');
        Schema::dropIfExists('problem_list_problem');
        Schema::dropIfExists('problem_lists');
    }
};
