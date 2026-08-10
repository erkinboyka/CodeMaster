<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\VacancyChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\AiTutorController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\AdminController;
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

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/google-login', [LoginController::class, 'loginGoogle']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/confirm-password', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('/confirm-password', [ConfirmPasswordController::class, 'confirm']);

Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

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
    Route::get('/profile/{userId}', [ProfileController::class, 'show'])->name('profile.show');
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

    // Certificates
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

    // Interview
    Route::get('/interview', [InterviewController::class, 'index'])->name('interview.index');
    Route::post('/interview', [InterviewController::class, 'store'])->name('interview.store');
    Route::get('/interview/{id}', [InterviewController::class, 'room'])->name('interview.room');
    Route::post('/interview/{id}/answer', [InterviewController::class, 'answer'])->name('interview.answer');
    Route::post('/interview/{id}/finish', [InterviewController::class, 'finish'])->name('interview.finish');
    Route::get('/interview/{id}/result', [InterviewController::class, 'result'])->name('interview.result');
    Route::delete('/interview/{id}', [InterviewController::class, 'destroy'])->name('interview.destroy');
    Route::post('/interview/ai-chat', [InterviewController::class, 'aiChat'])->name('interview.aiChat');

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

    // Practice
    Route::post('/practice/submit', [PracticeController::class, 'submit'])->name('practice.submit');

    // AI Tutor
    Route::post('/ai/chat', [AiTutorController::class, 'chat'])->name('ai.chat');
    Route::get('/ai/history', [AiTutorController::class, 'getChat'])->name('ai.history');
    Route::post('/ai/clear', [AiTutorController::class, 'clearChat'])->name('ai.clear');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/lessons', [AdminController::class, 'lessons'])->name('lessons');
    Route::get('/vacancies', [AdminController::class, 'vacancies'])->name('vacancies');
    Route::post('/users/{id}/toggle-block', [AdminController::class, 'toggleBlock'])->name('users.toggleBlock');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{id}/role', [AdminController::class, 'updateRole'])->name('users.updateRole');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');

    Route::delete('/courses/{id}', [AdminController::class, 'deleteCourse'])->name('courses.delete');

    Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [AdminController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{id}/edit', [AdminController::class, 'editCourse'])->name('courses.edit');
    Route::put('/courses/{id}', [AdminController::class, 'updateCourse'])->name('courses.update');

    Route::get('/lessons/create/{courseId}', [AdminController::class, 'createLesson'])->name('lessons.create');
    Route::post('/lessons/{courseId}', [AdminController::class, 'storeLesson'])->name('lessons.store');
    Route::get('/lessons/{id}/edit', [AdminController::class, 'editLesson'])->name('lessons.edit');
    Route::put('/lessons/{id}', [AdminController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('/lessons/{id}', [AdminController::class, 'deleteLesson'])->name('lessons.delete');

    Route::get('/vacancies/create', [AdminController::class, 'createVacancy'])->name('vacancies.create');
    Route::post('/vacancies', [AdminController::class, 'storeVacancy'])->name('vacancies.store');
    Route::get('/vacancies/{id}/edit', [AdminController::class, 'editVacancy'])->name('vacancies.edit');
    Route::put('/vacancies/{id}', [AdminController::class, 'updateVacancy'])->name('vacancies.update');

    Route::delete('/vacancies/{id}', [AdminController::class, 'deleteVacancy'])->name('vacancies.delete');

    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
    Route::delete('/notifications/{id}', [AdminController::class, 'deleteNotification'])->name('notifications.delete');

    // Exams
    Route::get('/exams', [AdminController::class, 'exams'])->name('exams');
    Route::get('/exams/create', [AdminController::class, 'createExam'])->name('exams.create');
    Route::post('/exams', [AdminController::class, 'storeExam'])->name('exams.store');
    Route::get('/exams/{id}/edit', [AdminController::class, 'editExam'])->name('exams.edit');
    Route::put('/exams/{id}', [AdminController::class, 'updateExam'])->name('exams.update');
    Route::delete('/exams/{id}', [AdminController::class, 'deleteExam'])->name('exams.delete');

    // Quizzes
    Route::get('/quizzes', [AdminController::class, 'quizzes'])->name('quizzes');
    Route::get('/quizzes/create/{lessonId}', [AdminController::class, 'createQuiz'])->name('quizzes.create');
    Route::post('/quizzes/{lessonId}', [AdminController::class, 'storeQuiz'])->name('quizzes.store');
    Route::get('/quizzes/{id}/edit', [AdminController::class, 'editQuiz'])->name('quizzes.edit');
    Route::put('/quizzes/{id}', [AdminController::class, 'updateQuiz'])->name('quizzes.update');
    Route::delete('/quizzes/{id}', [AdminController::class, 'deleteQuiz'])->name('quizzes.delete');

    // Practice Tasks
    Route::get('/practices', [AdminController::class, 'practices'])->name('practices');
    Route::get('/practices/create/{lessonId}', [AdminController::class, 'createPractice'])->name('practices.create');
    Route::post('/practices/{lessonId}', [AdminController::class, 'storePractice'])->name('practices.store');
    Route::get('/practices/{id}/edit', [AdminController::class, 'editPractice'])->name('practices.edit');
    Route::put('/practices/{id}', [AdminController::class, 'updatePractice'])->name('practices.update');
    Route::delete('/practices/{id}', [AdminController::class, 'deletePractice'])->name('practices.delete');

    // Roadmaps
    Route::get('/roadmaps', [AdminController::class, 'roadmaps'])->name('roadmaps');
    Route::get('/roadmaps/create', [AdminController::class, 'createRoadmap'])->name('roadmaps.create');
    Route::post('/roadmaps', [AdminController::class, 'storeRoadmap'])->name('roadmaps.store');
    Route::get('/roadmaps/{id}/edit', [AdminController::class, 'editRoadmap'])->name('roadmaps.edit');
    Route::put('/roadmaps/{id}', [AdminController::class, 'updateRoadmap'])->name('roadmaps.update');
    Route::delete('/roadmaps/{id}', [AdminController::class, 'deleteRoadmap'])->name('roadmaps.delete');
    Route::get('/roadmaps/{nodeId}/lessons', [AdminController::class, 'roadmapLessons'])->name('roadmap.lessons');
    Route::get('/roadmaps/{nodeId}/lessons/create', [AdminController::class, 'createRoadmapLesson'])->name('roadmap.lessons.create');
    Route::post('/roadmaps/{nodeId}/lessons', [AdminController::class, 'storeRoadmapLesson'])->name('roadmap.lessons.store');
    Route::get('/roadmap-lessons/{id}/edit', [AdminController::class, 'editRoadmapLesson'])->name('roadmap.lessons.edit');
    Route::put('/roadmap-lessons/{id}', [AdminController::class, 'updateRoadmapLesson'])->name('roadmap.lessons.update');
    Route::delete('/roadmap-lessons/{id}', [AdminController::class, 'deleteRoadmapLesson'])->name('roadmap.lessons.delete');
    Route::get('/roadmaps/{nodeId}/quizzes', [AdminController::class, 'roadmapQuizzes'])->name('roadmap.quizzes');
    Route::get('/roadmaps/{nodeId}/quizzes/create', [AdminController::class, 'createRoadmapQuiz'])->name('roadmap.quizzes.create');
    Route::post('/roadmaps/{nodeId}/quizzes', [AdminController::class, 'storeRoadmapQuiz'])->name('roadmap.quizzes.store');
    Route::get('/roadmap-quizzes/{id}/edit', [AdminController::class, 'editRoadmapQuiz'])->name('roadmap.quizzes.edit');
    Route::put('/roadmap-quizzes/{id}', [AdminController::class, 'updateRoadmapQuiz'])->name('roadmap.quizzes.update');
    Route::delete('/roadmap-quizzes/{id}', [AdminController::class, 'deleteRoadmapQuiz'])->name('roadmap.quizzes.delete');
});

// Static pages (must be last, after all specific routes)
Route::get('/about', fn () => app(StaticPageController::class)->show('about'))->name('static.about');
Route::get('/contacts', fn () => app(StaticPageController::class)->show('contacts'))->name('static.contacts');
Route::get('/terms', fn () => app(StaticPageController::class)->show('terms'))->name('static.terms');
Route::get('/privacy', fn () => app(StaticPageController::class)->show('privacy'))->name('static.privacy');
Route::get('/{page}', [StaticPageController::class, 'show'])->name('static.show');
