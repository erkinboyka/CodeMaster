<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Vacancy;
use App\Models\CourseExam;
use App\Models\LessonQuiz;
use App\Models\LessonPracticeTask;
use App\Models\RoadmapNode;
use App\Models\RoadmapLesson;
use App\Models\RoadmapQuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ── Dashboard ──
    public function index()
    {
        $totalUsers = User::count();
        $totalCourses = Course::count();
        $totalVacancies = Vacancy::count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $blockedUsers = User::where('is_blocked', true)->count();

        return view('admin.dashboard', compact('totalUsers', 'totalCourses', 'totalVacancies', 'newUsersToday', 'blockedUsers'));
    }

    // ── Users ──
    public function users(Request $request)
    {
        $query = User::query();
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        $users = $query->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function createUser() { return view('admin.users.create'); }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:seeker,recruiter,admin',
        ]);
        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        User::create($validated);
        return redirect()->route('admin.users')->with('success', 'User created.');
    }

    public function editUser($id) { return view('admin.users.edit', ['user' => User::findOrFail($id)]); }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => 'sometimes|in:seeker,recruiter,admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        if (empty($validated['password'])) unset($validated['password']);
        else $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user->update($validated);
        return redirect()->route('admin.users')->with('success', 'User updated.');
    }

    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_blocked' => !$user->is_blocked]);
        return back()->with('success', 'User ' . ($user->is_blocked ? 'blocked' : 'unblocked') . '.');
    }

    public function deleteUser($id) { User::findOrFail($id)->delete(); return back()->with('success', 'User deleted.'); }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate(['role' => 'required|in:seeker,recruiter,admin']);
        $user->update(['role' => $validated['role']]);
        return back()->with('success', 'Role updated.');
    }

    // ── Courses ──
    public function courses(Request $request)
    {
        $query = Course::withCount('lessons');
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $courses = $query->latest()->paginate(20);
        return view('admin.courses', compact('courses'));
    }

    public function createCourse() { return view('admin.courses.create'); }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:frontend,backend,design,devops,other',
            'level' => 'required|in:Начальный,Средний,Продвинутый',
            'image_url' => 'nullable|string|max:500',
            'materials_title' => 'nullable|string|max:255',
            'materials_url' => 'nullable|string|max:500',
        ]);
        Course::create($validated);
        return redirect()->route('admin.courses')->with('success', 'Course created.');
    }

    public function editCourse($id)
    {
        $course = Course::with(['lessons' => function ($q) {
            $q->orderBy('order_num');
        }, 'lessons.lessonQuizzes', 'lessons.practiceTasks', 'exams'])->findOrFail($id);
        return view('admin.courses.edit', ['course' => $course]);
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:frontend,backend,design,devops,other',
            'level' => 'required|in:Начальный,Средний,Продвинутый',
            'image_url' => 'nullable|string|max:500',
            'materials_title' => 'nullable|string|max:255',
            'materials_url' => 'nullable|string|max:500',
        ]);
        $course->update($validated);
        return redirect()->route('admin.courses')->with('success', 'Course updated.');
    }

    public function deleteCourse($id) { Course::findOrFail($id)->delete(); return back()->with('success', 'Course deleted.'); }

    // ── Lessons ──
    public function lessons(Request $request)
    {
        $query = Lesson::with('course');
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $lessons = $query->latest()->paginate(20);
        return view('admin.lessons', compact('lessons'));
    }

    public function createLesson($courseId) { return view('admin.lessons.create', ['course' => Course::findOrFail($courseId)]); }

    public function storeLesson(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article,quiz',
            'content' => 'nullable|string',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:500',
            'audio_url' => 'nullable|string|max:500',
            'presentation_url' => 'nullable|string|max:500',
            'materials_title' => 'nullable|string|max:255',
            'materials_url' => 'nullable|string|max:500',
            'order_num' => 'required|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'module' => 'nullable|string|max:255',
        ]);
        $validated['course_id'] = $course->id;
        Lesson::create($validated);
        return redirect()->route('admin.courses.edit', $course->id)->with('success', 'Lesson created.');
    }

    public function editLesson($id) { return view('admin.lessons.edit', ['lesson' => Lesson::findOrFail($id)]); }

    public function updateLesson(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article,quiz',
            'content' => 'nullable|string',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:500',
            'audio_url' => 'nullable|string|max:500',
            'presentation_url' => 'nullable|string|max:500',
            'materials_title' => 'nullable|string|max:255',
            'materials_url' => 'nullable|string|max:500',
            'order_num' => 'required|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'module' => 'nullable|string|max:255',
        ]);
        $lesson->update($validated);
        return redirect()->route('admin.courses.edit', $lesson->course_id)->with('success', 'Lesson updated.');
    }

    public function deleteLesson($id) { $l = Lesson::findOrFail($id); $cid = $l->course_id; $l->delete(); return redirect()->route('admin.courses.edit', $cid)->with('success', 'Lesson deleted.'); }

    // ── Vacancies ──
    public function vacancies(Request $request)
    {
        $query = Vacancy::with('vacancySkills');
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $vacancies = $query->latest()->paginate(20);
        return view('admin.vacancies', compact('vacancies'));
    }

    public function createVacancy() { return view('admin.vacancies.create'); }

    public function storeVacancy(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|in:remote,office,hybrid',
            'salary_min' => 'required|integer|min:0',
            'salary_max' => 'required|integer|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string|max:3',
            'description' => 'required|string',
            'company_description' => 'nullable|string',
        ]);
        Vacancy::create(array_merge($validated, [
            'owner_id' => Auth::id(),
            'salary_currency' => $validated['salary_currency'] ?? 'TJS',
        ]));
        return redirect()->route('admin.vacancies')->with('success', 'Vacancy created.');
    }

    public function editVacancy($id) { return view('admin.vacancies.edit', ['vacancy' => Vacancy::findOrFail($id)]); }

    public function updateVacancy(Request $request, $id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|in:remote,office,hybrid',
            'salary_min' => 'required|integer|min:0',
            'salary_max' => 'required|integer|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string|max:3',
            'description' => 'required|string',
            'company_description' => 'nullable|string',
        ]);
        $validated['salary_currency'] = $validated['salary_currency'] ?? $vacancy->salary_currency;
        $vacancy->update($validated);
        return redirect()->route('admin.vacancies')->with('success', 'Vacancy updated.');
    }

    public function deleteVacancy($id) { Vacancy::findOrFail($id)->delete(); return back()->with('success', 'Vacancy deleted.'); }

    // ── Notifications ──
    public function notifications(Request $request)
    {
        $query = \App\Models\Notification::with('user')->orderBy('notification_time', 'desc');
        if ($request->has('search') && $request->search) {
            $query->where('message', 'like', '%' . $request->search . '%');
        }
        $notifications = $query->paginate(20);
        return view('admin.notifications', compact('notifications'));
    }

    public function deleteNotification($id) { \App\Models\Notification::findOrFail($id)->delete(); return back()->with('success', 'Deleted.'); }

    // ══════════════════════════════════════════════
    // COURSE EXAMS
    // ══════════════════════════════════════════════
    public function exams(Request $request)
    {
        $query = CourseExam::with('course');
        if ($request->has('search') && $request->search) {
            $query->whereHas('course', fn($q) => $q->where('title', 'like', '%' . $request->search . '%'));
        }
        $exams = $query->latest()->paginate(20);
        return view('admin.exams.index', compact('exams'));
    }

    public function createExam() { return view('admin.exams.create', ['courses' => Course::orderBy('title')->get()]); }

    public function storeExam(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'time_limit_minutes' => 'required|integer|min:1',
            'pass_percent' => 'required|integer|min:1|max:100',
            'questions_per_exam' => 'required|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'exam_json' => 'nullable|string',
            'question_bank_json' => 'nullable|string',
        ]);
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        if (!empty($validated['exam_json'])) $validated['exam_json'] = json_decode($validated['exam_json'], true) ?? [];
        if (!empty($validated['question_bank_json'])) $validated['question_bank_json'] = json_decode($validated['question_bank_json'], true) ?? [];
        CourseExam::create($validated);
        return redirect()->route('admin.exams')->with('success', 'Exam created.');
    }

    public function editExam($id) { return view('admin.exams.edit', ['exam' => CourseExam::findOrFail($id), 'courses' => Course::orderBy('title')->get()]); }

    public function updateExam(Request $request, $id)
    {
        $exam = CourseExam::findOrFail($id);
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'time_limit_minutes' => 'required|integer|min:1',
            'pass_percent' => 'required|integer|min:1|max:100',
            'questions_per_exam' => 'required|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'exam_json' => 'nullable|string',
            'question_bank_json' => 'nullable|string',
        ]);
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        if (!empty($validated['exam_json'])) $validated['exam_json'] = json_decode($validated['exam_json'], true) ?? [];
        if (!empty($validated['question_bank_json'])) $validated['question_bank_json'] = json_decode($validated['question_bank_json'], true) ?? [];
        $exam->update($validated);
        return redirect()->route('admin.exams')->with('success', 'Exam updated.');
    }

    public function deleteExam($id) { CourseExam::findOrFail($id)->delete(); return back()->with('success', 'Exam deleted.'); }

    // ══════════════════════════════════════════════
    // LESSON QUIZZES
    // ══════════════════════════════════════════════
    public function quizzes(Request $request)
    {
        $query = LessonQuiz::with('lesson.course');
        if ($request->has('search') && $request->search) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }
        $quizzes = $query->latest()->paginate(20);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function createQuiz($lessonId) { return view('admin.quizzes.create', ['lesson' => Lesson::findOrFail($lessonId)]); }

    public function storeQuiz(Request $request, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $validated = $request->validate([
            'question_text' => 'required|string',
            'options_json' => 'required|string',
            'correct_option' => 'required|integer|min:0',
            'explanation' => 'nullable|string',
            'order_num' => 'nullable|integer|min:0',
        ]);
        $validated['lesson_id'] = $lesson->id;
        $validated['options_json'] = json_decode($validated['options_json'], true) ?? [];
        LessonQuiz::create($validated);
        return redirect()->route('admin.courses.edit', $lesson->course_id)->with('success', 'Quiz created.');
    }

    public function editQuiz($id) { return view('admin.quizzes.edit', ['quiz' => LessonQuiz::findOrFail($id)]); }

    public function updateQuiz(Request $request, $id)
    {
        $quiz = LessonQuiz::findOrFail($id);
        $validated = $request->validate([
            'question_text' => 'required|string',
            'options_json' => 'required|string',
            'correct_option' => 'required|integer|min:0',
            'explanation' => 'nullable|string',
            'order_num' => 'nullable|integer|min:0',
        ]);
        $validated['options_json'] = json_decode($validated['options_json'], true) ?? [];
        $quiz->update($validated);
        return redirect()->route('admin.courses.edit', $quiz->lesson->course_id)->with('success', 'Quiz updated.');
    }

    public function deleteQuiz($id) { $q = LessonQuiz::findOrFail($id); $cid = $q->lesson->course_id; $q->delete(); return redirect()->route('admin.courses.edit', $cid)->with('success', 'Quiz deleted.'); }

    // ══════════════════════════════════════════════
    // LESSON PRACTICE TASKS
    // ══════════════════════════════════════════════
    public function practices(Request $request)
    {
        $query = LessonPracticeTask::with('lesson.course');
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $practices = $query->latest()->paginate(20);
        return view('admin.practices.index', compact('practices'));
    }

    public function createPractice($lessonId) { return view('admin.practices.create', ['lesson' => Lesson::findOrFail($lessonId)]); }

    public function storePractice(Request $request, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'language' => 'required|string|max:50',
            'prompt' => 'nullable|string',
            'starter_code' => 'nullable|string',
            'tests_json' => 'nullable|string',
            'expected_output' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'hints' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'test_runner_json' => 'nullable|string',
            'is_required' => 'nullable|boolean',
        ]);
        $validated['lesson_id'] = $lesson->id;
        $validated['is_required'] = $request->boolean('is_required');
        if (!empty($validated['tests_json'])) $validated['tests_json'] = json_decode($validated['tests_json'], true) ?? [];
        if (!empty($validated['test_runner_json'])) $validated['test_runner_json'] = json_decode($validated['test_runner_json'], true) ?? [];
        LessonPracticeTask::create($validated);
        return redirect()->route('admin.courses.edit', $lesson->course_id)->with('success', 'Practice task created.');
    }

    public function editPractice($id) { return view('admin.practices.edit', ['practice' => LessonPracticeTask::findOrFail($id)]); }

    public function updatePractice(Request $request, $id)
    {
        $practice = LessonPracticeTask::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'language' => 'required|string|max:50',
            'prompt' => 'nullable|string',
            'starter_code' => 'nullable|string',
            'tests_json' => 'nullable|string',
            'expected_output' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'hints' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'test_runner_json' => 'nullable|string',
            'is_required' => 'nullable|boolean',
        ]);
        $validated['is_required'] = $request->boolean('is_required');
        if (!empty($validated['tests_json'])) $validated['tests_json'] = json_decode($validated['tests_json'], true) ?? [];
        if (!empty($validated['test_runner_json'])) $validated['test_runner_json'] = json_decode($validated['test_runner_json'], true) ?? [];
        $practice->update($validated);
        return redirect()->route('admin.courses.edit', $practice->lesson->course_id)->with('success', 'Practice task updated.');
    }

    public function deletePractice($id) { $p = LessonPracticeTask::findOrFail($id); $cid = $p->lesson->course_id; $p->delete(); return redirect()->route('admin.courses.edit', $cid)->with('success', 'Practice task deleted.'); }

    // ══════════════════════════════════════════════
    // ROADMAP NODES
    // ══════════════════════════════════════════════
    public function roadmaps(Request $request)
    {
        $query = RoadmapNode::with('course');
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $roadmaps = $query->latest()->paginate(20);
        return view('admin.roadmaps.index', compact('roadmaps'));
    }

    public function createRoadmap() { return view('admin.roadmaps.create', ['courses' => Course::orderBy('title')->get()]); }

    public function storeRoadmap(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'roadmap_title' => 'nullable|string|max:255',
            'topic' => 'nullable|string|max:255',
            'materials' => 'nullable|string',
            'x' => 'nullable|numeric',
            'y' => 'nullable|numeric',
            'deps' => 'nullable|string',
            'is_exam' => 'nullable|boolean',
        ]);
        $validated['is_exam'] = $request->boolean('is_exam');
        if (!empty($validated['materials'])) $validated['materials'] = json_decode($validated['materials'], true) ?? [];
        if (!empty($validated['deps'])) $validated['deps'] = json_decode($validated['deps'], true) ?? [];
        RoadmapNode::create($validated);
        return redirect()->route('admin.roadmaps')->with('success', 'Roadmap node created.');
    }

    public function editRoadmap($id) { return view('admin.roadmaps.edit', ['roadmap' => RoadmapNode::findOrFail($id), 'courses' => Course::orderBy('title')->get()]); }

    public function updateRoadmap(Request $request, $id)
    {
        $roadmap = RoadmapNode::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'roadmap_title' => 'nullable|string|max:255',
            'topic' => 'nullable|string|max:255',
            'materials' => 'nullable|string',
            'x' => 'nullable|numeric',
            'y' => 'nullable|numeric',
            'deps' => 'nullable|string',
            'is_exam' => 'nullable|boolean',
        ]);
        $validated['is_exam'] = $request->boolean('is_exam');
        if (!empty($validated['materials'])) $validated['materials'] = json_decode($validated['materials'], true) ?? [];
        if (!empty($validated['deps'])) $validated['deps'] = json_decode($validated['deps'], true) ?? [];
        $roadmap->update($validated);
        return redirect()->route('admin.roadmaps')->with('success', 'Roadmap node updated.');
    }

    public function deleteRoadmap($id) { RoadmapNode::findOrFail($id)->delete(); return back()->with('success', 'Roadmap node deleted.'); }

    // ── Roadmap Lessons ──
    public function roadmapLessons($nodeId)
    {
        $node = RoadmapNode::findOrFail($nodeId);
        $lessons = $node->roadmapLessons()->orderBy('order_index')->get();
        return view('admin.roadmaps.lessons', compact('node', 'lessons'));
    }

    public function createRoadmapLesson($nodeId) { return view('admin.roadmaps.lesson_form', ['node' => RoadmapNode::findOrFail($nodeId), 'lesson' => null]); }

    public function storeRoadmapLesson(Request $request, $nodeId)
    {
        $node = RoadmapNode::findOrFail($nodeId);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'materials' => 'nullable|string',
            'order_index' => 'nullable|integer|min:0',
        ]);
        $validated['node_id'] = $node->id;
        if (!empty($validated['materials'])) $validated['materials'] = json_decode($validated['materials'], true) ?? [];
        RoadmapLesson::create($validated);
        return redirect()->route('admin.roadmap.lessons', $node->id)->with('success', 'Roadmap lesson created.');
    }

    public function editRoadmapLesson($id) { $l = RoadmapLesson::findOrFail($id); return view('admin.roadmaps.lesson_form', ['node' => $l->node, 'lesson' => $l]); }

    public function updateRoadmapLesson(Request $request, $id)
    {
        $lesson = RoadmapLesson::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'materials' => 'nullable|string',
            'order_index' => 'nullable|integer|min:0',
        ]);
        if (!empty($validated['materials'])) $validated['materials'] = json_decode($validated['materials'], true) ?? [];
        $lesson->update($validated);
        return redirect()->route('admin.roadmap.lessons', $lesson->node_id)->with('success', 'Roadmap lesson updated.');
    }

    public function deleteRoadmapLesson($id) { $l = RoadmapLesson::findOrFail($id); $nid = $l->node_id; $l->delete(); return redirect()->route('admin.roadmap.lessons', $nid)->with('success', 'Deleted.'); }

    // ── Roadmap Quiz Questions ──
    public function roadmapQuizzes($nodeId)
    {
        $node = RoadmapNode::findOrFail($nodeId);
        $quizzes = $node->quizQuestions()->get();
        return view('admin.roadmaps.quizzes', compact('node', 'quizzes'));
    }

    public function createRoadmapQuiz($nodeId) { return view('admin.roadmaps.quiz_form', ['node' => RoadmapNode::findOrFail($nodeId), 'quiz' => null]); }

    public function storeRoadmapQuiz(Request $request, $nodeId)
    {
        $node = RoadmapNode::findOrFail($nodeId);
        $validated = $request->validate([
            'question' => 'required|string',
            'options' => 'required|string',
            'correct_answer' => 'required|string|max:255',
        ]);
        $validated['node_id'] = $node->id;
        $validated['options'] = json_decode($validated['options'], true) ?? [];
        RoadmapQuizQuestion::create($validated);
        return redirect()->route('admin.roadmap.quizzes', $node->id)->with('success', 'Quiz question created.');
    }

    public function editRoadmapQuiz($id) { $q = RoadmapQuizQuestion::findOrFail($id); return view('admin.roadmaps.quiz_form', ['node' => $q->node, 'quiz' => $q]); }

    public function updateRoadmapQuiz(Request $request, $id)
    {
        $quiz = RoadmapQuizQuestion::findOrFail($id);
        $validated = $request->validate([
            'question' => 'required|string',
            'options' => 'required|string',
            'correct_answer' => 'required|string|max:255',
        ]);
        $validated['options'] = json_decode($validated['options'], true) ?? [];
        $quiz->update($validated);
        return redirect()->route('admin.roadmap.quizzes', $quiz->node_id)->with('success', 'Quiz question updated.');
    }

    public function deleteRoadmapQuiz($id) { $q = RoadmapQuizQuestion::findOrFail($id); $nid = $q->node_id; $q->delete(); return redirect()->route('admin.roadmap.quizzes', $nid)->with('success', 'Deleted.'); }
}
