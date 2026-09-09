<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_skills', function (Blueprint $table) {
            $table->string('skill')->after('skill_name')->nullable();
            $table->integer('score')->after('skill_level')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('course_skills', function (Blueprint $table) {
            $table->dropColumn(['skill', 'score', 'created_at', 'updated_at']);
        });
    }
};
