<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peer_interview_rooms', function (Blueprint $table) {
            $table->json('tasks')->nullable()->after('guest_name');
            $table->text('code_content')->nullable()->after('tasks');
            $table->string('code_language', 30)->nullable()->after('code_content')->default('python');
        });
    }

    public function down(): void
    {
        Schema::table('peer_interview_rooms', function (Blueprint $table) {
            $table->dropColumn(['tasks', 'code_content', 'code_language']);
        });
    }
};
