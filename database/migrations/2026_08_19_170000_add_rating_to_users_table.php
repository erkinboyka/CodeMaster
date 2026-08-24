<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('rating')->default(1200)->after('xp');
            $table->unsignedInteger('rating_peak')->default(1200)->after('rating');
        });

        Schema::create('rating_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedInteger('contest_id')->nullable();
            $table->foreign('contest_id')->references('id')->on('contests')->nullOnDelete();
            $table->unsignedInteger('rating_before')->default(1200);
            $table->unsignedInteger('rating_after')->default(1200);
            $table->integer('rating_change')->default(0);
            $table->unsignedInteger('rank_position')->nullable();
            $table->unsignedInteger('participants_count')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_history');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rating', 'rating_peak']);
        });
    }
};
