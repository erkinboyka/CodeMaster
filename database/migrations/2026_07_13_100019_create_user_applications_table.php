<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('vacancy_id')->index();
            $table->enum('status', ['applied', 'interview', 'offer', 'rejected'])->default('applied');
            $table->enum('employment_status', ['pending', 'successful', 'unsuccessful'])->default('pending');
            $table->timestamp('employment_updated_at')->nullable();
            $table->timestamp('applied_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vacancy_id')->references('id')->on('vacancies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_applications');
    }
};
