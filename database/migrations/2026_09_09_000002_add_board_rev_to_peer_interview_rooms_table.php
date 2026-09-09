<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peer_interview_rooms', function (Blueprint $table) {
            $table->unsignedInteger('board_rev')->default(0)->after('board_content');
        });
    }

    public function down(): void
    {
        Schema::table('peer_interview_rooms', function (Blueprint $table) {
            $table->dropColumn(['board_rev']);
        });
    }
};
