<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->string('source')->nullable()->after('is_premium');
            $table->string('source_url')->nullable()->after('source');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn(['source', 'source_url']);
        });
    }
};
