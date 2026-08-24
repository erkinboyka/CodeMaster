<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\ContestProblem;
use App\Models\ContestSubmission;
use App\Models\User;
use Illuminate\Http\Request;

class AdminContestController extends Controller
{
    public function contests(Request $request)
    {
        $query = Contest::withCount('problems');
        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('title', 'like', '%' . $search . '%', 'and');
        }
        $contests = $query->latest()->paginate(20);
        return view('admin.contests.index', compact('contests'));
    }

    public function createContest() { return view('admin.contests.create'); }

    public function storeContest(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'status' => 'required|in:draft,active,finished',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'time_limit' => 'required|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
        ]);
        $validated['created_by'] = auth()->id();
        Contest::create($validated);
        return redirect()->route('admin.contests')->with('success', 'Contest created.');
    }

    public function editContest($id) { return view('admin.contests.edit', ['contest' => Contest::findOrFail($id)]); }

    public function updateContest(Request $request, $id)
    {
        $contest = Contest::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'status' => 'required|in:draft,active,finished',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'time_limit' => 'required|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
        ]);
        $contest->update($validated);
        return redirect()->route('admin.contests')->with('success', 'Contest updated.');
    }

    public function deleteContest($id)
    {
        $contest = Contest::findOrFail($id);
        $contest->submissions()->delete();
        $contest->problems()->delete();
        $contest->delete();
        return back()->with('success', 'Contest deleted.');
    }

    // ── Contest Tasks (Problems) ──
    public function tasks($contestId)
    {
        $contest = Contest::findOrFail($contestId);
        $tasks = $contest->problems()->orderBy('order_num')->paginate(20);
        return view('admin.contests.tasks', compact('contest', 'tasks'));
    }

    public function createTask($contestId) { return view('admin.contests.task_form', ['contest' => Contest::findOrFail($contestId), 'task' => null]); }

    public function storeTask(Request $request, $contestId)
    {
        $contest = Contest::findOrFail($contestId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'points' => 'required|integer|min:1',
            'input_example' => 'nullable|string',
            'output_example' => 'nullable|string',
            'constraints' => 'nullable|string',
            'starter_code' => 'nullable|string',
            'language' => 'required|string|max:50',
            'tests_json' => 'nullable|string',
            'time_limit' => 'required|integer|min:1',
            'memory_limit' => 'required|integer|min:64',
            'order_num' => 'nullable|integer|min:0',
        ]);
        $validated['contest_id'] = $contest->id;
        if (!empty($validated['tests_json'])) $validated['tests_json'] = json_decode($validated['tests_json'], true) ?? [];
        ContestProblem::create($validated);
        return redirect()->route('admin.contests.tasks', $contest->id)->with('success', 'Task created.');
    }

    public function editTask($contestId, $taskId)
    {
        return view('admin.contests.task_form', [
            'contest' => Contest::findOrFail($contestId),
            'task' => ContestProblem::findOrFail($taskId),
        ]);
    }

    public function updateTask(Request $request, $contestId, $taskId)
    {
        $task = ContestProblem::findOrFail($taskId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'points' => 'required|integer|min:1',
            'input_example' => 'nullable|string',
            'output_example' => 'nullable|string',
            'constraints' => 'nullable|string',
            'starter_code' => 'nullable|string',
            'language' => 'required|string|max:50',
            'tests_json' => 'nullable|string',
            'time_limit' => 'required|integer|min:1',
            'memory_limit' => 'required|integer|min:64',
            'order_num' => 'nullable|integer|min:0',
        ]);
        if (!empty($validated['tests_json'])) $validated['tests_json'] = json_decode($validated['tests_json'], true) ?? [];
        $task->update($validated);
        return redirect()->route('admin.contests.tasks', $contestId)->with('success', 'Task updated.');
    }

    public function deleteTask($contestId, $taskId)
    {
        ContestProblem::findOrFail($taskId)->delete();
        return redirect()->route('admin.contests.tasks', $contestId)->with('success', 'Task deleted.');
    }

    // ── Contest Submissions / Solutions ──
    public function solutions(Request $request)
    {
        $query = ContestSubmission::with(['user', 'contest', 'problem']);
        if ($request->has('contest_id') && $request->contest_id) {
            $query->where('contest_id', $request->contest_id);
        }
        if ($request->has('task_id') && $request->task_id) {
            $query->where('task_id', $request->task_id);
        }
        $submissions = $query->latest()->paginate(20);
        $contests = Contest::orderBy('title')->get();
        return view('admin.contests.solutions', compact('submissions', 'contests'));
    }

    public function submissionDetail(Request $request)
    {
        $kind = $request->query('kind', 'contest');
        $id = $request->query('id');

        if (!in_array($kind, ['contest', 'practice'])) {
            return response()->json(['error' => 'Invalid kind'], 400);
        }

        if ($kind === 'practice') {
            $submission = \App\Models\PracticeSubmission::with(['user', 'task.lesson.course'])->findOrFail($id);
        } else {
            $submission = ContestSubmission::with(['user', 'contest', 'problem'])->findOrFail($id);
        }

        return response()->json([
            'id' => $submission->id,
            'user' => $submission->user->name ?? '-',
            'code' => substr($submission->code ?? '', 0, 100000),
            'status' => $submission->status,
            'created_at' => $submission->created_at?->format('Y-m-d H:i:s'),
            'contest' => $submission->contest->title ?? null,
            'problem' => $submission->problem->title ?? null,
        ]);
    }

    public function resetSubmission($id)
    {
        ContestSubmission::findOrFail($id)->delete();
        return back()->with('success', 'Submission deleted.');
    }

    public function resetUserContests($userId)
    {
        $user = User::findOrFail($userId);
        ContestSubmission::where('user_id', $user->id)->delete();
        return back()->with('success', 'Contest progress reset for user.');
    }

    // ── Ejudge Import ──
    public function ejudgeScan(Request $request)
    {
        $paths = $request->input('paths', ['tasks']);
        $results = [];

        foreach ($paths as $path) {
            $path = preg_replace('#[^a-zA-Z0-9_/\-]#', '', $path);
            $fullPath = base_path($path);
            $baseDir = base_path();
            if (strpos(realpath($fullPath) ?: $fullPath, $baseDir) !== 0 || !is_dir($fullPath)) continue;

            $dirs = glob($fullPath . '/*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                $problemIni = $dir . '/problem.ini';
                if (file_exists($problemIni)) {
                    $ini = parse_ini_file($problemIni);
                    $results[] = [
                        'title' => $ini['problem_name'] ?? basename($dir),
                        'path' => $dir,
                        'difficulty' => $ini['difficulty'] ?? 'medium',
                    ];
                }
            }
        }

        return response()->json(array_slice($results, 0, 30));
    }

    public function ejudgeImport(Request $request)
    {
        $request->validate([
            'paths' => 'required|array',
            'import_contest' => 'nullable|boolean',
            'import_interview' => 'nullable|boolean',
            'contest_id' => 'nullable|integer|exists:contests,id',
            'interview_category' => 'nullable|string|max:255',
            'difficulty' => 'nullable|in:easy,medium,hard',
        ]);

        $paths = $request->input('paths', ['tasks']);
        $importContest = $request->boolean('import_contest');
        $importInterview = $request->boolean('import_interview');
        $difficulty = $request->input('difficulty', 'medium');
        $addedContest = 0;
        $addedInterview = 0;
        $skipped = 0;
        $errors = [];

        foreach ($paths as $path) {
            $path = preg_replace('#[^a-zA-Z0-9_/\-]#', '', $path);
            $fullPath = base_path($path);
            $baseDir = base_path();
            if (strpos(realpath($fullPath) ?: $fullPath, $baseDir) !== 0 || !is_dir($fullPath)) { $errors[] = "Path not found: {$path}"; continue; }

            $dirs = glob($fullPath . '/*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                $problemIni = $dir . '/problem.ini';
                if (!file_exists($problemIni)) { $skipped++; continue; }

                $ini = parse_ini_file($problemIni);
                $title = $ini['problem_name'] ?? basename($dir);
                $statement = '';
                $statementFile = $dir . '/statement.html';
                if (file_exists($statementFile)) $statement = file_get_contents($statementFile);

                $tests = [];
                $testFiles = glob($dir . '/tests/*.dat');
                foreach ($testFiles as $i => $testFile) {
                    $inputFile = $testFile;
                    $outputFile = str_replace('.dat', '.ans', $testFile);
                    $tests[] = [
                        'input' => file_get_contents($inputFile) ?: '',
                        'output' => file_exists($outputFile) ? file_get_contents($outputFile) : '',
                    ];
                }

                $taskData = [
                    'title' => $title,
                    'difficulty' => $difficulty,
                    'statement' => $statement,
                    'tests_json' => $tests,
                    'time_limit_sec' => $ini['time_limit'] ?? 5,
                    'memory_limit_kb' => ($ini['memory_limit'] ?? 256) * 1024,
                ];

                try {
                    if ($importContest && $request->contest_id) {
                        ContestProblem::create(array_merge($taskData, [
                            'contest_id' => $request->contest_id,
                            'language' => 'cpp',
                            'order_num' => $addedContest,
                        ]));
                        $addedContest++;
                    }

                    if ($importInterview) {
                        \App\Models\InterviewPrepTask::create(array_merge($taskData, [
                            'category' => $request->input('interview_category', 'general'),
                            'sort_order' => $addedInterview,
                        ]));
                        $addedInterview++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "{$title}: {$e->getMessage()}";
                }
            }
        }

        return response()->json([
            'added_contest' => $addedContest,
            'added_interview' => $addedInterview,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }
}
