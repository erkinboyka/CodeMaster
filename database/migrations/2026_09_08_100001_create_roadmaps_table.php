<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmaps', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('category')->default('other');
            $table->string('difficulty')->default('beginner');
            $table->integer('estimated_hours')->default(0);
            $table->boolean('is_published')->default(false);
            $table->boolean('ai_generated')->default(false);
            $table->integer('total_sections')->default(0);
            $table->integer('total_courses')->default(0);
            $table->integer('students_count')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmaps');
    }
};
