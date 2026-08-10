# Laravel 12 Architecture Design
## CodeMaster — Modern DDD / Clean Architecture

**Version:** 1.0
**Date:** 2026-08-01

---

## 1. Architecture Philosophy

### 1.1 Design Principles

| Principle | Application |
|-----------|-------------|
| **DDD** | Domain-centric organization with bounded contexts |
| **SOLID** | Single responsibility, dependency inversion, interface segregation |
| **Clean Architecture** | Dependencies point inward: Controllers → Actions → Services → Domain |
| **Service Layer** | Business logic extracted from controllers |
| **Repository Pattern** | Only where abstraction adds value (external APIs, complex queries) |
| **Policies** | Authorization logic isolated from controllers |
| **Form Requests** | Validation extracted from controllers |
| **DTOs** | Typed data transfer between layers |
| **Actions** | Single-purpose business operations |
| **Events/Listeners** | Decoupled side effects |
| **Jobs/Queues** | Async processing for slow operations |
| **Notifications** | User-facing notifications via multiple channels |
| **Caching** | Strategic caching for read-heavy data |
| **Enums** | Type-safe status/type values |
| **Value Objects** | Immutable domain values |

### 1.2 Dependency Rule

```
Presentation → Application → Domain → Infrastructure

Each layer depends ONLY on layers inside it.
Outer layers can use inner layers.
Inner layers NEVER use outer layers.
```

---

## 2. Directory Structure

```
app/
├── Console/
│   └── Commands/                    # Artisan commands
├── Domain/                          # Domain Layer (core business logic)
│   ├── Auth/
│   │   ├── Actions/
│   │   │   ├── RegisterUserAction.php
│   │   │   ├── LoginUserAction.php
│   │   │   ├── LoginWithGoogleAction.php
│   │   │   └── ResetPasswordAction.php
│   │   ├── Events/
│   │   │   ├── UserRegistered.php
│   │   │   └── UserLoggedIn.php
│   │   ├── Listeners/
│   │   │   ├── SendWelcomeNotification.php
│   │   │   └── LogUserActivity.php
│   │   └── Policies/
│   │       └── UserPolicy.php
│   ├── Course/
│   │   ├── Actions/
│   │   │   ├── CompleteLessonAction.php
│   │   │   ├── SubmitExamAction.php
│   │   │   └── EnrollCourseAction.php
│   │   ├── Events/
│   │   │   ├── LessonCompleted.php
│   │   │   ├── CourseCompleted.php
│   │   │   └── CertificateEarned.php
│   │   ├── Listeners/
│   │   │   ├── RecalculateProgress.php
│   │   │   ├── IssueCertificate.php
│   │   │   └── NotifyCourseCompletion.php
│   │   └── Policies/
│   │       └── CoursePolicy.php
│   ├── Vacancy/
│   │   ├── Actions/
│   │   │   ├── ApplyToVacancyAction.php
│   │   │   ├── SendChatMessageAction.php
│   │   │   └── UploadDocumentAction.php
│   │   ├── Events/
│   │   │   ├── ApplicationSubmitted.php
│   │   │   └── ChatMessageSent.php
│   │   ├── Listeners/
│   │   │   ├── CreateVacancyChat.php
│   │   │   └── NotifyRecruiter.php
│   │   └── Policies/
│   │       ├── VacancyPolicy.php
│   │       └── VacancyChatPolicy.php
│   ├── AI/
│   │   ├── Actions/
│   │   │   ├── SendChatMessageAction.php
│   │   │   ├── StartInterviewAction.php
│   │   │   ├── AnswerInterviewQuestionAction.php
│   │   │   └── CompleteInterviewAction.php
│   │   ├── Events/
│   │   │   ├── InterviewCompleted.php
│   │   │   └── InterviewScored.php
│   │   └── Listeners/
│   │       └── NotifyInterviewResult.php
│   ├── Community/
│   │   ├── Actions/
│   │   │   ├── CreatePostAction.php
│   │   │   ├── UpdatePostAction.php
│   │   │   ├── DeletePostAction.php
│   │   │   ├── AddCommentAction.php
│   │   │   └── ToggleLikeAction.php
│   │   ├── Events/
│   │   │   ├── PostCreated.php
│   │   │   └── PostLiked.php
│   │   └── Policies/
│   │       └── CommunityPostPolicy.php
│   ├── Profile/
│   │   ├── Actions/
│   │   │   ├── UpdateProfileAction.php
│   │   │   ├── AddSkillAction.php
│   │   │   ├── AddExperienceAction.php
│   │   │   ├── AddEducationAction.php
│   │   │   ├── AddPortfolioItemAction.php
│   │   │   └── UploadAvatarAction.php
│   │   └── Policies/
│   │       └── ProfilePolicy.php
│   ├── Roadmap/
│   │   ├── Actions/
│   │   │   └── CompleteRoadmapNodeAction.php
│   │   ├── Events/
│   │   │   ├── RoadmapNodeCompleted.php
│   │   │   └── RoadmapCompleted.php
│   │   └── Listeners/
│   │       └── IssueRoadmapCertificate.php
│   └── Shared/
│       ├── Enums/
│       │   ├── UserRole.php
│       │   ├── CourseCategory.php
│       │   ├── CourseLevel.php
│       │   ├── LessonType.php
│       │   ├── VacancyType.php
│       │   ├── ApplicationStatus.php
│       │   ├── InterviewType.php
│       │   ├── InterviewDifficulty.php
│       │   ├── InterviewStatus.php
│       │   ├── SkillLevel.php
│       │   ├── SkillCategory.php
│       │   ├── ChatMessageSender.php
│       │   ├── ActivityType.php
│       │   └── EmploymentStatus.php
│       ├── ValueObjects/
│       │   ├── Email.php
│       │   ├── Password.php
│       │   ├── Money.php
│       │   ├── CertificateHash.php
│       │   ├── ProgressPercent.php
│       │   └── Score.php
│       ├── DTOs/
│       │   ├── UserData.php
│       │   ├── CourseData.php
│       │   ├── VacancyData.php
│       │   ├── CertificateData.php
│       │   ├── InterviewData.php
│       │   ├── ChatMessageData.php
│       │   └── CodeExecutionResult.php
│       └── Exceptions/
│           ├── AccountLockedException.php
│           ├── AccountBlockedException.php
│           ├── DuplicateApplicationException.php
│           └── InsufficientAiCoinsException.php
├── App/                              # Application Layer (use cases)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Web/
│   │   │   │   ├── Auth/
│   │   │   │   │   ├── LoginController.php
│   │   │   │   │   ├── RegisterController.php
│   │   │   │   │   ├── ForgotPasswordController.php
│   │   │   │   │   └── ResetPasswordController.php
│   │   │   │   ├── Dashboard/
│   │   │   │   │   └── DashboardController.php
│   │   │   │   ├── Course/
│   │   │   │   │   ├── CourseController.php
│   │   │   │   │   └── ExamController.php
│   │   │   │   ├── Vacancy/
│   │   │   │   │   ├── VacancyController.php
│   │   │   │   │   └── VacancyChatController.php
│   │   │   │   ├── Roadmap/
│   │   │   │   │   └── RoadmapController.php
│   │   │   │   ├── AI/
│   │   │   │   │   ├── AiTutorController.php
│   │   │   │   │   └── InterviewController.php
│   │   │   │   ├── Community/
│   │   │   │   │   └── CommunityController.php
│   │   │   │   ├── Profile/
│   │   │   │   │   ├── ProfileController.php
│   │   │   │   │   ├── SkillController.php
│   │   │   │   │   ├── ExperienceController.php
│   │   │   │   │   ├── EducationController.php
│   │   │   │   │   └── PortfolioController.php
│   │   │   │   ├── Certificate/
│   │   │   │   │   └── CertificateController.php
│   │   │   │   ├── Rating/
│   │   │   │   │   └── RatingController.php
│   │   │   │   ├── Practice/
│   │   │   │   │   └── PracticeController.php
│   │   │   │   ├── Contest/
│   │   │   │   │   └── ContestController.php
│   │   │   │   ├── Notification/
│   │   │   │   │   └── NotificationController.php
│   │   │   │   └── Static/
│   │   │   │       └── StaticPageController.php
│   │   │   └── Admin/
│   │   │       ├── AdminDashboardController.php
│   │   │       ├── AdminUserController.php
│   │   │       ├── AdminCourseController.php
│   │   │       ├── AdminLessonController.php
│   │   │       ├── AdminVacancyController.php
│   │   │       └── AdminNotificationController.php
│   │   ├── Middleware/
│   │   │   ├── SetLocale.php
│   │   │   ├── EnsureUserIsAdmin.php
│   │   │   ├── EnsureUserIsNotBlocked.php
│   │   │   └── EnsureAccountIsNotLocked.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   ├── RegisterRequest.php
│   │   │   │   └── ResetPasswordRequest.php
│   │   │   ├── Course/
│   │   │   │   ├── CompleteLessonRequest.php
│   │   │   │   ├── SubmitExamRequest.php
│   │   │   │   └── FilterCoursesRequest.php
│   │   │   ├── Vacancy/
│   │   │   │   ├── ApplyToVacancyRequest.php
│   │   │   │   ├── SendChatMessageRequest.php
│   │   │   │   ├── UploadDocumentRequest.php
│   │   │   │   └── FilterVacanciesRequest.php
│   │   │   ├── AI/
│   │   │   │   ├── AiChatRequest.php
│   │   │   │   ├── StartInterviewRequest.php
│   │   │   │   └── AnswerInterviewRequest.php
│   │   │   ├── Community/
│   │   │   │   ├── CreatePostRequest.php
│   │   │   │   ├── UpdatePostRequest.php
│   │   │   │   └── AddCommentRequest.php
│   │   │   ├── Profile/
│   │   │   │   ├── UpdateProfileRequest.php
│   │   │   │   ├── ChangePasswordRequest.php
│   │   │   │   ├── AddSkillRequest.php
│   │   │   │   ├── AddExperienceRequest.php
│   │   │   │   ├── AddEducationRequest.php
│   │   │   │   ├── AddPortfolioRequest.php
│   │   │   │   └── UploadAvatarRequest.php
│   │   │   ├── Practice/
│   │   │   │   └── SubmitCodeRequest.php
│   │   │   └── Admin/
│   │   │       ├── CreateCourseRequest.php
│   │   │       ├── UpdateCourseRequest.php
│   │   │       ├── CreateLessonRequest.php
│   │   │       ├── CreateVacancyRequest.php
│   │   │       ├── CreateAdminUserRequest.php
│   │   │       └── UpdateAdminUserRequest.php
│   │   ├── Resources/
│   │   │   ├── CourseResource.php
│   │   │   ├── CourseCollection.php
│   │   │   ├── VacancyResource.php
│   │   │   ├── VacancyCollection.php
│   │   │   ├── UserResource.php
│   │   │   ├── CertificateResource.php
│   │   │   ├── CommunityPostResource.php
│   │   │   ├── InterviewResource.php
│   │   │   └── NotificationResource.php
│   │   └── View/
│   │       └── Composers/
│   │           ├── DashboardComposer.php
│   │           ├── HeaderComposer.php
│   │           └── NotificationComposer.php
│   ├── Services/
│   │   ├── Interfaces/
│   │   │   ├── AiServiceInterface.php
│   │   │   ├── CodeExecutionServiceInterface.php
│   │   │   ├── AuthServiceInterface.php
│   │   │   ├── RecaptchaServiceInterface.php
│   │   │   └── TranslationServiceInterface.php
│   │   ├── GeminiAiService.php
│   │   ├── Judge0CodeExecutionService.php
│   │   ├── GoogleAuthService.php
│   │   ├── RecaptchaService.php
│   │   ├── I18nService.php
│   │   ├── CertificateService.php
│   │   └── NotificationService.php
│   ├── Repositories/
│   │   ├── Interfaces/
│   │   │   ├── UserRepositoryInterface.php
│   │   │   ├── CourseRepositoryInterface.php
│   │   │   ├── VacancyRepositoryInterface.php
│   │   │   └── CertificateRepositoryInterface.php
│   │   ├── Eloquent/
│   │   │   ├── EloquentUserRepository.php
│   │   │   ├── EloquentCourseRepository.php
│   │   │   ├── EloquentVacancyRepository.php
│   │   │   └── EloquentCertificateRepository.php
│   │   └── Cache/
│   │       ├── CachedCourseRepository.php
│   │       └── CachedRatingRepository.php
│   ├── Jobs/
│   │   ├── Ai/
│   │   │   ├── GenerateAiResponseJob.php
│   │   │   ├── GenerateInterviewQuestionJob.php
│   │   │   └── EvaluateInterviewAnswerJob.php
│   │   ├── Code/
│   │   │   └── ExecuteCodeJob.php
│   │   ├── Notification/
│   │   │   └── SendNotificationJob.php
│   │   └── Certificate/
│   │       └── GenerateCertificateJob.php
│   ├── Notifications/
│   │   ├── CourseCompletedNotification.php
│   │   ├── CertificateEarnedNotification.php
│   │   ├── ApplicationReceivedNotification.php
│   │   ├── InterviewCompletedNotification.php
│   │   └── NewMessageNotification.php
│   └── Events/
│       ├── Auth/
│       │   ├── UserRegistered.php
│       │   └── UserLoggedIn.php
│       ├── Course/
│       │   ├── LessonCompleted.php
│       │   ├── CourseCompleted.php
│       │   └── CertificateEarned.php
│       ├── Vacancy/
│       │   ├── ApplicationSubmitted.php
│       │   └── ChatMessageSent.php
│       ├── AI/
│       │   ├── InterviewCompleted.php
│       │   └── InterviewScored.php
│       ├── Community/
│       │   ├── PostCreated.php
│       │   └── PostLiked.php
│       └── Roadmap/
│           ├── RoadmapNodeCompleted.php
│           └── RoadmapCompleted.php
├── Infrastructure/                   # Infrastructure Layer
│   ├── Persistence/
│   │   ├── Eloquent/
│   │   │   ├── Models/
│   │   │   │   ├── User.php
│   │   │   │   ├── Course.php
│   │   │   │   ├── Lesson.php
│   │   │   │   ├── ... (all 43 models)
│   │   │   └── Scopes/
│   │   │       ├── ActiveCoursesScope.php
│   │   │       └── PublishedVacanciesScope.php
│   │   └── Migrations/
│   │       └── ... (all migrations)
│   ├── External/
│   │   ├── GeminiApi.php
│   │   ├── Judge0Api.php
│   │   ├── GoogleOAuthApi.php
│   │   └── RecaptchaApi.php
│   └── Cache/
│       └── CacheManager.php
├── Providers/
│   ├── AppServiceProvider.php
│   ├── AuthServiceProvider.php       # Policy registration
│   ├── EventServiceProvider.php      # Event-Listener mapping
│   └── RepositoryServiceProvider.php # Interface bindings
├── Helpers/
│   └── helpers.php                   # Global helper functions
config/
├── gemini.php                        # Gemini API config
├── judge0.php                        # Judge0 API config
├── services.php                      # External services config
routes/
├── web.php
├── admin.php                         # Admin routes (separate file)
└── console.php
```

---

## 3. Domain Layer Design

### 3.1 Enums (Type-Safe Values)

```php
// Domain/Shared/Enums/UserRole.php
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
}

// Domain/Shared/Enums/CourseCategory.php
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
}

// Domain/Shared/Enums/LessonType.php
enum LessonType: string
{
    case Video = 'video';
    case Article = 'article';
    case Quiz = 'quiz';
}

// Domain/Shared/Enums/VacancyType.php
enum VacancyType: string
{
    case Remote = 'remote';
    case Office = 'office';
    case Hybrid = 'hybrid';
}

// Domain/Shared/Enums/ApplicationStatus.php
enum ApplicationStatus: string
{
    case Applied = 'applied';
    case Interview = 'interview';
    case Offer = 'offer';
    case Rejected = 'rejected';
}

// Domain/Shared/Enums/InterviewType.php
enum InterviewType: string
{
    case Technical = 'technical';
    case Behavioral = 'behavioral';
    case Coding = 'coding';
    case SystemDesign = 'system_design';
}

// Domain/Shared/Enums/InterviewDifficulty.php
enum InterviewDifficulty: string
{
    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';
}

// Domain/Shared/Enums/InterviewStatus.php
enum InterviewStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Abandoned = 'abandoned';
}

// Domain/Shared/Enums/SkillLevel.php
enum SkillLevel: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Expert = 'expert';
}

// Domain/Shared/Enums/CourseLevel.php
enum CourseLevel: string
{
    case Beginner = 'Начальный';
    case Intermediate = 'Средний';
    case Advanced = 'Продвинутый';
}
```

### 3.2 Value Objects

```php
// Domain/Shared/ValueObjects/Email.php
final class Email
{
    public function __construct(
        public readonly string $value
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email: {$value}");
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

// Domain/Shared/ValueObjects/Money.php
final class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency = 'TJS'
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException("Amount cannot be negative");
        }
    }

    public function format(): string
    {
        return number_format($this->amount, 0, '.', ' ') . ' ' . $this->currency;
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException("Cannot add different currencies");
        }
        return new self($this->amount + $other->amount, $this->currency);
    }
}

// Domain/Shared/ValueObjects/ProgressPercent.php
final class ProgressPercent
{
    private int $value;

    public function __construct(int $completed, int $total)
    {
        if ($total <= 0) {
            $this->value = 0;
        } else {
            $this->value = (int) round(($completed / $total) * 100);
        }
    }

    public function isComplete(): bool
    {
        return $this->value >= 100;
    }

    public function value(): int
    {
        return min($this->value, 100);
    }
}

// Domain/Shared/ValueObjects/CertificateHash.php
final class CertificateHash
{
    public function __construct(
        public readonly string $value
    ) {
        if (strlen($value) !== 40) {
            throw new InvalidArgumentException("Certificate hash must be 40 characters");
        }
    }

    public static function generate(): self
    {
        return new self(Str::random(40));
    }
}

// Domain/Shared/ValueObjects/Score.php
final class Score
{
    public function __construct(
        public readonly int $value
    ) {
        if ($value < 0 || $value > 100) {
            throw new InvalidArgumentException("Score must be between 0 and 100");
        }
    }

    public function isPassed(int $passPercent = 70): bool
    {
        return $this->value >= $passPercent;
    }

    public function grade(): string
    {
        return match(true) {
            $this->value >= 90 => 'A',
            $this->value >= 80 => 'B',
            $this->value >= 70 => 'C',
            $this->value >= 60 => 'D',
            default => 'F',
        };
    }
}
```

### 3.3 DTOs

```php
// Domain/Shared/DTOs/UserData.php
final class UserData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly UserRole $role,
        public readonly ?string $avatar,
        public readonly int $aiCoins,
        public readonly bool $isBlocked,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role: UserRole::from($user->role),
            avatar: $user->avatar,
            aiCoins: $user->ai_coins,
            isBlocked: $user->is_blocked,
        );
    }
}

// Domain/Shared/DTOs/CourseData.php
final class CourseData
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $instructor,
        public readonly ?string $description,
        public readonly CourseCategory $category,
        public readonly CourseLevel $level,
        public readonly ?string $imageUrl,
        public readonly int $lessonCount,
        public readonly ?float $userProgress,
    ) {}
}

// Domain/Shared/DTOs/CodeExecutionResult.php
final class CodeExecutionResult
{
    public function __construct(
        public readonly string $stdout,
        public readonly string $stderr,
        public readonly string $status,
        public readonly ?float $time,
        public readonly ?int $memory,
    ) {}

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function hasError(): bool
    {
        return !empty($this->stderr);
    }
}

// Domain/Shared/DTOs/CertificateData.php
final class CertificateData
{
    public function __construct(
        public readonly int $id,
        public readonly string $userName,
        public readonly string $courseName,
        public readonly string $hash,
        public readonly string $issuer,
        public readonly Carbon $issuedAt,
    ) {}
}

// Domain/Shared/DTOs/InterviewData.php
final class InterviewData
{
    public function __construct(
        public readonly int $id,
        public readonly InterviewType $type,
        public readonly InterviewDifficulty $difficulty,
        public readonly InterviewStatus $status,
        public readonly ?int $score,
        public readonly ?string $feedback,
        public readonly int $questionIndex,
        public readonly int $totalQuestions,
    ) {}
}
```

### 3.4 Exceptions

```php
// Domain/Shared/Exceptions/AccountLockedException.php
class AccountLockedException extends RuntimeException
{
    public function __construct(public readonly int $secondsRemaining)
    {
        parent::__construct("Account locked for {$secondsRemaining} seconds");
    }
}

// Domain/Shared/Exceptions/AccountBlockedException.php
class AccountBlockedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("This account has been blocked");
    }
}

// Domain/Shared/Exceptions/DuplicateApplicationException.php
class DuplicateApplicationException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("You have already applied to this vacancy");
    }
}

// Domain/Shared/Exceptions/InsufficientAiCoinsException.php
class InsufficientAiCoinsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("Insufficient AI coins for this operation");
    }
}
```

---

## 4. Application Layer Design

### 4.1 Actions (Single-Purpose Operations)

```php
// Domain/Course/Actions/CompleteLessonAction.php
class CompleteLessonAction
{
    public function __construct(
        private readonly UserLessonProgressRepositoryInterface $progressRepo,
        private readonly CourseRepositoryInterface $courseRepo,
    ) {}

    public function execute(User $user, int $courseId, int $lessonId): array
    {
        DB::transaction(function () use ($user, $courseId, $lessonId) {
            // 1. Mark lesson complete
            $this->progressRepo->markComplete($user->id, $lessonId);

            // 2. Recalculate progress
            $completed = $this->courseRepo->countCompletedLessons($courseId, $user->id);
            $total = $this->courseRepo->countTotalLessons($courseId);
            $progress = new ProgressPercent($completed, $total);

            // 3. Update course progress
            $courseProgress = $this->progressRepo->updateCourseProgress(
                $user->id,
                $courseId,
                $progress->value(),
                $progress->isComplete()
            );

            // 4. Dispatch events
            event(new LessonCompleted($user, $lessonId, $courseId));

            if ($progress->isComplete()) {
                event(new CourseCompleted($user, $courseId));
            }

            return [
                'success' => true,
                'percent' => $progress->value(),
                'completed' => $progress->isComplete(),
            ];
        });
    }
}

// Domain/Vacancy/Actions/ApplyToVacancyAction.php
class ApplyToVacancyAction
{
    public function __construct(
        private readonly UserApplicationRepositoryInterface $applicationRepo,
    ) {
        $this->applicationRepo = $applicationRepo;
    }

    public function execute(User $user, Vacancy $vacancy): UserApplication
    {
        // Check for duplicate
        if ($this->applicationRepo->hasUserApplied($user->id, $vacancy->id)) {
            throw new DuplicateApplicationException();
        }

        return DB::transaction(function () use ($user, $vacancy) {
            // 1. Create application
            $application = $this->applicationRepo->create([
                'user_id' => $user->id,
                'vacancy_id' => $vacancy->id,
                'status' => ApplicationStatus::Applied,
                'applied_at' => now(),
            ]);

            // 2. Dispatch event
            event(new ApplicationSubmitted($user, $vacancy, $application));

            return $application;
        });
    }
}

// Domain/AI/Actions/SendChatMessageAction.php
class SendChatMessageAction
{
    public function __construct(
        private readonly AiServiceInterface $aiService,
        private readonly ChatMessageRepositoryInterface $messageRepo,
    ) {}

    public function execute(User $user, string $message, ?string $context = null): string
    {
        // 1. Save user message
        $this->messageRepo->create([
            'user_id' => $user->id,
            'sender' => ChatMessageSender::User,
            'message_text' => $message,
            'sent_at' => now(),
        ]);

        // 2. Trim history
        $this->messageRepo->trimHistory($user->id, keepLast: 50);

        // 3. Build context from history
        $contents = $this->aiService->buildContents($user->id, $message, $context);

        // 4. Call AI
        $response = $this->aiService->callApi($contents);

        // 5. Save AI response
        $this->messageRepo->create([
            'user_id' => $user->id,
            'sender' => ChatMessageSender::Ai,
            'message_text' => $response['text'],
            'sent_at' => now(),
        ]);

        return $response['text'];
    }
}

// Domain/AI/Actions/StartInterviewAction.php
class StartInterviewAction
{
    public function __construct(
        private readonly InterviewRepositoryInterface $interviewRepo,
        private readonly AiServiceInterface $aiService,
    ) {}

    public function execute(User $user, InterviewType $type, InterviewDifficulty $difficulty): Interview
    {
        $interview = $this->interviewRepo->create([
            'user_id' => $user->id,
            'title' => "{$type->label()} - {$difficulty->label()}",
            'type' => $type,
            'difficulty' => $difficulty,
            'status' => InterviewStatus::InProgress,
            'started_at' => now(),
        ]);

        return $interview;
    }
}

// Domain/AI/Actions/AnswerInterviewQuestionAction.php
class AnswerInterviewQuestionAction
{
    private const MAX_QUESTIONS = 5;

    public function __construct(
        private readonly InterviewRepositoryInterface $interviewRepo,
        private readonly AiServiceInterface $aiService,
    ) {}

    public function execute(User $user, Interview $interview, string $answer, array $question): array
    {
        // 1. Evaluate answer
        $evaluation = $this->aiService->evaluateAnswer($interview, $question, $answer);

        // 2. Check if interview complete
        $questionIndex = session('interview_question_index', 0) + 1;

        if ($questionIndex >= self::MAX_QUESTIONS) {
            return $this->completeInterview($interview, $questionIndex);
        }

        // 3. Generate next question
        $nextQuestion = $this->aiService->generateQuestion($interview);

        session([
            'interview_question_index' => $questionIndex,
            'interview_current_question' => $nextQuestion,
        ]);

        return $evaluation;
    }

    private function completeInterview(Interview $interview, int $questionCount): array
    {
        // Calculate average score, generate feedback
        $score = $this->calculateAverageScore($interview);
        $feedback = $this->aiService->generateFeedback($interview, $score);

        $this->interviewRepo->update($interview->id, [
            'status' => InterviewStatus::Completed,
            'score' => $score,
            'feedback' => $feedback,
            'completed_at' => now(),
        ]);

        session()->forget([
            'interview_id',
            'interview_question_index',
            'interview_current_question',
            'interview_questions',
        ]);

        event(new InterviewCompleted($interview->user, $interview, $score));

        return ['completed' => true, 'score' => $score, 'feedback' => $feedback];
    }
}

// Domain/Course/Actions/SubmitExamAction.php
class SubmitExamAction
{
    public function __construct(
        private readonly CertificateService $certificateService,
    ) {}

    public function execute(User $user, Course $exam, array $answers): array
    {
        $examData = $exam->exam_json;
        $correct = 0;
        $total = count($examData['questions']);

        foreach ($examData['questions'] as $index => $question) {
            if (isset($answers[$index]) && $answers[$index] == $question['correct']) {
                $correct++;
            }
        }

        $score = $total > 0 ? round(($correct / $total) * 100) : 0;
        $passed = $score >= $exam->pass_percent;

        $certificate = null;
        if ($passed) {
            $certificate = $this->certificateService->issue(
                user: $user,
                course: $exam->course,
                certificateName: $exam->course->title
            );

            event(new CertificateEarned($user, $exam->course, $certificate));
        }

        return [
            'score' => $score,
            'passed' => $passed,
            'correct' => $correct,
            'total' => $total,
            'certificate' => $certificate,
        ];
    }
}
```

### 4.2 Form Requests

```php
// App/Http/Requests/Auth/LoginRequest.php
class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => t('auth.email_required'),
            'email.email' => t('auth.email_invalid'),
            'password.required' => t('auth.password_required'),
        ];
    }
}

// App/Http/Requests/Course/CompleteLessonRequest.php
class CompleteLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'lesson_id' => ['required', 'exists:lessons,id'],
        ];
    }
}

// App/Http/Requests/Vacancy/FilterVacanciesRequest.php
class FilterVacanciesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', Rule::in(VacancyType::values())],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0'],
            'skill' => ['nullable', 'string', 'max:100'],
        ];
    }
}

// App/Http/Requests/AI/StartInterviewRequest.php
class StartInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(InterviewType::values())],
            'difficulty' => ['required', Rule::in(InterviewDifficulty::values())],
        ];
    }
}

// App/Http/Requests/Community/CreatePostRequest.php
class CreatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
        ];
    }
}
```

### 4.3 Policies

```php
// Domain/Auth/Policies/UserPolicy.php
class UserPolicy
{
    public function update(User $auth, User $user): bool
    {
        return $auth->id === $user->id;
    }

    public function delete(User $auth, User $user): bool
    {
        return $auth->id === $user->id || $auth->role === UserRole::Admin;
    }
}

// Domain/Course/Policies/CoursePolicy.php
class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Course $course): bool
    {
        return true;
    }

    public function manage(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }
}

// Domain/Community/Policies/CommunityPostPolicy.php
class CommunityPostPolicy
{
    public function update(User $auth, CommunityPost $post): bool
    {
        return $auth->id === $post->user_id;
    }

    public function delete(User $auth, CommunityPost $post): bool
    {
        return $auth->id === $post->user_id || $auth->role === UserRole::Admin;
    }
}

// Domain/Vacancy/Policies/VacancyChatPolicy.php
class VacancyChatPolicy
{
    public function view(User $auth, UserApplication $application): bool
    {
        return $auth->id === $application->user_id
            || $auth->role === UserRole::Admin
            || $auth->role === UserRole::Recruiter;
    }
}

// Domain/Profile/Policies/ProfilePolicy.php
class ProfilePolicy
{
    public function update(User $auth, User $user): bool
    {
        return $auth->id === $user->id;
    }
}
```

### 4.4 API Resources

```php
// App/Http/Resources/CourseResource.php
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
            'lesson_count' => $this->lessons->count(),
            'created_at' => $this->created_at,
        ];
    }
}

// App/Http/Resources/VacancyResource.php
class VacancyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'company' => $this->company,
            'location' => $this->location,
            'type' => $this->type,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'salary_currency' => $this->salary_currency,
            'description' => $this->description,
            'skills' => $this->vacancySkills->pluck('skill_name'),
            'requirements' => $this->requirements->pluck('requirement_text'),
            'verified' => $this->verified,
        ];
    }
}

// App/Http/Resources/UserResource.php
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'avatar' => $this->avatar,
            'location' => $this->location,
            'bio' => $this->bio,
            'skills' => UserSkillResource::collection($this->skills),
            'certificate_count' => $this->certificates->count(),
        ];
    }
}
```

### 4.5 Service Interfaces

```php
// App/Services/Interfaces/AiServiceInterface.php
interface AiServiceInterface
{
    public function callApi(array $contents, array $configOverrides = []): array;
    public function buildContents(int $userId, string $message, ?string $context): array;
    public function trimUserChatMessages(int $userId, int $keepLast = 50): void;
    public function generateQuestion(Interview $interview, array $previousQuestions = []): array;
    public function evaluateAnswer(Interview $interview, array $question, string $answer): array;
    public function generateFeedback(Interview $interview, array $questions, int $score): string;
}

// App/Services/Interfaces/CodeExecutionServiceInterface.php
interface CodeExecutionServiceInterface
{
    public function execute(string $language, string $code, string $stdin = ''): CodeExecutionResult;
    public function runPractice(string $language, string $code, array $tests): array;
    public function resolveLanguageId(string $language): int;
}

// App/Services/Interfaces/AuthServiceInterface.php
interface AuthServiceInterface
{
    public function register(RegisterRequest $request): User;
    public function login(LoginRequest $request): User;
    public function loginWithGoogle(string $credential): User;
    public function logout(): void;
}
```

### 4.6 Repository Interfaces

```php
// App/Repositories/Interfaces/CourseRepositoryInterface.php
interface CourseRepositoryInterface
{
    public function find(int $id): ?Course;
    public function findWithLessons(int $id): ?Course;
    public function paginated(array $filters, int $perPage = 12): LengthAwarePaginator;
    public function countCompletedLessons(int $courseId, int $userId): int;
    public function countTotalLessons(int $courseId): int;
    public function getRecommended(int $userId, int $limit = 5): Collection;
    public function getLatest(int $limit = 6): Collection;
}

// App/Repositories/Interfaces/CertificateRepositoryInterface.php
interface CertificateRepositoryInterface
{
    public function create(array $data): Certificate;
    public function findByHash(string $hash): ?Certificate;
    public function getUserCertificates(int $userId): Collection;
    public function getUserCertificateCount(int $userId): int;
}
```

---

## 5. Event-Driven Architecture

### 5.1 Event-Listener Mapping

```php
// Providers/EventServiceProvider.php
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Auth Events
        UserRegistered::class => [
            SendWelcomeNotification::class,
            LogUserActivity::class,
        ],

        // Course Events
        LessonCompleted::class => [
            RecalculateProgress::class,
        ],

        CourseCompleted::class => [
            NotifyCourseCompletion::class,
        ],

        CertificateEarned::class => [
            IssueCertificate::class,
        ],

        // Vacancy Events
        ApplicationSubmitted::class => [
            CreateVacancyChat::class,
            NotifyRecruiter::class,
        ],

        // AI Events
        InterviewCompleted::class => [
            NotifyInterviewResult::class,
        ],

        // Community Events
        PostCreated::class => [
            // Could send notifications to followers
        ],

        PostLiked::class => [
            // Could notify post author
        ],

        // Roadmap Events
        RoadmapNodeCompleted::class => [
            // Could update progress
        ],

        RoadmapCompleted::class => [
            IssueRoadmapCertificate::class,
        ],
    ];
}
```

### 5.2 Event Classes

```php
// App/Events/Course/CertificateEarned.php
class CertificateEarned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Course $course,
        public readonly Certificate $certificate,
    ) {}
}

// App/Events/Vacancy/ApplicationSubmitted.php
class ApplicationSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Vacancy $vacancy,
        public readonly UserApplication $application,
    ) {}
}

// App/Events/AI/InterviewCompleted.php
class InterviewCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Interview $interview,
        public readonly int $score,
    ) {}
}
```

### 5.3 Listeners

```php
// App/Listeners/Course/IssueCertificate.php
class IssueCertificate
{
    public function handle(CertificateEarned $event): void
    {
        // Certificate already created in SubmitExamAction
        // This listener handles side effects
        Log::info("Certificate issued", [
            'user' => $event->user->id,
            'course' => $event->course->id,
            'hash' => $event->certificate->cert_hash,
        ]);
    }
}

// App/Listeners/Vacancy/CreateVacancyChat.php
class CreateVacancyChat
{
    public function handle(ApplicationSubmitted $event): void
    {
        // Chat channel already created in ApplyToVacancyAction
        // This listener handles side effects like notifications
    }
}

// App/Listeners/Course/RecalculateProgress.php
class RecalculateProgress
{
    public function handle(LessonCompleted $event): void
    {
        // Progress recalculation already handled in CompleteLessonAction
        // This listener handles analytics, notifications, etc.
    }
}
```

---

## 6. Queue & Jobs

### 6.1 Async Operations

```php
// App/Jobs/AI/GenerateAiResponseJob.php
class GenerateAiResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 30;
    public int $tries = 3;

    public function __construct(
        public readonly int $userId,
        public readonly string $message,
        public readonly ?string $context,
    ) {}

    public function handle(AiServiceInterface $aiService): void
    {
        // Save user message, call AI, save response
        // This replaces synchronous controller logic
    }

    public function failed(Throwable $exception): void
    {
        // Notify user of failure
    }
}

// App/Jobs/Code/ExecuteCodeJob.php
class ExecuteCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 30;

    public function __construct(
        public readonly int $userId,
        public readonly string $language,
        public readonly string $code,
        public readonly string $stdin,
    ) {}

    public function handle(CodeExecutionServiceInterface $codeService): CodeExecutionResult
    {
        return $codeService->execute($this->language, $this->code, $this->stdin);
    }
}

// App/Jobs/Notification/SendNotificationJob.php
class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $message,
    ) {}

    public function handle(): void
    {
        Notification::create([
            'user_id' => $this->userId,
            'message' => $this->message,
            'notification_time' => now(),
        ]);
    }
}
```

---

## 7. Notifications

```php
// App/Notifications/CourseCompletedNotification.php
class CourseCompletedNotification extends Notification
{
    public function __construct(
        public readonly Course $course,
        public readonly Certificate $certificate,
    ) {}

    public function via(User $notifiable): array
    {
        return ['database']; // Could add 'mail', 'broadcast'
    }

    public function toArray(User $notifiable): array
    {
        return [
            'message' => t('notifications.course_completed', ['course' => $this->course->title]),
            'course_id' => $this->course->id,
            'certificate_hash' => $this->certificate->cert_hash,
        ];
    }
}

// App/Notifications/ApplicationReceivedNotification.php
class ApplicationReceivedNotification extends Notification
{
    public function __construct(
        public readonly User $applicant,
        public readonly Vacancy $vacancy,
    ) {}

    public function via(User $notifiable): array
    {
        return ['database'];
    }

    public function toArray(User $notifiable): array
    {
        return [
            'message' => t('notifications.application_received', [
                'user' => $this->applicant->name,
                'vacancy' => $this->vacancy->title,
            ]),
        ];
    }
}

// App/Notifications/InterviewCompletedNotification.php
class InterviewCompletedNotification extends Notification
{
    public function __construct(
        public readonly Interview $interview,
        public readonly int $score,
    ) {}

    public function via(User $notifiable): array
    {
        return ['database'];
    }

    public function toArray(User $notifiable): array
    {
        return [
            'message' => t('notifications.interview_completed', ['score' => $this->score]),
            'interview_id' => $this->interview->id,
        ];
    }
}
```

---

## 8. Caching Strategy

### 8.1 Repository Caching

```php
// App/Repositories/Cache/CachedCourseRepository.php
class CachedCourseRepository implements CourseRepositoryInterface
{
    public function __construct(
        private readonly EloquentCourseRepository $repository,
        private readonly CacheManager $cache,
    ) {}

    public function find(int $id): ?Course
    {
        return $this->cache->remember(
            key: "course:{$id}",
            ttl: now()->addHours(2),
            callback: fn() => $this->repository->find($id)
        );
    }

    public function paginated(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $cacheKey = 'courses:' . md5(serialize($filters) . $perPage);

        return $this->cache->remember(
            key: $cacheKey,
            ttl: now()->addMinutes(30),
            callback: fn() => $this->repository->paginated($filters, $perPage)
        );
    }

    public function invalidate(int $id): void
    {
        $this->cache->forget("course:{$id}");
        $this->cache->forget('courses:*'); // Pattern-based invalidation
    }
}
```

### 8.2 Cache Configuration

```php
// config/cache.php additions
return [
    'stores' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'prefix' => 'codemaster:',
        ],
    ],

    'prefix' => 'codemaster_',
];
```

---

## 9. Service Provider Bindings

```php
// Providers/RepositoryServiceProvider.php
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repositories
        $this->app->bind(
            CourseRepositoryInterface::class,
            fn() => new CachedCourseRepository(
                new EloquentCourseRepository(),
                new CacheManager()
            )
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            VacancyRepositoryInterface::class,
            EloquentVacancyRepository::class
        );

        $this->app->bind(
            CertificateRepositoryInterface::class,
            EloquentCertificateRepository::class
        );

        // Services
        $this->app->bind(
            AiServiceInterface::class,
            GeminiAiService::class
        );

        $this->app->bind(
            CodeExecutionServiceInterface::class,
            Judge0CodeExecutionService::class
        );

        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );
    }
}

// Providers/AuthServiceProvider.php
class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerPolicies();
    }
}

// Providers/EventServiceProvider.php
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // ... event-listener mappings from Section 5
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
```

---

## 10. Testing Architecture

### 10.1 Test Structure

```
tests/
├── Unit/
│   ├── Domain/
│   │   ├── Shared/
│   │   │   ├── ValueObjects/
│   │   │   │   ├── EmailTest.php
│   │   │   │   ├── MoneyTest.php
│   │   │   │   ├── ProgressPercentTest.php
│   │   │   │   └── ScoreTest.php
│   │   │   └── Enums/
│   │   │       └── UserRoleTest.php
│   │   ├── Course/
│   │   │   └── Actions/
│   │   │       ├── CompleteLessonActionTest.php
│   │   │       └── SubmitExamActionTest.php
│   │   └── Vacancy/
│   │       └── Actions/
│   │           └── ApplyToVacancyActionTest.php
│   └── Services/
│       ├── GeminiAiServiceTest.php
│       └── Judge0CodeExecutionServiceTest.php
├── Feature/
│   ├── Auth/
│   │   ├── RegistrationTest.php
│   │   ├── LoginTest.php
│   │   └── PasswordResetTest.php
│   ├── Course/
│   │   ├── CourseCatalogTest.php
│   │   ├── CourseDetailTest.php
│   │   ├── LessonCompletionTest.php
│   │   └── ExamSubmissionTest.php
│   ├── Vacancy/
│   │   ├── VacancyCatalogTest.php
│   │   ├── VacancyApplicationTest.php
│   │   └── VacancyChatTest.php
│   ├── AI/
│   │   ├── AiTutorChatTest.php
│   │   └── InterviewFlowTest.php
│   ├── Community/
│   │   ├── PostCrudTest.php
│   │   ├── CommentTest.php
│   │   └── LikeToggleTest.php
│   ├── Profile/
│   │   ├── ProfileUpdateTest.php
│   │   ├── SkillManagementTest.php
│   │   └── AvatarUploadTest.php
│   ├── Roadmap/
│   │   ├── RoadmapViewTest.php
│   │   └── NodeCompletionTest.php
│   └── Admin/
│       ├── AdminDashboardTest.php
│       ├── AdminUserManagementTest.php
│       └── AdminCourseManagementTest.php
└── TestCase.php
```

### 10.2 Example Tests

```php
// tests/Unit/Domain/Shared/ValueObjects/ProgressPercentTest.php
class ProgressPercentTest extends TestCase
{
    public function test_progress_calculation(): void
    {
        $progress = new ProgressPercent(completed: 5, total: 10);
        $this->assertEquals(50, $progress->value());
    }

    public function test_progress_at_100_percent(): void
    {
        $progress = new ProgressPercent(completed: 10, total: 10);
        $this->assertTrue($progress->isComplete());
        $this->assertEquals(100, $progress->value());
    }

    public function test_progress_with_zero_total(): void
    {
        $progress = new ProgressPercent(completed: 0, total: 0);
        $this->assertEquals(0, $progress->value());
    }

    public function test_progress_exceeds_100(): void
    {
        $progress = new ProgressPercent(completed: 15, total: 10);
        $this->assertEquals(100, $progress->value()); // Capped at 100
    }
}

// tests/Unit/Domain/Shared/ValueObjects/ScoreTest.php
class ScoreTest extends TestCase
{
    public function test_score_passes_exam(): void
    {
        $score = new Score(85);
        $this->assertTrue($score->isPassed(70));
    }

    public function test_score_fails_exam(): void
    {
        $score = new Score(65);
        $this->assertFalse($score->isPassed(70));
    }

    public function test_score_grade_a(): void
    {
        $score = new Score(95);
        $this->assertEquals('A', $score->grade());
    }
}

// tests/Feature/Course/LessonCompletionTest.php
class LessonCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_complete_lesson(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($user)
            ->postJson('/courses/complete-lesson', [
                'course_id' => $course->id,
                'lesson_id' => $lesson->id,
            ]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('user_lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'completed' => true,
        ]);
    }

    public function test_guest_cannot_complete_lesson(): void
    {
        $response = $this->postJson('/courses/complete-lesson', [
            'course_id' => 1,
            'lesson_id' => 1,
        ]);

        $response->assertUnauthorized();
    }

    public function test_100_percent_progress_marks_course_complete(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        // Create 3 lessons
        $lessons = Lesson::factory()->count(3)->create(['course_id' => $course->id]);

        // Complete all lessons
        foreach ($lessons as $lesson) {
            $this->actingAs($user)
                ->postJson('/courses/complete-lesson', [
                    'course_id' => $course->id,
                    'lesson_id' => $lesson->id,
                ]);
        }

        $this->assertDatabaseHas('user_course_progress', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'completed' => true,
            'progress' => 100,
        ]);
    }
}

// tests/Feature/Vacancy/VacancyApplicationTest.php
class VacancyApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_apply_to_vacancy(): void
    {
        $user = User::factory()->create();
        $vacancy = Vacancy::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/vacancies/{$vacancy->id}/apply");

        $response->assertRedirect();

        $this->assertDatabaseHas('user_applications', [
            'user_id' => $user->id,
            'vacancy_id' => $vacancy->id,
            'status' => 'applied',
        ]);
    }

    public function test_user_cannot_apply_twice(): void
    {
        $user = User::factory()->create();
        $vacancy = Vacancy::factory()->create();

        // First application
        $this->actingAs($user)
            ->postJson("/vacancies/{$vacancy->id}/apply");

        // Second application
        $response = $this->actingAs($user)
            ->postJson("/vacancies/{$vacancy->id}/apply");

        $response->assertStatus(422);
    }
}

// tests/Feature/Community/LikeToggleTest.php
class LikeToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle_like_on_off(): void
    {
        $user = User::factory()->create();
        $post = CommunityPost::factory()->create(['likes_count' => 0]);

        // Like
        $response = $this->actingAs($user)
            ->postJson("/community/{$post->id}/like");

        $response->assertOk()->assertJson([
            'liked' => true,
            'likes' => 1,
        ]);

        // Unlike
        $response = $this->actingAs($user)
            ->postJson("/community/{$post->id}/like");

        $response->assertOk()->assertJson([
            'liked' => false,
            'likes' => 0,
        ]);
    }
}
```

---

## 11. Controller Refactored Example

```php
// App/Http/Controllers/Web/Course/CourseController.php
class CourseController extends Controller
{
    public function __construct(
        private readonly CourseRepositoryInterface $courseRepo,
    ) {}

    public function index(FilterCoursesRequest $request): View
    {
        $courses = $this->courseRepo->paginated(
            filters: $request->validated(),
            perPage: 12
        );

        return view('courses.index', compact('courses'));
    }

    public function show(int $id): View
    {
        $course = $this->courseRepo->findWithLessons($id);
        abort_if(!$course, 404);

        $user = auth()->user();
        $progress = $this->courseRepo->getUserProgress($user->id, $id);
        $certificate = $this->courseRepo->getUserCertificate($user->id, $id);

        return view('courses.show', compact('course', 'progress', 'certificate'));
    }

    public function completeLesson(CompleteLessonRequest $request): JsonResponse
    {
        $action = app(CompleteLessonAction::class);
        $result = $action->execute(
            user: auth()->user(),
            courseId: $request->course_id,
            lessonId: $request->lesson_id
        );

        return response()->json($result);
    }
}
```

---

## 12. Middleware Stack

```php
// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SetLocale::class);
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'not.blocked' => EnsureUserIsNotBlocked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (AccountLockedException $e) {
            return back()->withErrors(['email' => $e->getMessage()]);
        });
        $exceptions->renderable(function (AccountBlockedException $e) {
            return back()->withErrors(['email' => $e->getMessage()]);
        });
        $exceptions->renderable(function (DuplicateApplicationException $e) {
            return back()->withErrors(['application' => $e->getMessage()]);
        });
    })->create();

// App/Http/Middleware/EnsureUserIsNotBlocked.php
class EnsureUserIsNotBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_blocked) {
            auth()->logout();
            return redirect('/login')->withErrors([
                'email' => t('auth.account_blocked'),
            ]);
        }

        return $next($request);
    }
}
```

---

## 13. Migration Design Principles

### 13.1 Naming Convention
- Table names: plural, snake_case (e.g., `user_skills`, `community_posts`)
- Foreign keys: `{table}_id` (e.g., `user_id`, `course_id`)
- Pivot tables: alphabetical order of both tables (e.g., `course_user`)

### 13.2 Constraint Strategy
- Foreign keys with `ON DELETE CASCADE` for owned relationships
- Unique constraints for business rules (e.g., one like per user per post)
- Composite unique constraints (e.g., `user_id + course_id` for progress)

### 13.3 Index Strategy
- Index on all foreign keys
- Composite index for frequent query patterns (e.g., `user_id + status`)
- Unique index for business rules (e.g., `email`)

---

## 14. Configuration Files

### 14.1 New Config Files

```php
// config/gemini.php
return [
    'api_keys' => explode(',', env('GEMINI_API_KEY', '')),
    'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
    'default_config' => [
        'temperature' => 0.7,
        'max_output_tokens' => 2048,
    ],
    'interview_config' => [
        'temperature' => 0.3,
        'max_output_tokens' => 2048,
    ],
    'chat_config' => [
        'temperature' => 0.8,
        'max_output_tokens' => 1024,
    ],
    'context_window' => 20,
    'max_history' => 50,
];

// config/judge0.php
return [
    'api_url' => env('JUDGE0_API_URL', 'https://judge0-ce.p.rapidapi.com'),
    'api_key' => env('JUDGE0_API_KEY', ''),
    'timeout' => 30,
    'languages' => [
        'javascript' => 63,
        'python' => 71,
        'java' => 62,
        'c++' => 54,
        'c' => 50,
        'php' => 68,
        'ruby' => 73,
        'go' => 60,
        'rust' => 73,
        'typescript' => 74,
        'sql' => 82,
        'html/css' => 61,
    ],
];
```

---

## 15. Summary: What This Architecture Achieves

| Problem | Solution |
|---------|----------|
| Fat controllers | Actions + Services extract business logic |
| Inline validation | Form Requests with dedicated rules |
| Inline authorization | Policies with granular permissions |
| No type safety | Enums + Value Objects + DTOs |
| Tight coupling | Service interfaces + Repository pattern |
| Synchronous slow ops | Jobs + Queues for AI, code execution |
| No event system | Events + Listeners for decoupled side effects |
| No caching | Cached repositories for read-heavy data |
| No testing | Feature + Unit tests at every layer |
| No API resources | Resources for consistent JSON responses |
| Mixed concerns | DDD bounded contexts (Auth, Course, Vacancy, AI, etc.) |
| Custom i18n | Service-based translation (can migrate to Laravel lang) |
| No middleware stack | Additional middleware for block checks, account lock |
| No notifications | Laravel Notifications for user alerts |
