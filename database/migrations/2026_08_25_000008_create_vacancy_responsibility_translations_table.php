<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancy_responsibility_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vacancy_responsibility_id')->index('vres_id_idx');
            $table->string('locale', 5)->index('vres_loc_idx');
            $table->text('responsibility_text');
            $table->timestamps();

            $table->foreign('vacancy_responsibility_id', 'vres_trans_fk')->references('id')->on('vacancy_responsibilities')->onDelete('cascade');
            $table->unique(['vacancy_responsibility_id', 'locale'], 'vres_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancy_responsibility_translations');
    }
};
