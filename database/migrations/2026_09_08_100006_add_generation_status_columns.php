<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('generation_status')->default('pending')->after('ai_generated');
            $table->integer('generation_progress')->default(0)->after('generation_status');
        });

        Schema::table('course_steps', function (Blueprint $table) {
            $table->string('generation_status')->default('pending')->after('heirs');
            $table->integer('generation_progress')->default(0)->after('generation_status');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['generation_status', 'generation_progress']);
        });

        Schema::table('course_steps', function (Blueprint $table) {
            $table->dropColumn(['generation_status', 'generation_progress']);
        });
    }
};
