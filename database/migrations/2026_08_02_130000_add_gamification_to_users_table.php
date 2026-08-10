<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('xp')->default(0)->after('ai_coins');
            $table->integer('ai_tokens')->default(25)->after('xp');
            $table->integer('level')->default(1)->after('ai_tokens');
            $table->integer('total_xp')->default(0)->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp', 'ai_tokens', 'level', 'total_xp']);
        });
    }
};
