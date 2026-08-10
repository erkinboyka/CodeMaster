<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contest_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('contest_id')->nullable()->index();
            $table->unsignedInteger('task_id')->nullable()->index();
            $table->longText('code')->nullable();
            $table->string('status', 50)->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('lesson_practice_tasks')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_submissions');
    }
};
