<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('github')->nullable()->after('location');
            $table->string('linkedin')->nullable()->after('github');
            $table->string('website')->nullable()->after('linkedin');
        });

        Schema::table('user_experience', function (Blueprint $table) {
            $table->boolean('is_current')->default(false)->after('end_date');
        });

        Schema::table('user_education', function (Blueprint $table) {
            $table->string('field')->nullable()->after('degree');
        });

        Schema::table('user_portfolio', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->string('url', 500)->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['github', 'linkedin', 'website']);
        });

        Schema::table('user_experience', function (Blueprint $table) {
            $table->dropColumn('is_current');
        });

        Schema::table('user_education', function (Blueprint $table) {
            $table->dropColumn('field');
        });

        Schema::table('user_portfolio', function (Blueprint $table) {
            $table->dropColumn(['description', 'url']);
        });
    }
};
