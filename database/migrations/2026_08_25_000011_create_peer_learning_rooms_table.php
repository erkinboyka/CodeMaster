<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peer_learning_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_code', 8)->unique();
            $table->unsignedInteger('host_id');
            $table->unsignedInteger('guest_id')->nullable();
            $table->string('host_name');
            $table->string('guest_name')->nullable();
            $table->enum('status', ['waiting', 'active', 'reviewing', 'completed'])->default('waiting');
            $table->unsignedInteger('problem_id')->nullable();
            $table->longText('host_code')->nullable();
            $table->longText('guest_code')->nullable();
            $table->string('host_language', 30)->default('python');
            $table->string('guest_language', 30)->default('python');
            $table->integer('host_time_ms')->default(0);
            $table->integer('guest_time_ms')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('review_started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['host_id', 'status']);
            $table->index(['guest_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_learning_rooms');
    }
};
