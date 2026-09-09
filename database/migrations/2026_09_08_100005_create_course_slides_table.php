<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_slides', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id')->index();
            $table->unsignedInteger('step_id')->index();
            $table->string('title');
            $table->longText('content');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('step_id')->references('id')->on('course_steps')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_slides');
    }
};
