<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmap_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('roadmap_id')->index();
            $table->unsignedInteger('parent_id')->nullable()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('total_courses')->default(0);
            $table->timestamps();

            $table->foreign('roadmap_id')->references('id')->on('roadmaps')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('roadmap_sections')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmap_sections');
    }
};
