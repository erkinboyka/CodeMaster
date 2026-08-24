<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_list_user_favorite', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('problem_list_id');
            $table->foreign('problem_list_id')->references('id')->on('problem_lists')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['user_id', 'problem_list_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_list_user_favorite');
    }
};
