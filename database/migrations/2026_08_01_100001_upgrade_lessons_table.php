<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->text('description')->nullable()->after('content');
            $table->string('audio_url', 500)->nullable()->after('video_url');
            $table->string('presentation_url', 500)->nullable()->after('audio_url');
            $table->integer('duration_minutes')->default(15)->after('order_num');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium')->after('duration_minutes');
            $table->string('module', 255)->nullable()->after('difficulty')->comment('Module/group within course');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['description', 'audio_url', 'presentation_url', 'duration_minutes', 'difficulty', 'module']);
        });
    }
};
