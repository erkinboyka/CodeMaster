<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Таблица была создана как `course_test_matching` (ед.ч.),
     * а модель CourseTestMatching и весь код используют `course_test_matchings`.
     * Переименовываем с сохранением данных; если таблицы нет — создаём заново.
     */
    public function up(): void
    {
        if (Schema::hasTable('course_test_matching') && !Schema::hasTable('course_test_matchings')) {
            Schema::rename('course_test_matching', 'course_test_matchings');
        } elseif (!Schema::hasTable('course_test_matchings')) {
            Schema::create('course_test_matchings', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('test_id');
                $table->string('list1_item');
                $table->string('list2_item');
                $table->timestamps();

                $table->foreign('test_id')->references('id')->on('course_step_tests')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('course_test_matchings') && !Schema::hasTable('course_test_matching')) {
            Schema::rename('course_test_matchings', 'course_test_matching');
        }
    }
};
