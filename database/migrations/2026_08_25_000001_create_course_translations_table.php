<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id')->index();
            $table->string('locale', 5)->index();
            $table->string('title');
            $table->string('instructor')->nullable();
            $table->text('description')->nullable();
            $table->string('materials_title', 255)->nullable();
            $table->timestamps();

            $table->foreign('course_id', 'ct_fk')->references('id')->on('courses')->onDelete('cascade');
            $table->unique(['course_id', 'locale'], 'ct_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_translations');
    }
};
