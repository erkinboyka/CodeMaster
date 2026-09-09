<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freelance_workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('room_code', 8)->unique();
            $table->unsignedBigInteger('owner_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('topic', 150)->nullable();
            $table->enum('status', ['waiting','active','completed'])->default('waiting');
            $table->text('whiteboard_data')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        Schema::create('freelance_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_online')->default(false);
            $table->boolean('mic_on')->default(false);
            $table->boolean('cam_on')->default(false);
            $table->timestamps();
        });

        Schema::create('freelance_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('user_id');
            $table->text('message');
            $table->enum('type', ['text','system','todo'])->default('text');
            $table->timestamps();
        });

        Schema::create('freelance_todos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('text', 255);
            $table->boolean('done')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freelance_todos');
        Schema::dropIfExists('freelance_messages');
        Schema::dropIfExists('freelance_participants');
        Schema::dropIfExists('freelance_workspaces');
    }
};
