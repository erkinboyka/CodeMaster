<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('instructor');
            $table->text('description')->nullable();
            $table->enum('category', ['frontend', 'backend', 'design', 'devops', 'other'])->default('frontend');
            $table->enum('level', ['Начальный', 'Средний', 'Продвинутый'])->default('Начальный');
            $table->integer('progress')->default(0);
            $table->string('image_url', 500)->nullable();
            $table->string('materials_title', 255)->nullable();
            $table->string('materials_url', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
