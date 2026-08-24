<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peer_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('room_id');
            $table->unsignedInteger('user_id');
            $table->text('text');
            $table->timestamp('created_at')->nullable();

            $table->index('room_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_messages');
    }
};
