<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancy_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vacancy_id')->index();
            $table->string('locale', 5)->index();
            $table->string('title');
            $table->string('company')->nullable();
            $table->text('description')->nullable();
            $table->text('company_description')->nullable();
            $table->timestamps();

            $table->foreign('vacancy_id', 'vt_fk')->references('id')->on('vacancies')->onDelete('cascade');
            $table->unique(['vacancy_id', 'locale'], 'vac_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancy_translations');
    }
};
