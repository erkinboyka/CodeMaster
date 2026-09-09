<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancy_plus_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vacancy_plus_id')->index('vpl_id_idx');
            $table->string('locale', 5)->index('vpl_loc_idx');
            $table->text('plus_text');
            $table->timestamps();

            $table->foreign('vacancy_plus_id', 'vpl_trans_fk')->references('id')->on('vacancy_pluses')->onDelete('cascade');
            $table->unique(['vacancy_plus_id', 'locale'], 'vpl_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancy_plus_translations');
    }
};
