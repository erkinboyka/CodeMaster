<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $hasColumn = Schema::hasColumn('roadmap_nodes', 'parent_id');
        if (!$hasColumn) {
            Schema::table('roadmap_nodes', function (Blueprint $table) {
                $table->unsignedInteger('parent_id')->nullable()->after('roadmap_title');
            });
        }

        $fkExists = DB::select("SELECT 1 FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'roadmap_nodes' AND COLUMN_NAME = 'parent_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
        if (empty($fkExists)) {
            Schema::table('roadmap_nodes', function (Blueprint $table) {
                $table->foreign('parent_id', 'rm_parent_fk')->references('id')->on('roadmap_nodes')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('roadmap_nodes', function (Blueprint $table) {
            $table->dropForeign('rm_parent_fk');
            $table->dropColumn('parent_id');
        });
    }
};
