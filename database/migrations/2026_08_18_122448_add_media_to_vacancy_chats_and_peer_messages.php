<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vacancy_chats', function (Blueprint $table) {
            $table->string('message_type', 20)->default('text')->after('message_text');
            $table->string('file_url')->nullable()->after('message_type');
            $table->string('file_name')->nullable()->after('file_url');
            $table->string('file_type')->nullable()->after('file_name');
            $table->unsignedBigInteger('file_size')->nullable()->after('file_type');
        });

        Schema::table('peer_messages', function (Blueprint $table) {
            $table->string('message_type', 20)->default('text')->after('text');
            $table->string('file_url')->nullable()->after('message_type');
            $table->string('file_name')->nullable()->after('file_url');
            $table->string('file_type')->nullable()->after('file_name');
            $table->unsignedBigInteger('file_size')->nullable()->after('file_type');
        });
    }

    public function down(): void
    {
        Schema::table('vacancy_chats', function (Blueprint $table) {
            $table->dropColumn(['message_type', 'file_url', 'file_name', 'file_type', 'file_size']);
        });

        Schema::table('peer_messages', function (Blueprint $table) {
            $table->dropColumn(['message_type', 'file_url', 'file_name', 'file_type', 'file_size']);
        });
    }
};
