<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peer_interview_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('room_code', 8)->unique();
            $table->unsignedInteger('host_id')->index();
            $table->unsignedInteger('guest_id')->nullable()->index();
            $table->text('host_sdp')->nullable();
            $table->text('guest_sdp')->nullable();
            $table->json('host_ice')->nullable();
            $table->json('guest_ice')->nullable();
            $table->enum('status', ['waiting', 'connected', 'ended'])->default('waiting');
            $table->string('host_name')->nullable();
            $table->string('guest_name')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index('room_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_interview_rooms');
    }
};
