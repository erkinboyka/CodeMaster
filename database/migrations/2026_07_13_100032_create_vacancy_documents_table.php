<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancy_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('application_id')->index();
            $table->unsignedInteger('uploader_id')->index();
            $table->string('file_path', 500);
            $table->string('original_name', 255);
            $table->string('mime_type', 100)->nullable();
            $table->integer('size_bytes')->default(0);
            $table->timestamps();

            $table->foreign('application_id')->references('id')->on('user_applications')->onDelete('cascade');
            $table->foreign('uploader_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancy_documents');
    }
};
