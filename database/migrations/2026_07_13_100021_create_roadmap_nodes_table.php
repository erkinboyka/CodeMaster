<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmap_nodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('roadmap_title', 255)->default('Основной');
            $table->string('topic', 255)->nullable();
            $table->text('materials')->nullable();
            $table->integer('x')->default(0);
            $table->integer('y')->default(0);
            $table->text('deps')->nullable();
            $table->tinyInteger('is_exam')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmap_nodes');
    }
};
