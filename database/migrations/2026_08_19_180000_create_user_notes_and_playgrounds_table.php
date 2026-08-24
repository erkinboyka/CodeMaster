<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('problem_id')->nullable();
            $table->foreign('problem_id')->references('id')->on('problems')->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();
        });

        Schema::create('user_playgrounds', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('language', 30)->default('javascript');
            $table->text('code');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_playgrounds');
        Schema::dropIfExists('user_notes');
    }
};
