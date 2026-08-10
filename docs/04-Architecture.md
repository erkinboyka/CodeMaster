# CodeMaster — Modern Laravel 12 Architecture

**Версия:** 1.0  
**Дата:** 2026-08-01  
**Подход:** DDD + SOLID + Clean Architecture + CQRS-lite

---

## 1. Архитектурный стиль

### 1.1 Принципы

- **Domain-Driven Design (DDD):** Бизнес-логика组织化围绕 доменных контекстов
- **SOLID:** Каждый класс имеет одну ответственность, открыт для расширения, закрыт для модификации
- **Clean Architecture:** Зависимости направлены от внешних слоёв к внутренним
- **Service Layer:** Бизнес-операции в сервисах, не в контроллерах
- **Action Pattern:** Комплексные операции — отдельные классы Action
- **Repository Pattern:** Только для чтения данных (CQRS-lite) — команды через Eloquent, запросы через Repository
- **DTO (Data Transfer Objects):** Передача данных между слоями
- **Value Objects:** Неизменяемые значения (Money, Hash, Email)
- **Events/Listeners:** Слабая связанность между модулями
- **Jobs/Queues:** Асинхронные операции (AI-запросы, уведомления)

### 1.2 Структура слоёв

```
┌─────────────────────────────────────────────────────────┐
│                  PRESENTATION LAYER                      │
│  Controllers │ Form Requests │ API Resources │ Middleware │
│  Blade Views │ Livewire Components                      │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────┐
│                    APPLICATION LAYER                      │
│  Actions │ Services │ DTOs │ Events │ Listeners │ Jobs   │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────┐
│                     DOMAIN LAYER                          │
│  Models │ Enums │ Value Objects │ Policies │ Rules       │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────┐
│                  INFRASTRUCTURE LAYER                     │
│  Repositories │ Services │ External APIs │ Cache │ Queue  │
│  Migrations │ Seeders │ Factories                         │
└─────────────────────────────────────────────────────────┘
```

---

## 2. Структура каталогов

```
app/
├── Enums/
│   ├── UserRole.php
│   ├── CourseCategory.php
│   ├── CourseLevel.php
│   ├── LessonType.php
│   ├── VacancyType.php
│   ├── InterviewType.php
│   ├── InterviewDifficulty.php
│   └── ApplicationStatus.php
│
├── ValueObjects/
│   ├── Money.php
│   ├── CertificateHash.php
│   ├── Email.php
│   └── ProgressPercent.php
│
├── DTOs/
│   ├── Auth/
│   │   ├── RegistrationData.php
│   │   └── LoginData.php
│   ├── Education/
│   │   ├── CourseData.php
│   │   ├── LessonData.php
│   │   ├── ExamSubmissionData.php
│   │   └── PracticeSubmissionData.php
│   ├── Career/
│   │   ├── VacancyData.php
│   │   ├── ApplicationData.php
│   │   └── ChatMessageData.php
│   ├── AI/
│   │   ├── ChatMessageDTO.php
│   │   └── InterviewData.php
│   └── Profile/
│       ├── ProfileData.php
│       ├── SkillData.php
│       ├── ExperienceData.php
│       ├── EducationData.php
│       └── PortfolioData.php
│
├── Actions/
│   ├── Auth/
│   │   ├── RegisterUser.php
│   │   ├── LoginUser.php
│   │   ├── LoginWithGoogle.php
│   │   └── ResetPassword.php
│   ├── Education/
│   │   ├── CompleteLesson.php
│   │   ├── SubmitExam.php
│   │   ├── CompleteRoadmapNode.php
│   │   └── SubmitPracticeCode.php
│   ├── Career/
│   │   ├── ApplyToVacancy.php
│   │   ├── SendChatMessage.php
│   │   └── UploadDocument.php
│   ├── AI/
│   │   ├── SendChatMessage.php
│   │   ├── StartInterview.php
│   │   └── AnswerInterviewQuestion.php
│   ├── Community/
│   │   ├── CreatePost.php
│   │   ├── AddComment.php
│   │   └── ToggleLike.php
│   └── Profile/
│       ├── UpdateProfile.php
│       ├── AddSkill.php
│       ├── AddExperience.php
│       ├── AddEducation.php
│       └── AddPortfolio.php
│
├── Services/
│   ├── AI/
│   │   ├── GeminiService.php
│   │   └── AiTutorService.php
│   ├── Code/
│   │   └── Judge0Service.php
│   ├── Auth/
│   │   ├── GoogleAuthService.php
│   │   └── RecaptchaService.php
│   ├── Certificate/
│   │   └── CertificateService.php
│   └── I18n/
│       └── I18nService.php
│
├── Repositories/
│   ├── CourseRepository.php
│   ├── VacancyRepository.php
│   ├── CertificateRepository.php
│   ├── CommunityRepository.php
│   └── UserRepository.php
│
├── Events/
│   ├── UserRegistered.php
│   ├── LessonCompleted.php
│   ├── CourseCompleted.php
│   ├── ExamPassed.php
│   ├── CertificateIssued.php
│   ├── VacancyApplied.php
│   ├── ChatMessageSent.php
│   ├── InterviewCompleted.php
│   └── PostCreated.php
│
├── Listeners/
│   ├── SendWelcomeNotification.php
│   ├── UpdateCourseProgress.php
│   ├── IssueCertificate.php
│   ├── NotifyRecruiter.php
│   ├── TrimChatHistory.php
│   ├── LogUserActivity.php
│   └── UpdateLeaderboard.php
│
├── Jobs/
│   ├── SendGeminiRequest.php
│   ├── ExecuteJudge0Code.php
│   ├── GenerateCertificateImage.php
│   └── SendEmailNotification.php
│
├── Notifications/
│   ├── WelcomeNotification.php
│   ├── CourseCompletedNotification.php
│   ├── CertificateIssuedNotification.php
│   ├── NewApplicationNotification.php
│   └── NewChatMessageNotification.php
│
├── Policies/
│   ├── CoursePolicy.php
│   ├── LessonPolicy.php
│   ├── VacancyPolicy.php
│   ├── ApplicationPolicy.php
│   ├── CommunityPostPolicy.php
│   ├── ProfilePolicy.php
│   └── CertificatePolicy.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   ├── RegisterController.php
│   │   │   ├── ForgotPasswordController.php
│   │   │   └── ResetPasswordController.php
│   │   ├── Web/
│   │   │   ├── DashboardController.php
│   │   │   ├── HomeController.php
│   │   │   ├── CourseController.php
│   │   │   ├── LessonController.php
│   │   │   ├── ExamController.php
│   │   │   ├── VacancyController.php
│   │   │   ├── VacancyChatController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── CertificateController.php
│   │   │   ├── RoadmapController.php
│   │   │   ├── CommunityController.php
│   │   │   ├── InterviewController.php
│   │   │   ├── ContestController.php
│   │   │   ├── PracticeController.php
│   │   │   ├── RatingController.php
│   │   │   ├── NotificationController.php
│   │   │   └── AiTutorController.php
│   │   └── Admin/
│   │       ├── AdminDashboardController.php
│   │       ├── AdminUserController.php
│   │       ├── AdminCourseController.php
│   │       ├── AdminLessonController.php
│   │       └── AdminVacancyController.php
│   │
│   ├── Requests/
│   │   ├── Auth/
│   │   │   ├── RegisterRequest.php
│   │   │   └── LoginRequest.php
│   │   ├── Education/
│   │   │   ├── CompleteLessonRequest.php
│   │   │   ├── SubmitExamRequest.php
│   │   │   └── SubmitPracticeRequest.php
│   │   ├── Career/
│   │   │   ├── StoreVacancyRequest.php
│   │   │   └── ApplyToVacancyRequest.php
│   │   ├── AI/
│   │   │   ├── SendChatMessageRequest.php
│   │   │   └── StartInterviewRequest.php
│   │   ├── Community/
│   │   │   ├── StorePostRequest.php
│   │   │   └── StoreCommentRequest.php
│   │   ├── Profile/
│   │   │   ├── UpdateProfileRequest.php
│   │   │   ├── AddSkillRequest.php
│   │   │   ├── AddExperienceRequest.php
│   │   │   └── AddEducationRequest.php
│   │   └── Admin/
│   │       ├── StoreCourseRequest.php
│   │       ├── StoreLessonRequest.php
│   │       ├── StoreVacancyRequest.php
│   │       └── UpdateUserRoleRequest.php
│   │
│   ├── Resources/
│   │   ├── CourseResource.php
│   │   ├── LessonResource.php
│   │   ├── VacancyResource.php
│   │   ├── CertificateResource.php
│   │   ├── ProfileResource.php
│   │   ├── CommunityPostResource.php
│   │   ├── InterviewResource.php
│   │   ├── RatingResource.php
│   │   ├── NotificationResource.php
│   │   └── ChatMessageResource.php
│   │
│   └── Middleware/
│       ├── SetLocale.php
│       ├── EnsureUserIsAdmin.php
│       ├── EnsureUserIsBlocked.php
│       └── TrackLastActivity.php
│
├── Models/
│   ├── User.php
│   ├── Course.php
│   ├── Lesson.php
│   ├── QuizQuestion.php
│   ├── QuizOption.php
│   ├── LessonTest.php
│   ├── CourseExam.php
│   ├── CourseSkill.php
│   ├── Certificate.php
│   ├── Vacancy.php
│   ├── VacancySkill.php
│   ├── VacancyRequirement.php
│   ├── VacancyPluse.php
│   ├── VacancyResponsibility.php
│   ├── UserApplication.php
│   ├── VacancyChat.php
│   ├── VacancyDocument.php
│   ├── RoadmapNode.php
│   ├── RoadmapLesson.php
│   ├── RoadmapQuizQuestion.php
│   ├── RoadmapUserProgress.php
│   ├── RoadmapCertificate.php
│   ├── CommunityPost.php
│   ├── CommunityComment.php
│   ├── CommunityPostLike.php
│   ├── ChatMessage.php
│   ├── Interview.php
│   ├── Notification.php
│   ├── UserActivity.php
│   ├── UserSkill.php
│   ├── UserExperience.php
│   ├── UserEducation.php
│   ├── UserPortfolio.php
│   ├── UserCourseProgress.php
│   ├── UserLessonProgress.php
│   ├── UserAiWallet.php
│   ├── UserCvCustomization.php
│   ├── UserSkillsAssessment.php
│   ├── PracticeSubmission.php
│   ├── ContestSubmission.php
│   ├── PlatformReview.php
│   ├── LessonPracticeTask.php
│   └── Session.php
│
├── Helpers/
│   └── helpers.php
│
└── Providers/
    ├── AppServiceProvider.php
    ├── EventServiceProvider.php
    ├── AuthServiceProvider.php
    └── RepositoryServiceProvider.php
```

---

## 3. Enums

### 3.1 UserRole

```php
enum UserRole: string
{
    case Seeker = 'seeker';
    case Recruiter = 'recruiter';
    case Admin = 'admin';

    public function label(): string
    {
        return match($this) {
            self::Seeker => 'Соискатель',
            self::Recruiter => 'Работодатель',
            self::Admin => 'Администратор',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function canManageContent(): bool
    {
        return $this === self::Admin;
    }

    public function canPublishVacancies(): bool
    {
        return in_array($this, [self::Recruiter, self::Admin]);
    }
}
```

### 3.2 CourseCategory

```php
enum CourseCategory: string
{
    case Frontend = 'frontend';
    case Backend = 'backend';
    case Design = 'design';
    case DevOps = 'devops';
    case Other = 'other';

    public function label(): string
    {
        return match($this) {
            self::Frontend => 'Frontend',
            self::Backend => 'Backend',
            self::Design => 'Дизайн',
            self::DevOps => 'DevOps',
            self::Other => 'Другое',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Frontend => 'fa-code',
            self::Backend => 'fa-server',
            self::Design => 'fa-palette',
            self::DevOps => 'fa-cloud',
            self::Other => 'fa-ellipsis-h',
        };
    }
}
```

### 3.3 CourseLevel

```php
enum CourseLevel: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';

    public function label(): string
    {
        return match($this) {
            self::Beginner => 'Начальный',
            self::Intermediate => 'Средний',
            self::Advanced => 'Продвинутый',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Beginner => 'green',
            self::Intermediate => 'yellow',
            self::Advanced => 'red',
        };
    }
}
```

### 3.4 LessonType

```php
enum LessonType: string
{
    case Video = 'video';
    case Article = 'article';
    case Quiz = 'quiz';

    public function label(): string
    {
        return match($this) {
            self::Video => 'Видео',
            self::Article => 'Статья',
            self::Quiz => 'Тест',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Video => 'fa-play-circle',
            self::Article => 'fa-file-alt',
            self::Quiz => 'fa-question-circle',
        };
    }
}
```

### 3.5 VacancyType

```php
enum VacancyType: string
{
    case Remote = 'remote';
    case Office = 'office';
    case Hybrid = 'hybrid';

    public function label(): string
    {
        return match($this) {
            self::Remote => 'Удалённо',
            self::Office => 'Офис',
            self::Hybrid => 'Гибрид',
        };
    }
}
```

### 3.6 InterviewType

```php
enum InterviewType: string
{
    case Technical = 'technical';
    case Behavioral = 'behavioral';
    case Coding = 'coding';
    case SystemDesign = 'system_design';

    public function label(): string
    {
        return match($this) {
            self::Technical => 'Техническое',
            self::Behavioral => 'Поведенческое',
            self::Coding => 'Кодовое',
            self::SystemDesign => 'Системный дизайн',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::Technical => 'Вопросы по техническим знаниям',
            self::Behavioral => 'Вопросы по методу STAR',
            self::Coding => 'Задачи на написание кода',
            self::SystemDesign => 'Архитектурные задачи',
        };
    }
}
```

### 3.7 InterviewDifficulty

```php
enum InterviewDifficulty: string
{
    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';

    public function label(): string
    {
        return match($this) {
            self::Easy => 'Лёгкий',
            self::Medium => 'Средний',
            self::Hard => 'Сложный',
        };
    }

    public function multiplier(): float
    {
        return match($this) {
            self::Easy => 1.0,
            self::Medium => 1.5,
            self::Hard => 2.0,
        };
    }
}
```

### 3.8 ApplicationStatus

```php
enum ApplicationStatus: string
{
    case Applied = 'applied';
    case InReview = 'in_review';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Applied => 'Откликнут',
            self::InReview => 'На рассмотрении',
            self::Accepted => 'Принят',
            self::Rejected => 'Отклонён',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Applied => 'blue',
            self::InReview => 'yellow',
            self::Accepted => 'green',
            self::Rejected => 'red',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Accepted, self::Rejected]);
    }
}
```

---

## 4. Value Objects

### 4.1 Money

```php
final class Money
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency = 'TJS',
    ) {}

    public function format(): string
    {
        return number_format($this->amount, 0, '.', ' ') . ' ' . $this->currency;
    }

    public function isZero(): bool
    {
        return $this->amount <= 0;
    }

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount, $this->currency);
    }

    public static function fromArray(array $data): self
    {
        return new self($data['amount'] ?? 0, $data['currency'] ?? 'TJS');
    }
}
```

### 4.2 CertificateHash

```php
final class CertificateHash
{
    private const LENGTH = 40;

    private function __construct(
        public readonly string $value,
    ) {}

    public static function generate(): self
    {
        return new self(Str::random(self::LENGTH));
    }

    public static function fromString(string $hash): self
    {
        if (strlen($hash) !== self::LENGTH) {
            throw new \InvalidArgumentException('Invalid certificate hash length');
        }
        return new self($hash);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function url(): string
    {
        return route('certificate.show', $this->value);
    }
}
```

### 4.3 ProgressPercent

```php
final class ProgressPercent
{
    private function __construct(
        public readonly int $value,
    ) {}

    public static function calculate(int $completed, int $total): self
    {
        if ($total <= 0) {
            return new self(0);
        }
        $percent = (int) round(($completed / $total) * 100);
        return new self(min(100, max(0, $percent)));
    }

    public function isComplete(): bool
    {
        return $this->value >= 100;
    }

    public function barColor(): string
    {
        return match(true) {
            $this->value >= 100 => 'green',
            $this->value >= 50 => 'yellow',
            default => 'blue',
        };
    }
}
```

---

## 5. DTOs

### 5.1 RegistrationData

```php
final class RegistrationData
{
    public function __construct(
        public readonly string $name,
        public readonly Email $email,
        public readonly string $password,
        public readonly ?UserRole $role,
        public readonly ?array $skills,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: Email::fromString($request->validated('email')),
            password: $request->validated('password'),
            role: UserRole::tryFrom($request->validated('role', 'seeker')),
            skills: $request->validated('skills'),
        );
    }
}
```

### 5.2 CourseData

```php
final class CourseData
{
    public function __construct(
        public readonly string $title,
        public readonly string $instructor,
        public readonly string $description,
        public readonly CourseCategory $category,
        public readonly CourseLevel $level,
        public readonly ?string $imageUrl,
        public readonly ?string $materialsTitle,
        public readonly ?string $materialsUrl,
    ) {}

    public static function fromRequest(StoreCourseRequest $request): self
    {
        return new self(
            title: $request->validated('title'),
            instructor: $request->validated('instructor'),
            description: $request->validated('description'),
            category: CourseCategory::from($request->validated('category')),
            level: CourseLevel::from($request->validated('level')),
            imageUrl: $request->validated('image_url'),
            materialsTitle: $request->validated('materials_title'),
            materialsUrl: $request->validated('materials_url'),
        );
    }
}
```

### 5.3 ExamSubmissionData

```php
final class ExamSubmissionData
{
    public function __construct(
        public readonly int $courseId,
        public readonly array $answers,
    ) {}

    public static function fromRequest(SubmitExamRequest $request, int $courseId): self
    {
        return new self(
            courseId: $courseId,
            answers: $request->validated('answers'),
        );
    }
}
```

### 5.4 VacancyData

```php
final class VacancyData
{
    public function __construct(
        public readonly string $title,
        public readonly string $company,
        public readonly string $location,
        public readonly VacancyType $type,
        public readonly ?Money $salaryMin,
        public readonly ?Money $salaryMax,
        public readonly string $description,
        public readonly ?string $companyDescription,
        public readonly array $skills,
        public readonly array $requirements,
        public readonly array $pluses,
        public readonly array $responsibilities,
    ) {}

    public static function fromRequest(StoreVacancyRequest $request): self
    {
        return new self(
            title: $request->validated('title'),
            company: $request->validated('company'),
            location: $request->validated('location'),
            type: VacancyType::from($request->validated('type')),
            salaryMin: $request->validated('salary_min')
                ? Money::fromArray(['amount' => $request->validated('salary_min'), 'currency' => $request->validated('salary_currency', 'TJS')])
                : null,
            salaryMax: $request->validated('salary_max')
                ? Money::fromArray(['amount' => $request->validated('salary_max'), 'currency' => $request->validated('salary_currency', 'TJS')])
                : null,
            description: $request->validated('description'),
            companyDescription: $request->validated('company_description'),
            skills: $request->validated('skills', []),
            requirements: $request->validated('requirements', []),
            pluses: $request->validated('pluses', []),
            responsibilities: $request->validated('responsibilities', []),
        );
    }
}
```

---

## 6. Actions (Business Operations)

### 6.1 RegisterUser

```php
class RegisterUser
{
    public function __construct(
        private readonly RecaptchaService $recaptcha,
    ) {}

    public function execute(RegistrationData $data): User
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email->value,
            'password' => Hash::make($data->password),
            'role' => $data->role ?? UserRole::Seeker,
        ]);

        if ($data->skills) {
            foreach ($data->skills as $skill) {
                $user->skills()->create([
                    'skill_name' => $skill['name'],
                    'skill_level' => $skill['level'] ?? 'beginner',
                ]);
            }
        }

        UserAiWallet::create(['user_id' => $user->id, 'balance' => 100]);

        event(new UserRegistered($user));

        return $user;
    }
}
```

### 6.2 LoginUser

```php
class LoginUser
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 900;

    public function execute(LoginData $data): User
    {
        $user = User::where('email', $data->email->value)->first();

        if (!$user || !Hash::check($data->password, $user->password)) {
            $this->incrementFailedAttempts($user);
            throw new AuthenticationException('Invalid credentials');
        }

        if ($user->is_blocked) {
            throw new AuthenticationException('Account is blocked');
        }

        if ($user->locked_until && $user->locked_until->isFuture()) {
            throw new AuthenticationException('Account is locked');
        }

        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login' => now(),
        ]);

        Auth::login($user, $data->remember);

        return $user;
    }

    private function incrementFailedAttempts(?User $user): void
    {
        if (!$user) return;

        $attempts = $user->failed_login_attempts + 1;
        $updates = ['failed_login_attempts' => $attempts];

        if ($attempts >= self::MAX_ATTEMPTS) {
            $updates['locked_until'] = now()->addSeconds(self::LOCKOUT_SECONDS);
        }

        $user->update($updates);
    }
}
```

### 6.3 CompleteLesson

```php
class CompleteLesson
{
    public function execute(User $user, int $lessonId): ProgressPercent
    {
        $lesson = Lesson::findOrFail($lessonId);

        UserLessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId],
            ['completed' => true, 'completed_at' => now()]
        );

        $completedCount = UserLessonProgress::where('user_id', $user->id)
            ->where('lesson_id', fn($q) => $q->whereIn('id', $lesson->course->lessons->pluck('id')))
            ->where('completed', true)
            ->count();

        $totalLessons = $lesson->course->lessons()->count();

        $progress = ProgressPercent::calculate($completedCount, $totalLessons);

        UserCourseProgress::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $lesson->course_id],
            [
                'progress' => $progress->value,
                'completed' => $progress->isComplete(),
                'started_at' => now(),
                'completed_at' => $progress->isComplete() ? now() : null,
            ]
        );

        event(new LessonCompleted($user, $lesson, $progress));

        if ($progress->isComplete()) {
            event(new CourseCompleted($user, $lesson->course));
        }

        return $progress;
    }
}
```

### 6.4 SubmitExam

```php
class SubmitExam
{
    public function execute(User $user, ExamSubmissionData $data): array
    {
        $exam = CourseExam::where('course_id', $data->courseId)->firstOrFail();

        $correct = 0;
        $total = count($exam->exam_json['questions'] ?? []);

        foreach ($exam->exam_json['questions'] as $index => $question) {
            $userAnswer = $data->answers[$index] ?? null;
            if ($this->isCorrect($question, $userAnswer)) {
                $correct++;
            }
        }

        $score = $total > 0 ? round(($correct / $total) * 100) : 0;
        $passed = $score >= $exam->pass_percent;

        if ($passed) {
            $hash = CertificateHash::generate();

            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $data->courseId,
                'cert_hash' => $hash->value,
                'certificate_name' => $user->name . ' — ' . $exam->course->title,
                'issuer' => 'CodeMaster',
                'issue_date' => now()->toDateString(),
            ]);

            event(new ExamPassed($user, $exam->course, $score));
            event(new CertificateIssued($certificate));
        }

        return [
            'score' => $score,
            'passed' => $passed,
            'correct' => $correct,
            'total' => $total,
            'certificate_hash' => $passed ? $hash->value : null,
        ];
    }

    private function isCorrect(array $question, ?string $userAnswer): bool
    {
        if (isset($question['correct_options'])) {
            return in_array($userAnswer, $question['correct_options']);
        }
        return $userAnswer === $question['correct_option'];
    }
}
```

### 6.5 ApplyToVacancy

```php
class ApplyToVacancy
{
    public function execute(User $user, int $vacancyId): UserApplication
    {
        $exists = UserApplication::where('user_id', $user->id)
            ->where('vacancy_id', $vacancyId)
            ->exists();

        if ($exists) {
            throw new \DomainException('You have already applied to this vacancy');
        }

        $application = UserApplication::create([
            'user_id' => $user->id,
            'vacancy_id' => $vacancyId,
            'status' => ApplicationStatus::Applied,
            'applied_at' => now(),
        ]);

        VacancyChat::create([
            'application_id' => $application->id,
            'sender_id' => $user->id,
            'message_text' => 'Здравствуйте! Я откликнулся на вашу вакансию.',
        ]);

        event(new VacancyApplied($application));

        return $application;
    }
}
```

### 6.6 StartInterview

```php
class StartInterview
{
    public function __construct(
        private readonly GeminiService $gemini,
    ) {}

    public function execute(User $user, InterviewType $type, InterviewDifficulty $difficulty): Interview
    {
        $interview = Interview::create([
            'user_id' => $user->id,
            'title' => $type->label() . ' — ' . $difficulty->label(),
            'type' => $type,
            'difficulty' => $difficulty,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $questions = $this->gemini->generateInterviewQuestions($type, $difficulty);

        Cache::put("interview_{$interview->id}_questions", $questions, now()->addHours(2));

        return $interview;
    }
}
```

---

## 7. Policies

### 7.1 CommunityPostPolicy

```php
class CommunityPostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CommunityPost $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CommunityPost $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, CommunityPost $post): bool
    {
        return $user->id === $post->user_id || $user->role->isAdmin();
    }
}
```

### 7.2 VacancyPolicy

```php
class VacancyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role->canPublishVacancies();
    }

    public function update(User $user, Vacancy $vacancy): bool
    {
        return $user->id === $vacancy->owner_id || $user->role->isAdmin();
    }

    public function delete(User $user, Vacancy $vacancy): bool
    {
        return $user->role->isAdmin();
    }
}
```

### 7.3 ProfilePolicy

```php
class ProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function update(User $user, User $profile): bool
    {
        return $user->id === $profile->id;
    }

    public function uploadAvatar(User $user, User $profile): bool
    {
        return $user->id === $profile->id;
    }
}
```

### 7.4 CertificatePolicy

```php
class CertificatePolicy
{
    public function view(User $user, Certificate $certificate): bool
    {
        return true; // Публичные сертификаты
    }

    public function download(User $user, Certificate $certificate): bool
    {
        return $user->id === $certificate->user_id || $user->role->isAdmin();
    }
}
```

---

## 8. Events & Listeners

### 8.1 EventServiceProvider

```php
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            SendWelcomeNotification::class,
            LogUserActivity::class,
        ],
        LessonCompleted::class => [
            UpdateCourseProgress::class,
            LogUserActivity::class,
        ],
        CourseCompleted::class => [
            LogUserActivity::class,
        ],
        CertificateIssued::class => [
            IssueCertificate::class,
            LogUserActivity::class,
        ],
        VacancyApplied::class => [
            NotifyRecruiter::class,
            LogUserActivity::class,
        ],
        ChatMessageSent::class => [
            TrimChatHistory::class,
        ],
        InterviewCompleted::class => [
            LogUserActivity::class,
        ],
        PostCreated::class => [
            LogUserActivity::class,
        ],
    ];
}
```

### 8.2 Ключевые Events

```php
class UserRegistered
{
    public function __construct(public readonly User $user) {}
}

class LessonCompleted
{
    public function __construct(
        public readonly User $user,
        public readonly Lesson $lesson,
        public readonly ProgressPercent $progress,
    ) {}
}

class CourseCompleted
{
    public function __construct(
        public readonly User $user,
        public readonly Course $course,
    ) {}
}

class CertificateIssued
{
    public function __construct(public readonly Certificate $certificate) {}
}

class VacancyApplied
{
    public function __construct(public readonly UserApplication $application) {}
}
```

### 8.3 Ключевые Listeners

```php
class SendWelcomeNotification
{
    public function handle(UserRegistered $event): void
    {
        Notification::send($event->user, new WelcomeNotification());
    }
}

class UpdateCourseProgress
{
    public function handle(LessonCompleted $event): void
    {
        $course = $event->lesson->course;
        $completed = $course->lessons()
            ->whereHas('userProgress', fn($q) => $q->where('user_id', $event->user->id)->where('completed', true))
            ->count();
        $total = $course->lessons()->count();
        $progress = ProgressPercent::calculate($completed, $total);

        UserCourseProgress::updateOrCreate(
            ['user_id' => $event->user->id, 'course_id' => $course->id],
            ['progress' => $progress->value, 'completed' => $progress->isComplete()]
        );
    }
}

class IssueCertificate
{
    public function handle(CertificateIssued $event): void
    {
        // Генерация изображения сертификата (если нужно)
        // GenerateCertificateImage::dispatch($event->certificate);
    }
}

class NotifyRecruiter
{
    public function handle(VacancyApplied $event): void
    {
        $vacancy = $event->application->vacancy;
        $owner = $vacancy->owner;
        if ($owner) {
            Notification::send($owner, new NewApplicationNotification($event->application));
        }
    }
}

class TrimChatHistory
{
    public function handle(ChatMessageSent $event): void
    {
        $total = ChatMessage::where('user_id', $event->message->user_id)->count();
        if ($total > 50) {
            $toDelete = ChatMessage::where('user_id', $event->message->user_id)
                ->orderBy('created_at', 'asc')
                ->limit($total - 50)
                ->pluck('id');
            ChatMessage::whereIn('id', $toDelete)->delete();
        }
    }
}

class LogUserActivity
{
    public function handle(object $event): void
    {
        $user = match(get_class($event)) {
            UserRegistered::class => $event->user,
            LessonCompleted::class => $event->user,
            CourseCompleted::class => $event->user,
            CertificateIssued::class => $event->certificate->user,
            VacancyApplied::class => $event->application->user,
            InterviewCompleted::class => $event->user,
            PostCreated::class => $event->post->user,
            default => null,
        };

        if ($user) {
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => class_basename($event),
                'activity_text' => $this->describeEvent($event),
                'activity_time' => now(),
            ]);
        }
    }
}
```

---

## 9. Jobs & Queues

### 9.1 SendGeminiRequest

```php
class SendGeminiRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $message,
        public readonly ?string $context = null,
    ) {}

    public function handle(GeminiService $gemini): void
    {
        $contents = $gemini->buildContents($this->userId, $this->message, $this->context);
        $response = $gemini->callApi($contents);

        $reply = $response['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';

        ChatMessage::create([
            'user_id' => $this->userId,
            'sender' => 'assistant',
            'message_text' => $reply,
            'sent_at' => now(),
        ]);

        $gemini->trimUserChatMessages($this->userId, 50);
    }
}
```

### 9.2 ExecuteJudge0Code

```php
class ExecuteJudge0Code implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $code,
        public readonly string $language,
        public readonly array $tests = [],
    ) {}

    public function handle(Judge0Service $judge0): array
    {
        if (!empty($this->tests)) {
            return $judge0->runPractice($this->language, $this->code, $this->tests);
        }

        return $judge0->submitAndWait([
            'source_code' => $this->code,
            'language_id' => $judge0->resolveLanguageId($this->language),
        ]);
    }
}
```

---

## 10. Notifications

### 10.1 WelcomeNotification

```php
class WelcomeNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Добро пожаловать в CodeMaster! Начните обучение прямо сейчас.',
            'type' => 'welcome',
        ];
    }
}
```

### 10.2 CourseCompletedNotification

```php
class CourseCompletedNotification extends Notification
{
    public function __construct(private readonly Course $course) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Поздравляем! Вы завершили курс «{$this->course->title}»",
            'type' => 'course_completed',
            'course_id' => $this->course->id,
        ];
    }
}
```

---

## 11. Repositories

### 11.1 CourseRepository

```php
class CourseRepository
{
    public function __construct(
        private readonly Course $model,
    ) {}

    public function paginateWithFilters(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->withCount('lessons');

        if ($category = $filters['category'] ?? null) {
            $query->where('category', $category);
        }

        if ($level = $filters['level'] ?? null) {
            $query->where('level', $level);
        }

        if ($search = $filters['search'] ?? null) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findWithLessons(int $id): Course
    {
        return $this->model->with(['lessons.quizQuestions.options', 'lessons.practiceTasks', 'courseSkills', 'exam'])
            ->findOrFail($id);
    }

    public function findForExam(int $id): Course
    {
        return $this->model->with(['exam', 'lessons'])->findOrFail($id);
    }
}
```

### 11.2 VacancyRepository

```php
class VacancyRepository
{
    public function paginateWithFilters(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = Vacancy::with(['vacancySkills', 'requirements']);

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($type = $filters['type'] ?? null) {
            $query->where('type', $type);
        }

        if ($location = $filters['location'] ?? null) {
            $query->where('location', 'like', "%{$location}%");
        }

        if ($skill = $filters['skill'] ?? null) {
            $query->whereHas('vacancySkills', fn($q) => $q->where('skill_name', $skill));
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
```

---

## 12. Form Requests

### 12.1 RegisterRequest

```php
class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['nullable', 'in:seeker,recruiter'],
            'skills' => ['nullable', 'array'],
            'skills.*.name' => ['required_with:skills', 'string'],
            'skills.*.level' => ['nullable', 'in:beginner,intermediate,advanced,expert'],
            'recaptcha_token' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Пользователь с таким email уже существует',
            'password.confirmed' => 'Пароли не совпадают',
            'password.min' => 'Пароль должен содержать минимум 6 символов',
        ];
    }
}
```

### 12.2 SubmitExamRequest

```php
class SubmitExamRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'string'],
        ];
    }
}
```

---

## 13. API Resources

### 13.1 CourseResource

```php
class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'instructor' => $this->instructor,
            'description' => $this->description,
            'category' => $this->category,
            'level' => $this->level,
            'image_url' => $this->image_url,
            'lessons_count' => $this->whenCounted('lessons'),
            'progress' => $this->when(
                $request->user(),
                fn() => $this->userProgress($request->user())->progress ?? 0
            ),
        ];
    }
}
```

### 13.2 CertificateResource

```php
class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hash' => $this->cert_hash,
            'name' => $this->certificate_name,
            'issuer' => $this->issuer,
            'issue_date' => $this->issue_date,
            'course' => new CourseResource($this->whenLoaded('course')),
            'user' => new ProfileResource($this->whenLoaded('user')),
            'url' => route('certificate.show', $this->cert_hash),
        ];
    }
}
```

---

## 14. Middleware

### 14.1 EnsureUserIsBlocked

```php
class EnsureUserIsBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->is_blocked) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Ваш аккаунт заблокирован');
        }

        return $next($request);
    }
}
```

### 14.2 TrackLastActivity

```php
class TrackLastActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $request->user()->update(['last_login' => now()]);
        }

        return $next($request);
    }
}
```

---

## 15. Controllers (Resource Controllers)

### 15.1 CourseController

```php
class CourseController extends Controller
{
    public function __construct(
        private readonly CourseRepository $courses,
    ) {}

    public function index(Request $request): View
    {
        $courses = $this->courses->paginateWithFilters($request->only(['category', 'level', 'search']));
        return view('courses.index', compact('courses'));
    }

    public function show(int $id): View
    {
        $course = $this->courses->findWithLessons($id);
        return view('courses.show', compact('course'));
    }

    public function completeLesson(CompleteLessonRequest $request): JsonResponse
    {
        $progress = app(CompleteLesson::class)->execute(
            $request->user(),
            $request->validated('lesson_id')
        );

        return response()->json(['progress' => $progress->value]);
    }
}
```

### 15.2 ExamController

```php
class ExamController extends Controller
{
    public function show(int $id): View
    {
        $course = Course::with('exam')->findOrFail($id);
        return view('courses.exam', compact('course'));
    }

    public function submit(SubmitExamRequest $request, int $id): View
    {
        $result = app(SubmitExam::class)->execute(
            $request->user(),
            ExamSubmissionData::fromRequest($request, $id)
        );

        return view('courses.exam-result', $result);
    }
}
```

### 15.3 VacancyController

```php
class VacancyController extends Controller
{
    public function index(Request $request): View
    {
        $vacancies = app(VacancyRepository::class)
            ->paginateWithFilters($request->only(['search', 'type', 'location', 'skill']));
        return view('vacancies.index', compact('vacancies'));
    }

    public function show(int $id): View
    {
        $vacancy = Vacancy::with(['vacancySkills', 'requirements', 'pluses', 'responsibilities'])
            ->findOrFail($id);
        return view('vacancies.show', compact('vacancy'));
    }

    public function apply(int $id): JsonResponse
    {
        $application = app(ApplyToVacancy::class)->execute(auth()->user(), $id);
        return response()->json(['application_id' => $application->id]);
    }
}
```

---

## 16. Сервисный слой

### 16.1 GeminiService

```php
class GeminiService
{
    private array $apiKeys;
    private int $currentKeyIndex = 0;

    public function __construct()
    {
        $this->apiKeys = array_filter(explode(',', config('services.gemini.keys', '')));
    }

    public function chat(int $userId, string $message, ?string $context = null): string
    {
        $contents = $this->buildContents($userId, $message, $context);
        $response = $this->callApi($contents);
        return $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    public function generateInterviewQuestions(InterviewType $type, InterviewDifficulty $difficulty): array
    {
        $prompt = "Generate 5 {$type->value} interview questions at {$difficulty->value} level. Return as JSON array.";
        $response = $this->callApi([['role' => 'user', 'parts' => [['text' => $prompt]]]]);
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
        return json_decode($text, true) ?? [];
    }

    public function evaluateInterviewAnswer(string $question, string $answer): array
    {
        $prompt = "Evaluate this interview answer on a scale of 0-100. Question: {$question}. Answer: {$answer}. Return JSON with score, strengths, improvements, feedback.";
        $response = $this->callApi([['role' => 'user', 'parts' => [['text' => $prompt]]]]);
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        return json_decode($text, true) ?? [];
    }

    public function buildContents(int $userId, string $message, ?string $context = null): array
    {
        $history = ChatMessage::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->limit(20)
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->sender === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg->message_text]],
            ])
            ->toArray();

        $systemInstruction = 'You are an AI tutor for CodeMaster.';
        if ($context) {
            $systemInstruction .= "\n\nContext: {$context}";
        }

        return array_merge([
            ['role' => 'user', 'parts' => [['text' => $systemInstruction]]],
            ['role' => 'model', 'parts' => [['text' => 'I understand. I am your AI tutor.']]],
        ], $history, [
            ['role' => 'user', 'parts' => [['text' => $message]]],
        ]);
    }

    public function callApi(array $contents, array $configOverrides = []): array
    {
        $maxRetries = min(3, count($this->apiKeys));

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            $key = $this->keyPool();
            $model = config('services.gemini.model', 'gemini-2.5-flash');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";

            try {
                $response = Http::timeout(60)->post($url, [
                    'contents' => $contents,
                    'generationConfig' => array_merge([
                        'temperature' => 0.7,
                        'maxOutputTokens' => 2048,
                    ], $configOverrides),
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                if (in_array($response->status(), [429, 503])) {
                    usleep(500000);
                    continue;
                }
                break;
            } catch (\Exception $e) {
                continue;
            }
        }

        return ['candidates' => [['content' => ['parts' => [['text' => 'Service temporarily unavailable.']]]]]];
    }

    private function keyPool(): string
    {
        $key = $this->apiKeys[$this->currentKeyIndex % count($this->apiKeys)];
        $this->currentKeyIndex++;
        return $key;
    }

    public function trimChatHistory(int $userId, int $keepLast = 50): void
    {
        $total = ChatMessage::where('user_id', $userId)->count();
        if ($total > $keepLast) {
            $toDelete = ChatMessage::where('user_id', $userId)
                ->orderBy('created_at', 'asc')
                ->limit($total - $keepLast)
                ->pluck('id');
            ChatMessage::whereIn('id', $toDelete)->delete();
        }
    }
}
```

### 16.2 Judge0Service

```php
class Judge0Service
{
    public function submitAndWait(array $payload): array
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Key' => config('services.judge0.token'),
            'X-RapidAPI-Host' => 'judge0-ce.p.rapidapi.com',
        ])->timeout(30)->post(
            config('services.judge0.url') . '/submissions?base64_encoded=false&wait=true',
            $payload
        );

        return $response->successful()
            ? $response->json()
            : ['stdout' => null, 'stderr' => 'Execution service unavailable.'];
    }

    public function resolveLanguageId(string $lang): int
    {
        $map = [
            'javascript' => 63, 'js' => 63, 'python' => 71, 'py' => 71,
            'java' => 62, 'cpp' => 54, 'c++' => 54, 'c' => 50,
            'php' => 68, 'ruby' => 73, 'go' => 60, 'rust' => 73,
            'typescript' => 74, 'ts' => 74, 'sql' => 82,
            'html' => 61, 'css' => 61,
        ];

        return $map[strtolower(trim($lang))] ?? 63;
    }

    public function runPractice(string $lang, string $code, array $tests): array
    {
        $languageId = $this->resolveLanguageId($lang);
        $results = [];

        foreach ($tests as $index => $test) {
            $result = $this->submitAndWait([
                'source_code' => $code,
                'language_id' => $languageId,
                'stdin' => $test['input'] ?? '',
                'expected_output' => $test['expected'] ?? '',
            ]);

            $results[] = [
                'test_case' => $index + 1,
                'passed' => trim($result['stdout'] ?? '') === trim($test['expected'] ?? ''),
                'input' => $test['input'] ?? '',
                'expected' => $test['expected'] ?? '',
                'output' => $result['stdout'] ?? '',
                'error' => $result['stderr'] ?? null,
            ];
        }

        return [
            'status' => collect($results)->every('passed') ? 'accepted' : 'wrong_answer',
            'results' => $results,
            'total_tests' => count($tests),
            'passed_tests' => collect($results)->where('passed', true)->count(),
        ];
    }
}
```

---

## 17. Routes

```php
Route::get('/lang/{locale}', fn(string $locale) => ...)->name('lang.switch');
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/google-login', [LoginController::class, 'loginGoogle']);
    // Password reset routes...
});

Route::middleware(['auth', 'ensure.blocked'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/complete-lesson', [CourseController::class, 'completeLesson'])->name('courses.completeLesson');

    // Exams
    Route::get('/courses/{course}/exam', [ExamController::class, 'show'])->name('courses.exam');
    Route::post('/courses/{course}/exam/submit', [ExamController::class, 'submit'])->name('courses.exam.submit');

    // Vacancies
    Route::resource('vacancies', VacancyController::class)->only(['index', 'show']);
    Route::post('/vacancies/{vacancy}/apply', [VacancyController::class, 'apply'])->name('vacancies.apply');

    // Vacancy Chat
    Route::get('/vacancy-chat/{application}', [VacancyChatController::class, 'show'])->name('vacancyChat.show');
    Route::post('/vacancy-chat', [VacancyChatController::class, 'store'])->name('vacancyChat.store');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // CRUD routes for skills, experience, education, portfolio...

    // Certificates
    Route::get('/certificate/{hash}', [CertificateController::class, 'show'])->name('certificate.show');
    Route::get('/certificate/{hash}/download', [CertificateController::class, 'download'])->name('certificate.download');

    // Roadmaps
    Route::get('/roadmaps', [RoadmapController::class, 'index'])->name('roadmaps.index');
    Route::get('/roadmap/{title}', [RoadmapController::class, 'show'])->name('roadmap.show');
    Route::post('/roadmap/complete-node', [RoadmapController::class, 'completeNode'])->name('roadmap.completeNode');

    // Community
    Route::resource('community', CommunityController::class)->except(['edit', 'create']);
    Route::post('/community/comment', [CommunityController::class, 'comment'])->name('community.comment');
    Route::post('/community/{post}/like', [CommunityController::class, 'like'])->name('community.like');

    // Interview
    Route::resource('interview', InterviewController::class)->except(['edit', 'update']);
    Route::post('/interview/{interview}/answer', [InterviewController::class, 'answer'])->name('interview.answer');
    Route::get('/interview/{interview}/result', [InterviewController::class, 'result'])->name('interview.result');

    // Practice
    Route::post('/practice/submit', [PracticeController::class, 'submit'])->name('practice.submit');

    // AI Tutor
    Route::post('/ai/chat', [AiTutorController::class, 'chat'])->name('ai.chat');
    Route::get('/ai/history', [AiTutorController::class, 'getChat'])->name('ai.history');
    Route::post('/ai/clear', [AiTutorController::class, 'clearChat'])->name('ai.clear');

    // Notifications
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');

    // Ratings
    Route::get('/ratings', [RatingController::class, 'index'])->name('ratings.index');
});

// Admin
Route::middleware(['auth', 'admin', 'ensure.blocked'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::resource('courses', AdminCourseController::class)->except(['show']);
    Route::resource('lessons', AdminLessonController::class)->except(['show', 'index']);
    Route::resource('vacancies', AdminVacancyController::class)->except(['show']);
    Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.delete');
});
```

---

## 18. Тестирование

### 18.1 Unit Tests

```
tests/Unit/
├── ValueObjects/
│   ├── MoneyTest.php
│   ├── CertificateHashTest.php
│   └── ProgressPercentTest.php
├── Enums/
│   ├── UserRoleTest.php
│   ├── CourseCategoryTest.php
│   └── InterviewTypeTest.php
├── Actions/
│   ├── RegisterUserTest.php
│   ├── LoginUserTest.php
│   ├── CompleteLessonTest.php
│   ├── SubmitExamTest.php
│   ├── ApplyToVacancyTest.php
│   └── StartInterviewTest.php
└── Services/
    ├── GeminiServiceTest.php
    └── Judge0ServiceTest.php
```

### 18.2 Feature Tests

```
tests/Feature/
├── Auth/
│   ├── RegistrationTest.php
│   ├── LoginTest.php
│   ├── GoogleOAuthTest.php
│   └── PasswordResetTest.php
├── Education/
│   ├── CourseCatalogTest.php
│   ├── LessonCompletionTest.php
│   ├── ExamSubmissionTest.php
│   └── CertificateDownloadTest.php
├── Career/
│   ├── VacancyListingTest.php
│   ├── ApplicationTest.php
│   └── VacancyChatTest.php
├── AI/
│   ├── AiTutorTest.php
│   └── InterviewTest.php
├── Community/
│   ├── PostCrudTest.php
│   ├── CommentTest.php
│   └── LikeTest.php
├── Profile/
│   ├── ProfileUpdateTest.php
│   ├── SkillManagementTest.php
│   └── PortfolioTest.php
└── Admin/
    ├── DashboardTest.php
    ├── UserManagementTest.php
    ├── CourseManagementTest.php
    └── VacancyManagementTest.php
```

### 18.3 Пример Feature Test

```php
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'seeker',
        ]);
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_is_blocked_after_5_failed_attempts(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong',
            ]);
        }

        $user->refresh();
        $this->assertTrue($user->is_blocked || $user->locked_until !== null);
    }
}
```

---

## 19. Конфигурация провайдеров

### 19.1 RepositoryServiceProvider

```php
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CourseRepository::class, function () {
            return new CourseRepository(new Course());
        });

        $this->app->bind(VacancyRepository::class, function () {
            return new VacancyRepository();
        });
    }
}
```

### 19.2 AppServiceProvider

```php
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GeminiService::class);
        $this->app->singleton(Judge0Service::class);
        $this->app->singleton(GoogleAuthService::class);
        $this->app->singleton(RecaptchaService::class);
        $this->app->singleton(I18nService::class);
    }

    public function boot(): void
    {
        // Register policies
        // Register view composers
        // Register Blade directives
    }
}
```

---

## 20. Сводка: Ключевые паттерны

| Паттерн | Где используется | Зачем |
|---------|-----------------|-------|
| **DDD** | Доменные сущности (Models), Enums, Value Objects | Бизнес-логика в центре |
| **SOLID** | Каждый класс — одна ответственность | Поддерживаемость |
| **Clean Architecture** | Слои: Presentation → Application → Domain → Infrastructure | Зависимости внутрь |
| **Service Layer** | GeminiService, Judge0Service, CertificateService | Внешние интеграции |
| **Repository** | CourseRepository, VacancyRepository | Чтение данных (CQRS-lite) |
| **Action Pattern** | RegisterUser, CompleteLesson, SubmitExam, ApplyToVacancy | Комплексные операции |
| **DTO** | RegistrationData, CourseData, ExamSubmissionData | Передача данных |
| **Value Object** | Money, CertificateHash, ProgressPercent | Неизменяемые значения |
| **Enum** | UserRole, CourseCategory, InterviewType | Типизированные константы |
| **Event/Listener** | UserRegistered → SendWelcomeNotification | Слабая связанность |
| **Job/Queue** | SendGeminiRequest, ExecuteJudge0Code | Асинхронность |
| **Policy** | CommunityPostPolicy, VacancyPolicy, ProfilePolicy | Авторизация |
| **Form Request** | RegisterRequest, SubmitExamRequest | Валидация |
| **API Resource** | CourseResource, CertificateResource | Формат ответа |
| **Middleware** | SetLocale, EnsureUserIsAdmin, EnsureUserIsBlocked | Прослойки |
| **Cache** | Рейтинги, статистика, AI-история | Производительность |
| **DB Transaction** | Все записывающие операции | Целостность данных |
