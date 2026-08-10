<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_practice_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_id')->index();
            $table->string('language', 50);
            $table->string('title', 255);
            $table->text('prompt')->nullable();
            $table->text('starter_code')->nullable();
            $table->longText('tests_json')->nullable();
            $table->tinyInteger('is_required')->default(1);
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_practice_tasks');
    }
};
