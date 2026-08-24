<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterviewPrepTask;
use Illuminate\Http\Request;

class AdminInterviewPrepController extends Controller
{
    public function index(Request $request)
    {
        $query = InterviewPrepTask::query();
        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('title', 'like', '%' . $search . '%', 'and');
        }
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        $tasks = $query->orderBy('sort_order')->paginate(20);
        return view('admin.interview_prep.index', compact('tasks'));
    }

    public function create() { return view('admin.interview_prep.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'difficulty' => 'required|in:easy,medium,hard',
            'statement' => 'nullable|string',
            'input_spec' => 'nullable|string',
            'output_spec' => 'nullable|string',
            'starter_cpp' => 'nullable|string',
            'starter_python' => 'nullable|string',
            'starter_c' => 'nullable|string',
            'starter_csharp' => 'nullable|string',
            'starter_java' => 'nullable|string',
            'tests_json' => 'nullable|string',
            'time_limit_sec' => 'required|integer|min:1',
            'memory_limit_kb' => 'required|integer|min:64',
            'hints' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        if (!empty($validated['tests_json'])) $validated['tests_json'] = json_decode($validated['tests_json'], true) ?? [];
        InterviewPrepTask::create($validated);
        return redirect()->route('admin.interview-prep')->with('success', 'Task created.');
    }

    public function edit($id) { return view('admin.interview_prep.edit', ['task' => InterviewPrepTask::findOrFail($id)]); }

    public function update(Request $request, $id)
    {
        $task = InterviewPrepTask::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'difficulty' => 'required|in:easy,medium,hard',
            'statement' => 'nullable|string',
            'input_spec' => 'nullable|string',
            'output_spec' => 'nullable|string',
            'starter_cpp' => 'nullable|string',
            'starter_python' => 'nullable|string',
            'starter_c' => 'nullable|string',
            'starter_csharp' => 'nullable|string',
            'starter_java' => 'nullable|string',
            'tests_json' => 'nullable|string',
            'time_limit_sec' => 'required|integer|min:1',
            'memory_limit_kb' => 'required|integer|min:64',
            'hints' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        if (!empty($validated['tests_json'])) $validated['tests_json'] = json_decode($validated['tests_json'], true) ?? [];
        $task->update($validated);
        return redirect()->route('admin.interview-prep')->with('success', 'Task updated.');
    }

    public function destroy($id)
    {
        InterviewPrepTask::findOrFail($id)->delete();
        return back()->with('success', 'Task deleted.');
    }

    public function importFolders(Request $request)
    {
        $request->validate([
            'paths' => 'required|array',
            'category' => 'nullable|string|max:255',
            'difficulty' => 'nullable|in:easy,medium,hard',
        ]);

        $paths = $request->input('paths', ['tasks']);
        $category = $request->input('category', 'general');
        $difficulty = $request->input('difficulty', 'medium');
        $added = 0;
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

                $exists = InterviewPrepTask::where('title', $title)->where('category', $category)->exists();
                if ($exists) { $skipped++; continue; }

                $statement = '';
                $statementFile = $dir . '/statement.html';
                if (file_exists($statementFile)) $statement = file_get_contents($statementFile);

                $tests = [];
                $testFiles = glob($dir . '/tests/*.dat');
                foreach ($testFiles as $testFile) {
                    $outputFile = str_replace('.dat', '.ans', $testFile);
                    $tests[] = [
                        'input' => file_get_contents($testFile) ?: '',
                        'output' => file_exists($outputFile) ? file_get_contents($outputFile) : '',
                    ];
                }

                try {
                    InterviewPrepTask::create([
                        'title' => $title,
                        'category' => $category,
                        'difficulty' => $difficulty,
                        'statement' => $statement,
                        'tests_json' => $tests,
                        'time_limit_sec' => $ini['time_limit'] ?? 5,
                        'memory_limit_kb' => ($ini['memory_limit'] ?? 256) * 1024,
                        'sort_order' => $added,
                    ]);
                    $added++;
                } catch (\Exception $e) {
                    $errors[] = "{$title}: {$e->getMessage()}";
                }
            }
        }

        return response()->json(['added' => $added, 'skipped' => $skipped, 'errors' => $errors]);
    }
}
