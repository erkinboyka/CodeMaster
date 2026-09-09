<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('news_id')->index();
            $table->string('locale', 5)->index();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->timestamps();

            $table->foreign('news_id', 'nt_fk')->references('id')->on('news')->onDelete('cascade');
            $table->unique(['news_id', 'locale'], 'nt_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_translations');
    }
};
