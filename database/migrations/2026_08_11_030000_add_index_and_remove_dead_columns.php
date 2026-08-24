<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasIndex('notifications', 'notifications_is_read_index')) {
                $table->index('is_read');
            }
        });

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'progress')) {
                $table->dropColumn('progress');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // ai_coins is still used by seeders and controllers - keep it
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['is_read']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->integer('progress')->default(0);
        });

        Schema::table('users', function (Blueprint $table) {
            // ai_coins kept - no action needed
        });
    }
};
