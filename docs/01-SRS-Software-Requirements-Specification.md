# Software Requirements Specification (SRS)
## CodeMaster — IT Education & Employment Platform

**Version:** 1.0
**Date:** 2026-08-01
**Status:** Reverse-Engineered from Production Codebase

---

## 1. Introduction

### 1.1 Purpose

CodeMaster is a comprehensive IT education and employment platform designed to bridge the gap between learning and employment for developers, primarily targeting the Tajikistan market. The platform unifies education (courses, roadmaps), practice (code execution via Judge0), career development (vacancies with chat), and AI-powered tools (tutor and interview simulator) into a single ecosystem.

### 1.2 Problem Statement

The IT education landscape in Central Asia lacks accessible, localized platforms that provide:
- Structured learning paths with verified certificates
- Practical code execution environments
- Direct employment pathways
- AI-powered interview preparation
- Community engagement

CodeMaster solves these problems by providing an end-to-end platform: **Learn → Practice → Certify → Get Hired**.

### 1.3 Scope

The system encompasses:
- Multi-role user management (Seeker, Recruiter, Admin)
- Course management with lessons (video, article, quiz)
- Code execution engine integration (Judge0 CE)
- AI-powered tutoring and interview simulation (Google Gemini)
- Vacancy management with applicant chat
- Community forum
- Certificate generation with verification hashes
- Interactive roadmap visualizations
- Platform ratings and leaderboards
- Multi-language support (Russian, English, Tajik)

### 1.4 Definitions

| Term | Definition |
|------|-----------|
| **Course** | A structured learning program containing ordered lessons |
| **Lesson** | A single unit of learning content (video, article, or quiz) |
| **Roadmap** | A visual learning path represented as interconnected nodes on a 2D plane |
| **Node** | An element in a roadmap containing lessons and quiz questions |
| **Certificate** | A document with unique hash verifying course/roadmap completion |
| **Vacancy** | A job posting with requirements, skills, and salary information |
| **Application** | A user's response to a vacancy, triggering a chat channel |
| **Interview** | An AI-simulated interview session with scoring and feedback |
| **AI Tutor** | A conversational AI assistant for programming questions |
| **Practice Task** | A coding challenge executed against test cases via Judge0 |

### 1.5 Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend Framework | Laravel 12 (PHP 8.2+) |
| Frontend | Blade Templates + Tailwind CSS 4 + Alpine.js |
| Database | MySQL/MariaDB (production), SQLite (testing) |
| AI Integration | Google Gemini API (gemini-2.5-flash) |
| Code Execution | Judge0 CE API (via RapidAPI) |
| Authentication | Session-based + Google OAuth2 |
| Build Tool | Vite 7 |
| Package Manager | Composer (PHP), npm (JS) |

---

## 2. Overall Description

### 2.1 Product Perspective

CodeMaster is a server-rendered web application following the MVC pattern. It operates as a monolithic Laravel application with server-side rendering via Blade templates. There is no separate API layer — all routes serve HTML responses or JSON for AJAX interactions.

### 2.2 User Classes and Characteristics

| Role | Description | Access Level |
|------|-------------|-------------|
| **Guest** | Unauthenticated visitor | Public pages only (landing, login, register, static pages) |
| **Seeker** | Job seeker / student | Full platform access except admin panel and vacancy creation |
| **Recruiter** | Employer / HR | Seeker permissions + vacancy creation and management |
| **Admin** | Platform administrator | Full access including user management, content CRUD, admin panel |

### 2.3 Operating Environment

- **Server:** PHP 8.2+ with extensions: openssl, pdo, mbstring, tokenizer, xml, ctype, json, bcmath, fileinfo
- **Database:** MySQL 5.7+ or MariaDB 10.3+ or SQLite 3.35+
- **Browser:** Modern browsers (Chrome, Firefox, Safari, Edge)
- **Network:** HTTPS recommended, HTTP acceptable for development

### 2.4 Design and Implementation Constraints

1. **Monolithic Architecture:** Single deployment unit, no microservices
2. **Server-Side Rendering:** No SPA — all views rendered via Blade
3. **No REST API:** No separate API endpoints (all dynamic content via AJAX)
4. **Custom i18n:** Uses custom translation system instead of Laravel's built-in `__()` or `trans()`
5. **Session-Driven Auth:** Uses database-backed sessions (not token-based)
6. **External Dependencies:** Requires active internet for Gemini API, Judge0 API, Google OAuth
7. **Currency:** TJS (Tajikistani Somoni) as the primary salary currency
8. **Locale Priority:** query param `lang` > session > cookie > config default (`ru`)

### 2.5 Assumptions

- Users have basic web browsing capabilities
- Internet connectivity is available for AI features
- Admin users are pre-seeded or created by existing admins
- Email service is configured for password reset flows
- reCAPTCHA keys are optional (bypass if not configured)

---

## 3. Functional Requirements

### 3.1 Authentication & Authorization (FR-AUTH)

#### FR-AUTH-001: User Registration
- **Input:** name, email, password, optional skills, optional role
- **Process:**
  1. Validate input (name required, email unique, password min 8 chars with confirmation)
  2. Verify reCAPTCHA token (bypass if not configured)
  3. Hash password with bcrypt (12 rounds)
  4. Create User record with role `seeker` (default)
  5. Parse comma-separated skills and create UserSkill records
  6. Auto-login user
- **Output:** Redirect to dashboard
- **Constraint:** Email must be globally unique

#### FR-AUTH-002: User Login (Email/Password)
- **Input:** email, password
- **Process:**
  1. Check if account is locked (`locked_until > now()`) — reject with time remaining
  2. Validate credentials against database
  3. Check if account is blocked (`is_blocked = true`) — reject
  4. On success: reset `failed_login_attempts`, set `last_login`, regenerate session
  5. On failure: increment `failed_login_attempts`, if >= 5 lock account for 900 seconds (15 minutes)
- **Output:** Redirect to dashboard on success, error message on failure
- **Brute-force Protection:** 5 failed attempts → 15-minute lockout

#### FR-AUTH-003: Google OAuth Login
- **Input:** Google ID token (credential)
- **Process:**
  1. Verify token via `https://oauth2.googleapis.com/tokeninfo`
  2. Validate `aud` matches configured client ID
  3. Find existing user by email or create new (name from Google profile, random password)
  4. Check if blocked
  5. Login user
- **Output:** Redirect to dashboard

#### FR-AUTH-004: Logout
- **Process:** Invalidate session, regenerate CSRF token
- **Output:** Redirect to home page

#### FR-AUTH-005: Password Reset
- **Input:** email (request), then token + new password (reset)
- **Process:**
  1. Send reset link via Laravel's Password broker
  2. User clicks link → verify token
  3. Validate new password (min 8, confirmed)
  4. Hash and update password
- **Output:** Redirect to login

#### FR-AUTH-006: Role-Based Access Control
- **Middleware:** `auth` (checks session), `admin` (checks role === 'admin')
- **Authorization Rules:**
  - Seeker: Cannot access `/admin/*`, cannot create vacancies
  - Recruiter: Cannot access `/admin/*`, can create vacancies
  - Admin: Full access to all routes

### 3.2 Education Module (FR-EDU)

#### FR-EDU-001: Course Catalog
- **Route:** GET `/courses`
- **Features:**
  - Search by title (LIKE query)
  - Filter by category: frontend, backend, design, devops, other
  - Filter by level: Начальный, Средний, Продвинутый
  - Pagination: 12 items per page
  - Display: title, instructor, category, level, image, lesson count

#### FR-EDU-002: Course Detail View
- **Route:** GET `/courses/{id}`
- **Features:**
  - Course description, instructor, category, level
  - Ordered list of lessons with types (video/article/quiz)
  - User's progress percentage
  - Available exam (if exists)
  - User's certificate (if earned)
  - Materials download links

#### FR-EDU-003: Lesson Completion
- **Route:** POST `/courses/complete-lesson`
- **Input:** course_id, lesson_id
- **Process:**
  1. Validate course and lesson exist
  2. Create/update UserLessonProgress (completed=true, completed_at=now())
  3. Recalculate progress: (completed lessons / total lessons) × 100
  4. Update UserCourseProgress (mark completed at 100%)
- **Output:** JSON {success, percent, completed}
- **Constraint:** Only authenticated users can complete lessons

#### FR-EDU-004: Lesson Types
- **Video:** Embedded video player + downloadable materials
- **Article:** Rich text content display
- **Quiz:** Multiple choice questions with auto-grading
  - Questions from QuizQuestion model
  - Options from QuizOption model
  - Support for multiple correct answers (correct_options array)
  - Hints available per question

#### FR-EDU-005: Course Exam
- **Route:** GET `/courses/{id}/exam` (view), POST `/courses/{id}/exam/submit` (submit)
- **Features:**
  - Questions stored as JSON in CourseExam.exam_json
  - Configurable time limit (default 60 minutes)
  - Configurable pass percentage (default 70%)
  - Optional shuffle of questions and options
  - Timer-based submission
- **Scoring:** (correct answers / total questions) × 100
- **Certificate:** Auto-generated if score >= pass_percent
  - Unique 40-character hash (random string)
  - Issuer: "CodeMaster"
  - Certificate name derived from course title

#### FR-EDU-006: Certificate Verification
- **Route:** GET `/certificate/{hash}`
- **Features:**
  - Publicly accessible (requires auth but any user can view)
  - Displays: user name, course name, issuer, issue date
  - Downloadable as printable HTML
  - Unique hash ensures authenticity

### 3.3 Roadmap Module (FR-ROAD)

#### FR-ROAD-001: Roadmap List
- **Route:** GET `/roadmaps`
- **Features:**
  - Lists distinct roadmap titles from RoadmapNode table
  - Each roadmap has a title and description

#### FR-ROAD-002: Roadmap Visualization
- **Route:** GET `/roadmap/{title}`
- **Features:**
  - Interactive 2D visualization of nodes
  - Nodes positioned by x/y coordinates
  - Dependencies defined in deps JSON array
  - Each node contains: title, description, lessons, quiz questions
  - Node types: lesson node or exam node (is_exam flag)
  - User progress displayed as percentage

#### FR-ROAD-003: Node Completion
- **Route:** POST `/roadmap/complete-node`
- **Input:** node_id
- **Process:**
  1. Validate node exists
  2. Check dependency satisfaction (all deps must be completed)
  3. Create RoadmapUserProgress record
  4. Recalculate roadmap progress percentage
- **Output:** JSON {success, percent, completed}
- **Certificate:** Auto-generated when all nodes completed (RoadmapCertificate)

### 3.4 Practice Module (FR-PRACT)

#### FR-PRACT-001: Code Execution
- **Route:** POST `/practice/submit`
- **Input:** language, code, optional stdin
- **Process:**
  1. Map language name to Judge0 language ID
  2. Submit code to Judge0 CE API with `wait=true`
  3. Receive result (stdout, stderr, time, memory, status)
- **Output:** JSON {stdout, stderr, status, time, memory}
- **Supported Languages:** JavaScript (63), Python (71), Java (62), C++ (54), C (50), PHP (68), Ruby (73), Go (60), Rust (73), TypeScript (74), SQL (82), HTML/CSS (61)

#### FR-PRACT-002: Practice Task Validation
- **Process:**
  1. Run code against multiple test cases
  2. Each test has input and expected output
  3. Compare stdout to expected (trimmed, normalized)
  4. Return per-test pass/fail status
- **Output:** status (accepted/wrong_answer), per-test results

#### FR-PRACT-003: SQL Practice
- **Special Handling:** SQL queries sent as JSON stdin to Judge0
- **Process:** Compare query result to expected output

#### FR-PRACT-004: Fill-in-the-Blank Practice
- **No Code Execution:** Direct string comparison
- **Process:** Compare user answers to expected (case-insensitive, trimmed)

### 3.5 Vacancy Module (FR-VAC)

#### FR-VAC-001: Vacancy Catalog
- **Route:** GET `/vacancies`
- **Features:**
  - Search by title (LIKE)
  - Filter by location (LIKE)
  - Filter by type: remote, office, hybrid
  - Filter by salary range (min/max)
  - Filter by skill (has matching VacancySkill)
  - Pagination: 12 items per page

#### FR-VAC-002: Vacancy Detail
- **Route:** GET `/vacancies/{id}`
- **Features:**
  - Company name and description
  - Requirements list (VacancyRequirement)
  - Responsibilities list (VacancyResponsibility)
  - Plus points (VacancyPluse)
  - Required skills (VacancySkill)
  - Salary range with currency (default TJS)
  - Location and work type
  - "Apply" button (disabled if already applied)

#### FR-VAC-003: Job Application
- **Route:** POST `/vacancies/{id}/apply`
- **Process:**
  1. Check for duplicate application (user_id + vacancy_id unique)
  2. Create UserApplication with status `applied`
  3. Auto-create VacancyChat channel
- **Output:** Redirect to vacancy chat
- **Constraint:** One application per user per vacancy

#### FR-VAC-004: Vacancy Chat
- **Route:** GET `/vacancy-chat/{applicationId}`, POST `/vacancy-chat`
- **Features:**
  - Real-time messaging between applicant and recruiter/admin
  - Message history with timestamps
  - Document upload (PDF, DOC, DOCX, TXT, PNG, JPG, JPEG)
  - Max file size: 10MB
  - Storage: public disk, `chat-documents` directory
- **Authorization:** Only application owner or admin can access

#### FR-VAC-005: Application Status
- **Statuses:** applied, interview, offer, rejected
- **Employment Status:** pending, successful, unsuccessful
- **Lifecycle:** applied → interview → offer/rejected

### 3.6 AI Module (FR-AI)

#### FR-AI-001: AI Tutor
- **Route:** POST `/ai/chat`, GET `/ai/history`, POST `/ai/clear`
- **Features:**
  - Floating widget UI (always accessible)
  - Message history (last 50 messages stored)
  - Context window: last 20 messages sent to Gemini
  - System instruction: "You are an AI tutor for CodeMaster"
  - Max input: 2000 characters per message
- **Process:**
  1. Save user message (role: user)
  2. Load last 20 messages for context
  3. Build Gemini API request with system instruction
  4. Call Gemini (gemini-2.5-flash)
  5. Save assistant response (role: assistant)
  6. Trim history to 50 messages
- **Output:** JSON {reply}

#### FR-AI-002: AI Interview Simulation
- **Route:** POST `/interview` (create), GET `/interview/{id}` (room), POST `/interview/{id}/answer`
- **Interview Types:**
  - **Technical:** Multiple choice questions about programming concepts
  - **Behavioral:** STAR method questions
  - **Coding:** Code writing problems with I/O format
  - **System Design:** Architecture and design problems
- **Difficulty Levels:** easy, medium, hard
- **Session Flow:**
  1. Create Interview record (status: in_progress)
  2. Generate 5 questions via Gemini
  3. User answers each question
  4. AI evaluates each answer (0-100 score + feedback)
  5. After 5 questions: average score + comprehensive feedback
  6. Interview marked as completed
- **Evaluation Types:**
  - Multiple choice: correctness check
  - Code writing: code quality, correctness, efficiency
  - Open-ended: relevance, depth, clarity

#### FR-AI-003: Interview Chat
- **Route:** POST `/interview/ai-chat`
- **Features:**
  - Ask clarifying questions during interview
  - Context-aware (knows current question, interview type/difficulty)
  - Temperature: 0.8 (more creative than evaluation)
- **Output:** JSON {reply}

### 3.7 Community Module (FR-COMM)

#### FR-COMM-001: Post Management
- **Routes:** CRUD on `/community`
- **Features:**
  - Create posts (title + content, max 10000 chars)
  - View posts with comments
  - Edit own posts only (403 for others)
  - Delete own posts only (cascades to comments and likes via DB transaction)
  - View count tracking
  - Pagination: 100 most recent posts

#### FR-COMM-002: Comments
- **Route:** POST `/community/comment`
- **Features:**
  - Add comments to posts (max 5000 chars)
  - Auto-loads user relationship
  - Returns JSON for AJAX

#### FR-COMM-003: Likes
- **Route:** POST `/community/{id}/like`
- **Features:**
  - Toggle behavior: like → unlike → like
  - Unique constraint: one like per user per post
  - Counter updated atomically (increment/decrement)
- **Output:** JSON {success, likes, liked}

### 3.8 Profile Module (FR-PROF)

#### FR-PROF-001: Profile View
- **Routes:** GET `/profile` (own), GET `/profile/{userId}` (public)
- **Features:**
  - Bio, location, avatar
  - Skills with levels and verification status
  - Work experience (CRUD)
  - Education (CRUD)
  - Portfolio items with images (CRUD)
  - Platform reviews

#### FR-PROF-002: Profile Edit
- **Route:** PUT `/profile`
- **Editable Fields:** name, location, bio (max 1000), title
- **Avatar Upload:** Image, max 2MB, stored on public disk
- **Authorization:** Users can only edit their own profile

#### FR-PROF-003: Password Change
- **Route:** PUT `/profile/password`
- **Validation:** current_password must be correct, new password min 8 with confirmation

#### FR-PROF-004: Skills Management
- **Routes:** POST `/profile/skill`, DELETE `/profile/skill/{id}`
- **Features:**
  - Add skills with levels: beginner, intermediate, advanced, expert
  - Categories: technical, soft
  - Endorsement count
  - Verification status (admin can verify)

#### FR-PROF-005: Experience Management
- **Routes:** POST/PUT/DELETE `/profile/experience`
- **Fields:** position, company, start_date, end_date, description

#### FR-PROF-006: Education Management
- **Routes:** POST/PUT/DELETE `/profile/education`
- **Fields:** degree, institution, start_date, end_date, description

#### FR-PROF-007: Portfolio Management
- **Routes:** POST/PUT/DELETE `/profile/portfolio`
- **Fields:** title, category, image_url, github_url
- **Image Handling:** Upload to public disk, delete old on update

### 3.9 Notification Module (FR-NOTIF)

#### FR-NOTIF-001: Notification Display
- **Location:** Header dropdown
- **Features:**
  - Shows unread notification count
  - Lists recent notifications (up to 10)
  - Mark all as read button

#### FR-NOTIF-002: Mark Read
- **Route:** POST `/notifications/mark-read`
- **Process:** Set is_read=true for all unread notifications of current user
- **Output:** JSON {success: true}

### 3.10 Rating Module (FR-RATE)

#### FR-RATE-001: Leaderboard
- **Route:** GET `/ratings`
- **Features:**
  - Users ranked by certificate count (descending)
  - Optional skill filter
  - Pagination: 20 per page
  - Display: user name, avatar, certificate count

### 3.11 Admin Module (FR-ADMIN)

#### FR-ADMIN-001: Dashboard
- **Route:** GET `/admin/`
- **Statistics:**
  - Total users, courses, vacancies
  - New users today
  - Blocked users count

#### FR-ADMIN-002: User Management
- **Routes:** GET/POST/PUT/DELETE `/admin/users/*`
- **Features:**
  - List users with search (name/email) and role filter
  - Create users (name, email, password, role)
  - Edit users (name, email, role, password)
  - Toggle block status
  - Delete users
  - Update user role

#### FR-ADMIN-003: Course Management
- **Routes:** CRUD on `/admin/courses/*`
- **Features:**
  - List courses with search
  - Create courses (title, instructor, description, category, level)
  - Edit courses
  - Delete courses (cascades to lessons, skills, exams)

#### FR-ADMIN-004: Lesson Management
- **Routes:** CRUD on `/admin/lessons/*`
- **Features:**
  - List lessons with search
  - Create lessons (title, type, content, video_url, order_num)
  - Edit lessons
  - Delete lessons (cascades to quiz questions, practice tasks)

#### FR-ADMIN-005: Vacancy Management
- **Routes:** CRUD on `/admin/vacancies/*`
- **Features:**
  - List vacancies with search
  - Create vacancies (title, company, location, type, salary range, description)
  - Edit vacancies
  - Delete vacancies

#### FR-ADMIN-006: Notification Management
- **Routes:** GET/DELETE `/admin/notifications/*`
- **Features:**
  - List notifications with search
  - Delete notifications

### 3.12 Internationalization (FR-I18N)

#### FR-I18N-001: Multi-Language Support
- **Languages:** Russian (ru, default), English (en), Tajik (tg)
- **Locale Priority:** query param `lang` > session > cookie > config default
- **Implementation:**
  - Custom I18nService with file-based translations
  - `t()` helper function for string translation
  - `@t` Blade directive
  - Language switcher in header
- **Translation Files:** `lang/ru.php`, `lang/en.php`, `lang/tg.php`

---

## 4. Non-Functional Requirements

### 4.1 Security

| Requirement | Implementation |
|-------------|---------------|
| Password Hashing | bcrypt, 12 rounds |
| CSRF Protection | Laravel CSRF tokens on all forms |
| XSS Prevention | Blade auto-escaping (`{{ }}`) |
| SQL Injection | Eloquent ORM parameterized queries |
| Brute-force Protection | 5 failed attempts → 15-minute lockout |
| Session Security | Database-backed sessions, 24-hour TTL |
| File Upload Validation | MIME type checking, size limits |
| reCAPTCHA | Google reCAPTCHA on registration/login |
| Authorization | Manual role checks in controllers |

### 4.2 Performance

| Metric | Target |
|--------|--------|
| Page Load | < 3 seconds on 3G |
| Code Execution | 30-second timeout (Judge0) |
| AI Response | < 10 seconds (Gemini API) |
| Pagination | 12 items (courses/vacancies), 20 items (admin/ratings) |
| Chat History | Last 50 messages retained |

### 4.3 Reliability

- Database transactions for critical operations (post deletion with cascade)
- Retry logic for AI API calls (up to 3 attempts with key rotation)
- Graceful degradation (reCAPTCHA bypass if not configured)
- Custom error pages (404)

### 4.4 Usability

- Dark theme with neon-style UI
- Responsive design via Tailwind CSS
- Floating AI tutor widget
- Animated landing page (particles, 3D effects)
- Preloader for page transitions
- Toast notifications for user feedback

---

## 5. Database Schema Summary

### 5.1 Tables (42 total)

| Domain | Tables |
|--------|--------|
| Users & Auth | users, sessions, notifications, user_activities |
| User Profile | user_skills, user_experience, user_education, user_portfolio, user_ai_wallets, user_cv_customizations, user_skills_assessments |
| Courses | courses, lessons, quiz_questions, quiz_options, lesson_tests, course_exams, course_skills, user_course_progress, user_lesson_progress |
| Practice | lesson_practice_tasks, practice_submissions, contest_submissions |
| Vacancies | vacancies, vacancy_skills, vacancy_requirements, vacancy_pluses, vacancy_responsibilities, user_applications, vacancy_chats, vacancy_documents |
| Roadmaps | roadmap_nodes, roadmap_lessons, roadmap_quiz_questions, roadmap_user_progress, roadmap_certificates |
| Community | community_posts, community_comments, community_post_likes |
| AI & Reviews | chat_messages, platform_reviews, interviews |
| Certificates | certificates |

### 5.2 Key Relationships

```
User ──< UserSkill, UserExperience, UserEducation, UserPortfolio
User ──< Certificate ──> Course
User ──< UserCourseProgress ──> Course
User ──< UserLessonProgress ──> Lesson
User ──< UserApplication ──> Vacancy
User ──< VacancyChat
User ──< ChatMessage
User ──< Interview
User ──< CommunityPost ──< CommunityComment
User ──< CommunityPostLike
User ──< Notification

Course ──< Lesson ──< QuizQuestion ──< QuizOption
Course ──< Lesson ──< LessonPracticeTask ──< PracticeSubmission
Course ──< CourseExam
Course ──< CourseSkill
Course ──< RoadmapNode ──< RoadmapLesson
Course ──< RoadmapNode ──< RoadmapQuizQuestion

Vacancy ──< VacancySkill, VacancyRequirement, VacancyPluse, VacancyResponsibility
Vacancy ──< UserApplication ──< VacancyChat
UserApplication ──< VacancyDocument
```

---

## 6. External Interfaces

### 6.1 Google Gemini API
- **Endpoint:** `https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent`
- **Model:** gemini-2.5-flash
- **Authentication:** API key (pool of up to 6 keys with round-robin rotation)
- **Features:** Chat completion, JSON response parsing, retry on 429/503

### 6.2 Judge0 CE API
- **Endpoint:** Configurable via `JUDGE0_API_URL`
- **Authentication:** RapidAPI headers (X-RapidAPI-Key, X-RapidAPI-Host)
- **Features:** Code compilation and execution, test case validation

### 6.3 Google OAuth2
- **Endpoint:** `https://oauth2.googleapis.com/tokeninfo`
- **Purpose:** ID token verification for Google Sign-In

### 6.4 Google reCAPTCHA
- **Endpoint:** `https://www.google.com/recaptcha/api/siteverify`
- **Purpose:** Bot protection on registration and login forms

### 6.5 ui-avatars.com
- **Endpoint:** `https://ui-avatars.com/api/`
- **Purpose:** Dynamic avatar generation from user name when no custom avatar is uploaded

---

## 7. Data Validation Rules

### 7.1 User Registration
| Field | Rules |
|-------|-------|
| name | required, string |
| email | required, email, unique:users |
| password | required, string, min:8, confirmed |
| role | nullable, in:seeker,recruiter |

### 7.2 Course Creation (Admin)
| Field | Rules |
|-------|-------|
| title | required, string, max:255 |
| instructor | required, string, max:255 |
| description | required, string |
| category | required, in:frontend,backend,design,devops,other |
| level | required, in:Начальный,Средний,Продвинутый |

### 7.3 Vacancy Creation
| Field | Rules |
|-------|-------|
| title | required, string, max:255 |
| company | required, string, max:255 |
| location | nullable, string, max:255 |
| type | nullable, in:remote,office,hybrid |
| salary_min | nullable, integer, min:0 |
| salary_max | nullable, integer, min:0, gte:salary_min |
| description | required, string |

### 7.4 Chat Message
| Field | Rules |
|-------|-------|
| message | required, string, max:5000 |

### 7.5 AI Chat Message
| Field | Rules |
|-------|-------|
| message | required, string, max:2000 |
| context | nullable, string |

---

## 8. State Machines

### 8.1 User Application Status
```
applied → interview → offer
                   → rejected
```

### 8.2 Interview Status
```
in_progress → completed
            → abandoned
```

### 8.3 User Account States
```
active ──(5 failed logins)──> locked (15 min)
active ──(admin toggle)──> blocked
blocked ──(admin toggle)──> active
locked ──(15 min timeout)──> active
```

---

## 9. Appendix

### 9.1 Seed Data

The system includes comprehensive seed data:
- 20 courses across all categories
- 5 lessons per course (100 total)
- 10 vacancies from various companies
- 20 roadmap nodes for "Frontend Developer"
- Course exams with 30 questions each
- Test user: `user@example.com` / `password`
- Admin user: `admin@codemaster.com` / `admin123` (AI coins: 999)
- Community posts, notifications, activities, certificates

### 9.2 File Storage Structure
```
public/
  avatars/          -- User avatar uploads
  portfolios/       -- Portfolio item images
  chat-documents/   -- Vacancy chat file uploads
```
