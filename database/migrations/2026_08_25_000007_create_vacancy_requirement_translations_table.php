<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancy_requirement_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vacancy_requirement_id')->index();
            $table->string('locale', 5)->index();
            $table->text('requirement_text');
            $table->timestamps();

            $table->foreign('vacancy_requirement_id', 'vrq_trans_fk')->references('id')->on('vacancy_requirements')->onDelete('cascade');
            $table->unique(['vacancy_requirement_id', 'locale'], 'vrq_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancy_requirement_translations');
    }
};
