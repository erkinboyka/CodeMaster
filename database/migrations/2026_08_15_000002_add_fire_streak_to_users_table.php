<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'streak_count')) {
                $table->integer('streak_count')->default(0)->after('total_xp');
            }
            if (!Schema::hasColumn('users', 'longest_streak')) {
                $table->integer('longest_streak')->default(0)->after('streak_count');
            }
            if (!Schema::hasColumn('users', 'last_active_date')) {
                $table->date('last_active_date')->nullable()->after('longest_streak');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['streak_count', 'longest_streak', 'last_active_date']);
        });
    }
};
