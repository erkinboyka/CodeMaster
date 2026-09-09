<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $locale = 'ru';

        // course_translations - skip existing
        $existing = DB::table('course_translations')->where('locale', $locale)->pluck('course_id')->toArray();
        DB::table('courses')->whereNotIn('id', $existing)->orderBy('id')->chunk(500, function ($rows) use ($now, $locale) {
            $rows->each(function ($c) use ($now, $locale) {
                DB::table('course_translations')->insert([
                    'course_id' => $c->id, 'locale' => $locale,
                    'title' => $c->title, 'instructor' => $c->instructor,
                    'description' => $c->description, 'materials_title' => $c->materials_title,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            });
        });

        // lesson_translations
        $existing = DB::table('lesson_translations')->where('locale', $locale)->pluck('lesson_id')->toArray();
        DB::table('lessons')->whereNotIn('id', $existing)->orderBy('id')->chunk(500, function ($rows) use ($now, $locale) {
            $rows->each(function ($l) use ($now, $locale) {
                DB::table('lesson_translations')->insert([
                    'lesson_id' => $l->id, 'locale' => $locale,
                    'title' => $l->title, 'content' => $l->content,
                    'description' => $l->description, 'materials_title' => $l->materials_title,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            });
        });

        // problem_translations
        $existing = DB::table('problem_translations')->where('locale', $locale)->pluck('problem_id')->toArray();
        DB::table('problems')->whereNotIn('id', $existing)->orderBy('id')->chunk(500, function ($rows) use ($now, $locale) {
            $rows->each(function ($p) use ($now, $locale) {
                DB::table('problem_translations')->insert([
                    'problem_id' => $p->id, 'locale' => $locale,
                    'title' => $p->title, 'description' => $p->description,
                    'input_example' => $p->input_example, 'output_example' => $p->output_example,
                    'constraints' => $p->constraints,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            });
        });

        // vacancy_translations
        $existing = DB::table('vacancy_translations')->where('locale', $locale)->pluck('vacancy_id')->toArray();
        DB::table('vacancies')->whereNotIn('id', $existing)->orderBy('id')->chunk(500, function ($rows) use ($now, $locale) {
            $rows->each(function ($v) use ($now, $locale) {
                DB::table('vacancy_translations')->insert([
                    'vacancy_id' => $v->id, 'locale' => $locale,
                    'title' => $v->title, 'company' => $v->company,
                    'description' => $v->description, 'company_description' => $v->company_description,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            });
        });

        // news_translations
        $existing = DB::table('news_translations')->where('locale', $locale)->pluck('news_id')->toArray();
        DB::table('news')->whereNotIn('id', $existing)->orderBy('id')->chunk(500, function ($rows) use ($now, $locale) {
            $rows->each(function ($n) use ($now, $locale) {
                DB::table('news_translations')->insert([
                    'news_id' => $n->id, 'locale' => $locale,
                    'title' => $n->title, 'excerpt' => $n->excerpt,
                    'content' => $n->content,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            });
        });

        // quiz_question_translations
        $existing = DB::table('quiz_question_translations')->where('locale', $locale)->pluck('quiz_question_id')->toArray();
        DB::table('quiz_questions')->whereNotIn('id', $existing)->orderBy('id')->chunk(500, function ($rows) use ($now, $locale) {
            $rows->each(function ($q) use ($now, $locale) {
                DB::table('quiz_question_translations')->insert([
                    'quiz_question_id' => $q->id, 'locale' => $locale,
                    'question_text' => $q->question_text, 'hint' => $q->hint,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            });
        });

        // vacancy_requirement_translations
        $existing = DB::table('vacancy_requirement_translations')->where('locale', $locale)->pluck('vacancy_requirement_id')->toArray();
        DB::table('vacancy_requirements')->whereNotIn('id', $existing)->orderBy('id')->chunk(500, function ($rows) use ($now, $locale) {
            $rows->each(function ($r) use ($now, $locale) {
                DB::table('vacancy_requirement_translations')->insert([
                    'vacancy_requirement_id' => $r->id, 'locale' => $locale,
                    'requirement_text' => $r->requirement_text,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            });
        });

        // vacancy_responsibility_translations
        $existing = DB::table('vacancy_responsibility_translations')->where('locale', $locale)->pluck('vacancy_responsibility_id')->toArray();
        DB::table('vacancy_responsibilities')->whereNotIn('id', $existing)->orderBy('id')->chunk(500, function ($rows) use ($now, $locale) {
            $rows->each(function ($r) use ($now, $locale) {
                DB::table('vacancy_responsibility_translations')->insert([
                    'vacancy_responsibility_id' => $r->id, 'locale' => $locale,
                    'responsibility_text' => $r->responsibility_text,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            });
        });

        // vacancy_plus_translations
        $existing = DB::table('vacancy_plus_translations')->where('locale', $locale)->pluck('vacancy_plus_id')->toArray();
        DB::table('vacancy_pluses')->whereNotIn('id', $existing)->orderBy('id')->chunk(500, function ($rows) use ($now, $locale) {
            $rows->each(function ($p) use ($now, $locale) {
                DB::table('vacancy_plus_translations')->insert([
                    'vacancy_plus_id' => $p->id, 'locale' => $locale,
                    'plus_text' => $p->plus_text,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            });
        });
    }

    public function down(): void
    {
        DB::table('vacancy_plus_translations')->where('locale', 'ru')->delete();
        DB::table('vacancy_responsibility_translations')->where('locale', 'ru')->delete();
        DB::table('vacancy_requirement_translations')->where('locale', 'ru')->delete();
        DB::table('quiz_question_translations')->where('locale', 'ru')->delete();
        DB::table('news_translations')->where('locale', 'ru')->delete();
        DB::table('vacancy_translations')->where('locale', 'ru')->delete();
        DB::table('problem_translations')->where('locale', 'ru')->delete();
        DB::table('lesson_translations')->where('locale', 'ru')->delete();
        DB::table('course_translations')->where('locale', 'ru')->delete();
    }
};
