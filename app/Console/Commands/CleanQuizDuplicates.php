<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\RoadmapQuizQuestion;
use App\Models\RoadmapLesson;

class CleanQuizDuplicates extends Command
{
    protected $signature = 'clean:quiz-duplicates';
    protected $description = 'Remove duplicate quiz questions and lessons';

    public function handle()
    {
        $this->info('Cleaning duplicate quiz questions...');

        $questions = RoadmapQuizQuestion::orderBy('id')->get();
        $seen = [];
        $deleteIds = [];

        foreach ($questions as $q) {
            $key = $q->node_id . '|' . $q->question . '|' . $q->correct_answer;
            if (isset($seen[$key])) {
                $deleteIds[] = $q->id;
            } else {
                $seen[$key] = $q->id;
            }
        }

        $this->info('Found ' . count($deleteIds) . ' duplicate quiz questions to remove.');
        
        if (!empty($deleteIds)) {
            // Delete in chunks to avoid memory issues
            $chunks = array_chunk($deleteIds, 500);
            foreach ($chunks as $chunk) {
                RoadmapQuizQuestion::whereIn('id', $chunk)->delete();
            }
        }

        $remaining = RoadmapQuizQuestion::count();
        $this->info('Quiz questions remaining: ' . $remaining);

        // Clean duplicate lessons too
        $this->info('Cleaning duplicate lessons...');
        $lessons = RoadmapLesson::orderBy('id')->get();
        $seenL = [];
        $deleteL = [];

        foreach ($lessons as $l) {
            $key = $l->node_id . '|' . $l->title;
            if (isset($seenL[$key])) {
                $deleteL[] = $l->id;
            } else {
                $seenL[$key] = $l->id;
            }
        }

        $this->info('Found ' . count($deleteL) . ' duplicate lessons to remove.');

        if (!empty($deleteL)) {
            $chunks = array_chunk($deleteL, 500);
            foreach ($chunks as $chunk) {
                RoadmapLesson::whereIn('id', $chunk)->delete();
            }
        }

        $remainingL = RoadmapLesson::count();
        $this->info('Lessons remaining: ' . $remainingL);

        // Show quiz count per roadmap
        $this->info('Quiz questions per roadmap:');
        $rows = DB::table('roadmap_quiz_questions')
            ->join('roadmap_nodes', 'roadmap_nodes.id', '=', 'roadmap_quiz_questions.node_id')
            ->select('roadmap_nodes.roadmap_title', DB::raw('count(*) as cnt'))
            ->groupBy('roadmap_nodes.roadmap_title')
            ->get();
        
        foreach ($rows as $row) {
            $this->info('  ' . $row->roadmap_title . ': ' . $row->cnt . ' questions');
        }

        // Show nodes without quiz questions
        $this->info('Nodes without quiz questions:');
        $nodesWithout = DB::table('roadmap_nodes')
            ->leftJoin('roadmap_quiz_questions', 'roadmap_nodes.id', '=', 'roadmap_quiz_questions.node_id')
            ->whereNull('roadmap_quiz_questions.id')
            ->select('roadmap_nodes.id', 'roadmap_nodes.title', 'roadmap_nodes.roadmap_title', 'roadmap_nodes.is_exam')
            ->get();

        foreach ($nodesWithout as $n) {
            $examTag = $n->is_exam ? ' [EXAM]' : '';
            $this->info('  [' . $n->roadmap_title . '] ' . $n->title . ' (id=' . $n->id . ')' . $examTag);
        }

        return 0;
    }
}
