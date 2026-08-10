<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmap_lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('node_id')->index();
            $table->string('title');
            $table->string('video_url', 500)->nullable();
            $table->text('description')->nullable();
            $table->text('materials')->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();

            $table->foreign('node_id')->references('id')->on('roadmap_nodes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmap_lessons');
    }
};
