<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmap_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('roadmap_id')->index();
            $table->unsignedInteger('section_id')->index();
            $table->unsignedInteger('course_id')->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('roadmap_id')->references('id')->on('roadmaps')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('roadmap_sections')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmap_courses');
    }
};
