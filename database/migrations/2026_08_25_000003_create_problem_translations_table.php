<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('problem_id')->index();
            $table->string('locale', 5)->index();
            $table->string('title');
            $table->text('description');
            $table->string('input_example')->nullable();
            $table->string('output_example')->nullable();
            $table->text('constraints')->nullable();
            $table->timestamps();

            $table->foreign('problem_id', 'pt_fk')->references('id')->on('problems')->onDelete('cascade');
            $table->unique(['problem_id', 'locale'], 'pt_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_translations');
    }
};
