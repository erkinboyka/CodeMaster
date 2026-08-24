<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peer_interview_rooms', function (Blueprint $table) {
            $table->tinyInteger('total_score')->nullable()->after('ended_at');
            $table->tinyInteger('max_score')->nullable()->after('total_score');
            $table->text('summary')->nullable()->after('max_score');
        });
    }

    public function down(): void
    {
        Schema::table('peer_interview_rooms', function (Blueprint $table) {
            $table->dropColumn(['total_score', 'max_score', 'summary']);
        });
    }
};
