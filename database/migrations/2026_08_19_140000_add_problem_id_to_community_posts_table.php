<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('problem_id')->nullable()->after('user_id');
            $table->foreign('problem_id')->references('id')->on('problems')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropForeign(['problem_id']);
            $table->dropColumn('problem_id');
        });
    }
};
