<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancy_chats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('application_id')->index();
            $table->unsignedInteger('sender_id')->index();
            $table->text('message_text');
            $table->timestamps();

            $table->foreign('application_id')->references('id')->on('user_applications')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancy_chats');
    }
};
