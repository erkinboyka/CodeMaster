<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id')->index();
            $table->string('title');
            $table->enum('type', ['video', 'article', 'quiz'])->default('video');
            $table->text('content')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->string('materials_title', 255)->nullable();
            $table->string('materials_url', 500)->nullable();
            $table->boolean('completed')->default(false);
            $table->integer('order_num')->default(0);
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
