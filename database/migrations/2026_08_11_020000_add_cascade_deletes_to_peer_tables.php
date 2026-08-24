<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peer_tasks', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('peer_interview_rooms')->onDelete('cascade');
        });

        Schema::table('peer_messages', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('peer_interview_rooms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('peer_tasks', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
        });

        Schema::table('peer_messages', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['user_id']);
        });
    }
};
