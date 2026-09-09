<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_id')->index();
            $table->string('locale', 5)->index();
            $table->string('title');
            $table->text('content')->nullable();
            $table->text('description')->nullable();
            $table->string('materials_title', 255)->nullable();
            $table->timestamps();

            $table->foreign('lesson_id', 'lt_fk')->references('id')->on('lessons')->onDelete('cascade');
            $table->unique(['lesson_id', 'locale'], 'lt_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_translations');
    }
};
