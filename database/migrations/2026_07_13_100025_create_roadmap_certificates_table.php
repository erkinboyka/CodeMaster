<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmap_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('node_id')->index();
            $table->string('cert_hash', 100);
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();

            $table->unique('cert_hash');
            $table->unique(['user_id', 'node_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('node_id')->references('id')->on('roadmap_nodes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmap_certificates');
    }
};
