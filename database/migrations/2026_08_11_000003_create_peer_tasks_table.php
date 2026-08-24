<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peer_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('room_id');
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->enum('type', ['code', 'theory', 'system_design']);
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->text('starter_code')->nullable();
            $table->string('language', 30)->nullable();
            $table->enum('status', ['active', 'in_progress', 'done', 'skipped', 'review'])->default('active');
            $table->text('solution')->nullable();
            $table->tinyInteger('score')->nullable();
            $table->text('feedback')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->index('room_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_tasks');
    }
};
