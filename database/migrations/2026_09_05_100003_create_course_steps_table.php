<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->enum('type', ['parent', 'heir'])->default('parent');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('experience')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->json('heirs')->nullable();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('course_steps')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_steps');
    }
};
