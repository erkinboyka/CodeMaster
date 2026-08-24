<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\VacancyChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePagesController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProblemController;
use App\Http\Controllers\StudyPlanController;
use App\Http\Controllers\DailyChallengeController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\AiTutorController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\PeerInterviewController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminLessonController;
use App\Http\Controllers\Admin\AdminVacancyController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminExamController;
use App\Http\Controllers\Admin\AdminQuizController;
use App\Http\Controllers\Admin\AdminPracticeController;
use App\Http\Controllers\Admin\AdminRoadmapController;
use App\Http\Controllers\Admin\AdminContestController;
use App\Http\Controllers\Admin\AdminInterviewPrepController;
use App\Http\Controllers\Admin\AdminSeedController;
use App\Http\Controllers\Admin\AdminRoadmapListController;
use App\Http\Controllers\Admin\AdminNewsController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/lang/{locale}', function (string $locale) {
    $supportedLocales = ['ru', 'en', 'tg'];
    if (in_array($locale, $supportedLocales, true)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('throttle:auth');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:auth');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/google-login', [LoginController::class, 'loginGoogle'])->middleware('throttle:auth');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('throttle:auth');
Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:auth');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request')->middleware('throttle:auth');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:auth');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/confirm-password', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('/confirm-password', [ConfirmPasswordController::class, 'confirm']);

Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

// Two-Factor Authentication (login challenge — public, no auth required)
Route::get('/two-factor/challenge', [TwoFactorController::class, 'showChallenge'])->name('two-factor.challenge.show');
Route::post('/two-factor/challenge', [TwoFactorController::class, 'verifyChallenge'])->name('two-factor.challenge');
Route::get('/two-factor/recovery', [TwoFactorController::class, 'showRecoveryChallenge'])->name('two-factor.recovery');
Route::post('/two-factor/recovery', [TwoFactorController::class, 'verifyRecoveryCode'])->name('two-factor.recovery.verify');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/complete-lesson', [CourseController::class, 'completeLesson'])->name('courses.completeLesson');
    Route::get('/courses/{id}/exam', [CourseController::class, 'exam'])->name('courses.exam');
    Route::post('/courses/{id}/exam/submit', [CourseController::class, 'submitExam'])->name('courses.exam.submit');

    // Lessons
    Route::get('/courses/{courseId}/lessons/{lessonId}', [\App\Http\Controllers\LessonController::class, 'show'])->name('courses.lesson');
    Route::post('/courses/{courseId}/lessons/{lessonId}/quiz', [\App\Http\Controllers\LessonController::class, 'submitQuiz'])->name('courses.lesson.quiz');
    Route::post('/courses/{courseId}/lessons/{lessonId}/complete', [\App\Http\Controllers\LessonController::class, 'completeLesson'])->name('courses.lesson.complete');

    // Practice
    Route::get('/courses/{courseId}/practice/{taskId}', [\App\Http\Controllers\PracticeController::class, 'show'])->name('courses.practice');
    Route::post('/courses/{courseId}/practice/{taskId}/run', [\App\Http\Controllers\PracticeController::class, 'runTests'])->name('courses.practice.run');
    Route::post('/courses/{courseId}/practice/{taskId}/submit', [\App\Http\Controllers\PracticeController::class, 'submit'])->name('courses.practice.submit');

    // Vacancies
    Route::get('/vacancies', [VacancyController::class, 'index'])->name('vacancies.index');
    Route::get('/vacancies/{id}', [VacancyController::class, 'show'])->name('vacancies.show');
    Route::post('/vacancies/{id}/apply', [VacancyController::class, 'apply'])->name('vacancies.apply');

    // Vacancy Chat
    Route::get('/vacancy-chat/{applicationId}', [VacancyChatController::class, 'show'])->name('vacancyChat.show');
    Route::post('/vacancy-chat', [VacancyChatController::class, 'store'])->name('vacancyChat.store');
    Route::post('/vacancy-chat/upload', [VacancyChatController::class, 'uploadDocument'])->name('vacancyChat.upload');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/skill', [ProfileController::class, 'addSkill'])->name('profile.skill.add');
    Route::delete('/profile/skill/{id}', [ProfileController::class, 'deleteSkill'])->name('profile.skill.delete');
    Route::post('/profile/experience', [ProfileController::class, 'addExperience'])->name('profile.experience.add');
    Route::put('/profile/experience/{id}', [ProfileController::class, 'updateExperience'])->name('profile.experience.update');
    Route::delete('/profile/experience/{id}', [ProfileController::class, 'deleteExperience'])->name('profile.experience.delete');
    Route::post('/profile/education', [ProfileController::class, 'addEducation'])->name('profile.education.add');
    Route::put('/profile/education/{id}', [ProfileController::class, 'updateEducation'])->name('profile.education.update');
    Route::delete('/profile/education/{id}', [ProfileController::class, 'deleteEducation'])->name('profile.education.delete');
    Route::post('/profile/portfolio', [ProfileController::class, 'addPortfolio'])->name('profile.portfolio.add');
    Route::put('/profile/portfolio/{id}', [ProfileController::class, 'updatePortfolio'])->name('profile.portfolio.update');
    Route::delete('/profile/portfolio/{id}', [ProfileController::class, 'deletePortfolio'])->name('profile.portfolio.delete');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    Route::post('/profile/verify-skill', [ProfileController::class, 'verifySkill'])->name('profile.skill.verify');
    Route::post('/profile/review', [ProfileController::class, 'reviewPlatform'])->name('profile.review');

    // Profile Pages
    Route::get('/profile/my-lists', [ProfilePagesController::class, 'myLists'])->name('profile.my-lists');
    Route::get('/profile/my-lists/{slug}', [ProfilePagesController::class, 'showList'])->name('profile.my-lists.show');
    Route::post('/profile/my-lists', [ProfilePagesController::class, 'createList'])->name('profile.my-lists.create');
    Route::put('/profile/my-lists/{id}', [ProfilePagesController::class, 'updateList'])->name('profile.my-lists.update');
    Route::delete('/profile/my-lists/{id}', [ProfilePagesController::class, 'deleteList'])->name('profile.my-lists.delete');
    Route::post('/profile/my-lists/{id}/problems', [ProfilePagesController::class, 'addProblems'])->name('profile.my-lists.problems.add');
    Route::delete('/profile/my-lists/{listId}/problems/{problemId}', [ProfilePagesController::class, 'removeProblem'])->name('profile.my-lists.problems.remove');
    Route::get('/profile/my-lists/{id}/available-problems', [ProfilePagesController::class, 'availableProblems'])->name('profile.my-lists.problems.available');
    Route::get('/profile/notebook', [ProfilePagesController::class, 'notebook'])->name('profile.notebook');
    Route::post('/profile/notebook', [ProfilePagesController::class, 'storeNote'])->name('profile.notebook.store');
    Route::delete('/profile/notebook/{id}', [ProfilePagesController::class, 'deleteNote'])->name('profile.notebook.delete');
    Route::get('/profile/progress', [ProfilePagesController::class, 'progress'])->name('profile.progress');
    Route::get('/profile/points', [ProfilePagesController::class, 'points'])->name('profile.points');

    // Two-Factor Authentication (authenticated — setup, manage)
    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::get('/two-factor/setup', [TwoFactorController::class, 'showSetup'])->name('two-factor.setup');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::delete('/two-factor', [TwoFactorController::class, 'destroy'])->name('two-factor.destroy');

    // Profile - wildcard LAST
    Route::get('/profile/{userId}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/certificate/{hash}', [CertificateController::class, 'show'])->name('certificate.show');
    Route::get('/certificate/{hash}/download', [CertificateController::class, 'download'])->name('certificate.download');

    // Ratings
    Route::get('/ratings', [RatingController::class, 'index'])->name('ratings.index');

    // Roadmaps
    Route::get('/roadmaps', [RoadmapController::class, 'index'])->name('roadmaps.index');
    Route::get('/roadmap/{title}', [RoadmapController::class, 'show'])->name('roadmap.show')->where('title', '.*');
    Route::post('/roadmap/complete-node', [RoadmapController::class, 'completeNode'])->name('roadmap.completeNode');

    // Community
    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
    Route::post('/community', [CommunityController::class, 'store'])->name('community.store');
    Route::get('/community/{id}', [CommunityController::class, 'show'])->name('community.show');
    Route::put('/community/{id}', [CommunityController::class, 'update'])->name('community.update');
    Route::delete('/community/{id}', [CommunityController::class, 'destroy'])->name('community.destroy');
    Route::post('/community/comment', [CommunityController::class, 'comment'])->name('community.comment');
    Route::post('/community/{id}/like', [CommunityController::class, 'like'])->name('community.like');
    Route::get('/community/tags/all', [CommunityController::class, 'tags'])->name('community.tags');

    // Reviews
    Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/dismiss', [\App\Http\Controllers\ReviewController::class, 'dismiss'])->name('reviews.dismiss');

    // Interview
    Route::get('/interview', [InterviewController::class, 'index'])->name('interview.index');
    Route::post('/interview', [InterviewController::class, 'store'])->name('interview.store');
    Route::get('/interview/{id}', [InterviewController::class, 'room'])->name('interview.room');
    Route::post('/interview/{id}/answer', [InterviewController::class, 'answer'])->name('interview.answer');
    Route::post('/interview/{id}/finish', [InterviewController::class, 'finish'])->name('interview.finish');
    Route::get('/interview/{id}/result', [InterviewController::class, 'result'])->name('interview.result');
    Route::delete('/interview/{id}', [InterviewController::class, 'destroy'])->name('interview.destroy');
    Route::post('/interview/ai-chat', [InterviewController::class, 'aiChat'])->name('interview.aiChat')->middleware('throttle:ai');

    // Peer Interview (WebRTC)
    Route::get('/peer', [PeerInterviewController::class, 'index'])->name('peer.index');
    Route::post('/peer/create', [PeerInterviewController::class, 'create'])->name('peer.create');
    Route::get('/peer/join', [PeerInterviewController::class, 'joinForm'])->name('peer.joinForm');
    Route::post('/peer/join', [PeerInterviewController::class, 'join'])->name('peer.join');
    Route::get('/peer/{code}', [PeerInterviewController::class, 'room'])->name('peer.room');
    Route::match(['get','post'], '/peer/{code}/signal', [PeerInterviewController::class, 'signaling'])->name('peer.signal')->middleware('throttle:peer');
    Route::post('/peer/{code}/leave', [PeerInterviewController::class, 'leave'])->name('peer.leave');
    Route::post('/peer/{code}/code', [PeerInterviewController::class, 'updateCode'])->name('peer.code.update')->middleware('throttle:peer');
    Route::post('/peer/{code}/message', [PeerInterviewController::class, 'sendMessage'])->name('peer.message.send')->middleware('throttle:peer');
    Route::post('/peer/{code}/task', [PeerInterviewController::class, 'addTask'])->name('peer.task.add');
    Route::put('/peer/{code}/task/{taskId}', [PeerInterviewController::class, 'updateTask'])->name('peer.task.update');
    Route::delete('/peer/{code}/task/{taskId}', [PeerInterviewController::class, 'deleteTask'])->name('peer.task.delete');
    Route::post('/peer/{code}/task/{taskId}/start', [PeerInterviewController::class, 'startTask'])->name('peer.task.start');
    Route::post('/peer/{code}/task/{taskId}/submit', [PeerInterviewController::class, 'submitTask'])->name('peer.task.submit');
    Route::post('/peer/{code}/task/{taskId}/review', [PeerInterviewController::class, 'reviewTask'])->name('peer.task.review');
    Route::put('/peer/{code}/task-reorder', [PeerInterviewController::class, 'reorderTasks'])->name('peer.task.reorder');

    // Contests
    Route::get('/contests', [ContestController::class, 'index'])->name('contests.index');
    Route::get('/contests/create', [ContestController::class, 'create'])->name('contests.create');
    Route::post('/contests', [ContestController::class, 'store'])->name('contests.store');
    Route::get('/contests/{id}', [ContestController::class, 'show'])->name('contests.show');
    Route::get('/contests/{contestId}/problems/{problemId}', [ContestController::class, 'showProblem'])->name('contests.problems.show');
    Route::delete('/contests/{contestId}/problems/{problemId}', [ContestController::class, 'destroyProblem'])->name('contests.problems.destroy');
    Route::get('/contests/{id}/edit', [ContestController::class, 'edit'])->name('contests.edit');
    Route::put('/contests/{id}', [ContestController::class, 'update'])->name('contests.update');
    Route::delete('/contests/{id}', [ContestController::class, 'destroy'])->name('contests.destroy');
    Route::get('/contests/{id}/leaderboard', [ContestController::class, 'leaderboard'])->name('contests.leaderboard');
    Route::post('/contests/{id}/problems', [ContestController::class, 'storeProblem'])->name('contests.problems.store');
    Route::post('/contest/submit', [ContestController::class, 'submit'])->name('contest.submit');

    // Notifications
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::get('/api/notifications', function () {
        $user = Auth::user();
        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->orderByDesc('notification_time')
            ->limit(20)
            ->get();
        $unreadCount = \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count();
        return response()->json([
            'ok' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    })->name('api.notifications');

    // Practice
    Route::post('/practice/submit', [PracticeController::class, 'submit'])->name('practice.submit');

    // AI Tutor
    Route::post('/ai/chat', [AiTutorController::class, 'chat'])->name('ai.chat');
    Route::get('/ai/history', [AiTutorController::class, 'getChat'])->name('ai.history');
    Route::post('/ai/clear', [AiTutorController::class, 'clearChat'])->name('ai.clear');

    // Problems (LeetCode-style)
    Route::get('/problems', [ProblemController::class, 'index'])->name('problems.index');
    Route::get('/problems/{problem:slug}', [ProblemController::class, 'show'])->name('problems.show');
    Route::post('/problems/{problem:slug}/submit', [ProblemController::class, 'submit'])->name('problems.submit');
    Route::get('/problems/{problem:slug}/submissions', [ProblemController::class, 'submissions'])->name('problems.submissions');
    Route::get('/problems/{problem:slug}/performance', [ProblemController::class, 'performance'])->name('problems.performance');
    Route::post('/problems/{problem:slug}/collab', [ProblemController::class, 'createCollab'])->name('problems.collab.create');
    Route::get('/collab/{code}', [ProblemController::class, 'joinCollab'])->name('collab.join');
    Route::post('/collab/{code}/leave', [ProblemController::class, 'leaveCollab'])->name('collab.leave');

    // Study Plans (curated)
    Route::get('/study-plans', [StudyPlanController::class, 'index'])->name('study-plans.index');
    Route::get('/study-plans/favorite', [StudyPlanController::class, 'favorite'])->name('study-plans.favorite');
    Route::post('/study-plans/{plan}/favorite', [StudyPlanController::class, 'toggleFavorite'])->name('study-plans.toggle-favorite');

    // AI Study Plans (personalized)
    Route::get('/study-plans/create', [StudyPlanController::class, 'create'])->name('study-plans.create');
    Route::post('/study-plans', [StudyPlanController::class, 'store'])->name('study-plans.store');

    Route::get('/study-plans/{plan:slug}', [StudyPlanController::class, 'show'])->name('study-plans.show');
    Route::get('/my-plans/{plan}', [StudyPlanController::class, 'userShow'])->name('study-plans.user.show');
    Route::delete('/my-plans/{plan}', [StudyPlanController::class, 'userDestroy'])->name('study-plans.user.destroy');
    Route::post('/my-plans/{plan}/mark-solved', [StudyPlanController::class, 'markSolved'])->name('study-plans.mark-solved');

    // Daily Challenge
    Route::get('/daily-challenge', [DailyChallengeController::class, 'index'])->name('daily-challenge');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminUserController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminUserController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminUserController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{id}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('users.toggleBlock');
    Route::delete('/users/{id}', [AdminUserController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{id}/role', [AdminUserController::class, 'updateRole'])->name('users.updateRole');

    // Courses
    Route::get('/courses', [AdminCourseController::class, 'courses'])->name('courses');
    Route::get('/courses/create', [AdminCourseController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [AdminCourseController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{id}/edit', [AdminCourseController::class, 'editCourse'])->name('courses.edit');
    Route::put('/courses/{id}', [AdminCourseController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{id}', [AdminCourseController::class, 'deleteCourse'])->name('courses.delete');

    // Lessons
    Route::get('/lessons', [AdminLessonController::class, 'lessons'])->name('lessons');
    Route::get('/lessons/create/{courseId}', [AdminLessonController::class, 'createLesson'])->name('lessons.create');
    Route::post('/lessons/{courseId}', [AdminLessonController::class, 'storeLesson'])->name('lessons.store');
    Route::get('/lessons/{id}/edit', [AdminLessonController::class, 'editLesson'])->name('lessons.edit');
    Route::put('/lessons/{id}', [AdminLessonController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('/lessons/{id}', [AdminLessonController::class, 'deleteLesson'])->name('lessons.delete');

    // Vacancies
    Route::get('/vacancies', [AdminVacancyController::class, 'vacancies'])->name('vacancies');
    Route::get('/vacancies/create', [AdminVacancyController::class, 'createVacancy'])->name('vacancies.create');
    Route::post('/vacancies', [AdminVacancyController::class, 'storeVacancy'])->name('vacancies.store');
    Route::get('/vacancies/{id}/edit', [AdminVacancyController::class, 'editVacancy'])->name('vacancies.edit');
    Route::put('/vacancies/{id}', [AdminVacancyController::class, 'updateVacancy'])->name('vacancies.update');
    Route::delete('/vacancies/{id}', [AdminVacancyController::class, 'deleteVacancy'])->name('vacancies.delete');

    // Notifications
    Route::get('/notifications', [AdminNotificationController::class, 'notifications'])->name('notifications');
    Route::delete('/notifications/{id}', [AdminNotificationController::class, 'deleteNotification'])->name('notifications.delete');

    // Exams
    Route::get('/exams', [AdminExamController::class, 'exams'])->name('exams');
    Route::get('/exams/create', [AdminExamController::class, 'createExam'])->name('exams.create');
    Route::post('/exams', [AdminExamController::class, 'storeExam'])->name('exams.store');
    Route::get('/exams/{id}/edit', [AdminExamController::class, 'editExam'])->name('exams.edit');
    Route::put('/exams/{id}', [AdminExamController::class, 'updateExam'])->name('exams.update');
    Route::delete('/exams/{id}', [AdminExamController::class, 'deleteExam'])->name('exams.delete');

    // Quizzes
    Route::get('/quizzes', [AdminQuizController::class, 'quizzes'])->name('quizzes');
    Route::get('/quizzes/create/{lessonId}', [AdminQuizController::class, 'createQuiz'])->name('quizzes.create');
    Route::post('/quizzes/{lessonId}', [AdminQuizController::class, 'storeQuiz'])->name('quizzes.store');
    Route::get('/quizzes/{id}/edit', [AdminQuizController::class, 'editQuiz'])->name('quizzes.edit');
    Route::put('/quizzes/{id}', [AdminQuizController::class, 'updateQuiz'])->name('quizzes.update');
    Route::delete('/quizzes/{id}', [AdminQuizController::class, 'deleteQuiz'])->name('quizzes.delete');

    // Practice Tasks
    Route::get('/practices', [AdminPracticeController::class, 'practices'])->name('practices');
    Route::get('/practices/create/{lessonId}', [AdminPracticeController::class, 'createPractice'])->name('practices.create');
    Route::post('/practices/{lessonId}', [AdminPracticeController::class, 'storePractice'])->name('practices.store');
    Route::get('/practices/{id}/edit', [AdminPracticeController::class, 'editPractice'])->name('practices.edit');
    Route::put('/practices/{id}', [AdminPracticeController::class, 'updatePractice'])->name('practices.update');
    Route::delete('/practices/{id}', [AdminPracticeController::class, 'deletePractice'])->name('practices.delete');

    // Roadmaps
    Route::get('/roadmaps', [AdminRoadmapController::class, 'roadmaps'])->name('roadmaps');
    Route::get('/roadmaps/create', [AdminRoadmapController::class, 'createRoadmap'])->name('roadmaps.create');
    Route::post('/roadmaps', [AdminRoadmapController::class, 'storeRoadmap'])->name('roadmaps.store');
    Route::get('/roadmaps/{id}/edit', [AdminRoadmapController::class, 'editRoadmap'])->name('roadmaps.edit');
    Route::put('/roadmaps/{id}', [AdminRoadmapController::class, 'updateRoadmap'])->name('roadmaps.update');
    Route::delete('/roadmaps/{id}', [AdminRoadmapController::class, 'deleteRoadmap'])->name('roadmaps.delete');
    Route::get('/roadmaps/{nodeId}/lessons', [AdminRoadmapController::class, 'roadmapLessons'])->name('roadmap.lessons');
    Route::get('/roadmaps/{nodeId}/lessons/create', [AdminRoadmapController::class, 'createRoadmapLesson'])->name('roadmap.lessons.create');
    Route::post('/roadmaps/{nodeId}/lessons', [AdminRoadmapController::class, 'storeRoadmapLesson'])->name('roadmap.lessons.store');
    Route::get('/roadmap-lessons/{id}/edit', [AdminRoadmapController::class, 'editRoadmapLesson'])->name('roadmap.lessons.edit');
    Route::put('/roadmap-lessons/{id}', [AdminRoadmapController::class, 'updateRoadmapLesson'])->name('roadmap.lessons.update');
    Route::delete('/roadmap-lessons/{id}', [AdminRoadmapController::class, 'deleteRoadmapLesson'])->name('roadmap.lessons.delete');
    Route::get('/roadmaps/{nodeId}/quizzes', [AdminRoadmapController::class, 'roadmapQuizzes'])->name('roadmap.quizzes');
    Route::get('/roadmaps/{nodeId}/quizzes/create', [AdminRoadmapController::class, 'createRoadmapQuiz'])->name('roadmap.quizzes.create');
    Route::post('/roadmaps/{nodeId}/quizzes', [AdminRoadmapController::class, 'storeRoadmapQuiz'])->name('roadmap.quizzes.store');
    Route::get('/roadmap-quizzes/{id}/edit', [AdminRoadmapController::class, 'editRoadmapQuiz'])->name('roadmap.quizzes.edit');
    Route::put('/roadmap-quizzes/{id}', [AdminRoadmapController::class, 'updateRoadmapQuiz'])->name('roadmap.quizzes.update');
    Route::delete('/roadmap-quizzes/{id}', [AdminRoadmapController::class, 'deleteRoadmapQuiz'])->name('roadmap.quizzes.delete');

    // Contests
    Route::get('/contests', [AdminContestController::class, 'contests'])->name('contests');
    Route::get('/contests/create', [AdminContestController::class, 'createContest'])->name('contests.create');
    Route::post('/contests', [AdminContestController::class, 'storeContest'])->name('contests.store');
    Route::get('/contests/{id}/edit', [AdminContestController::class, 'editContest'])->name('contests.edit');
    Route::put('/contests/{id}', [AdminContestController::class, 'updateContest'])->name('contests.update');
    Route::delete('/contests/{id}', [AdminContestController::class, 'deleteContest'])->name('contests.delete');

    // Contest Tasks
    Route::get('/contests/{contestId}/tasks', [AdminContestController::class, 'tasks'])->name('contests.tasks');
    Route::get('/contests/{contestId}/tasks/create', [AdminContestController::class, 'createTask'])->name('contests.tasks.create');
    Route::post('/contests/{contestId}/tasks', [AdminContestController::class, 'storeTask'])->name('contests.tasks.store');
    Route::get('/contests/{contestId}/tasks/{taskId}/edit', [AdminContestController::class, 'editTask'])->name('contests.tasks.edit');
    Route::put('/contests/{contestId}/tasks/{taskId}', [AdminContestController::class, 'updateTask'])->name('contests.tasks.update');
    Route::delete('/contests/{contestId}/tasks/{taskId}', [AdminContestController::class, 'deleteTask'])->name('contests.tasks.delete');

    // Contest Solutions & Submissions
    Route::get('/solutions', [AdminContestController::class, 'solutions'])->name('contests.solutions');
    Route::get('/submission-detail', [AdminContestController::class, 'submissionDetail'])->name('contests.submission-detail');
    Route::delete('/submissions/{id}', [AdminContestController::class, 'resetSubmission'])->name('contests.submissions.reset');
    Route::post('/users/{userId}/reset-contests', [AdminContestController::class, 'resetUserContests'])->name('users.reset-contests');

    // Ejudge Import
    Route::post('/ejudge/scan', [AdminContestController::class, 'ejudgeScan'])->name('ejudge.scan');
    Route::post('/ejudge/import', [AdminContestController::class, 'ejudgeImport'])->name('ejudge.import');

    // Interview Prep Tasks
    Route::get('/interview-prep', [AdminInterviewPrepController::class, 'index'])->name('interview-prep');
    Route::get('/interview-prep/create', [AdminInterviewPrepController::class, 'create'])->name('interview-prep.create');
    Route::post('/interview-prep', [AdminInterviewPrepController::class, 'store'])->name('interview-prep.store');
    Route::get('/interview-prep/{id}/edit', [AdminInterviewPrepController::class, 'edit'])->name('interview-prep.edit');
    Route::put('/interview-prep/{id}', [AdminInterviewPrepController::class, 'update'])->name('interview-prep.update');
    Route::delete('/interview-prep/{id}', [AdminInterviewPrepController::class, 'destroy'])->name('interview-prep.destroy');
    Route::post('/interview-prep/import', [AdminInterviewPrepController::class, 'importFolders'])->name('interview-prep.import');

    // Notification Create
    Route::get('/notifications/create', [AdminNotificationController::class, 'createNotification'])->name('notifications.create');
    Route::post('/notifications', [AdminNotificationController::class, 'storeNotification'])->name('notifications.store');

    // Seed Learning Pack
    Route::post('/seed-learning-pack', [AdminSeedController::class, 'seedLearningPack'])->name('seed-learning-pack');

    // Roadmap List
    Route::get('/roadmap-list', [AdminRoadmapListController::class, 'index'])->name('roadmap-list');
    Route::get('/roadmap-list/create', [AdminRoadmapListController::class, 'create'])->name('roadmap-list.create');
    Route::post('/roadmap-list', [AdminRoadmapListController::class, 'store'])->name('roadmap-list.store');
    Route::get('/roadmap-list/{id}/edit', [AdminRoadmapListController::class, 'edit'])->name('roadmap-list.edit');
    Route::put('/roadmap-list/{id}', [AdminRoadmapListController::class, 'update'])->name('roadmap-list.update');
    Route::delete('/roadmap-list/{id}', [AdminRoadmapListController::class, 'destroy'])->name('roadmap-list.destroy');

    // News
    Route::get('/news', [AdminNewsController::class, 'index'])->name('news.index');
    Route::get('/news/create', [AdminNewsController::class, 'create'])->name('news.create');
    Route::post('/news', [AdminNewsController::class, 'store'])->name('news.store');
    Route::get('/news/{news}/edit', [AdminNewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{news}', [AdminNewsController::class, 'update'])->name('news.update');
    Route::delete('/news/{news}', [AdminNewsController::class, 'destroy'])->name('news.destroy');
});

// Static pages (must be last, after all specific routes)
Route::get('/about', fn () => app(StaticPageController::class)->show('about'))->name('static.about');
Route::get('/contacts', fn () => app(StaticPageController::class)->show('contacts'))->name('static.contacts');
Route::get('/terms', fn () => app(StaticPageController::class)->show('terms'))->name('static.terms');
Route::get('/privacy', fn () => app(StaticPageController::class)->show('privacy'))->name('static.privacy');
Route::get('/{page}', [StaticPageController::class, 'show'])->name('static.show');
