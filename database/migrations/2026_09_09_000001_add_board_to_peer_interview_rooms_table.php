<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peer_interview_rooms', function (Blueprint $table) {
            $table->longText('board_content')->nullable()->after('code_language');
        });
    }

    public function down(): void
    {
        Schema::table('peer_interview_rooms', function (Blueprint $table) {
            $table->dropColumn(['board_content']);
        });
    }
};
