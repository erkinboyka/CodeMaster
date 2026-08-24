<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    const XP_LESSON_COMPLETE = 10;
    const XP_QUIZ_PASS = 25;
    const XP_PRACTICE_PASS = 30;
    const XP_COURSE_EXAM_PASS = 50;
    const XP_COURSE_COMPLETE = 100;
    const XP_CONTEST_PASS = 40;
    const XP_INTERVIEW_COMPLETE = 35;
    const XP_PROBLEM_SOLVED = 10;
    const XP_DAILY_CHALLENGE = 30;

    const AI_TOKEN_CHAT_COST = 1;
    const AI_TOKEN_DAILY_BONUS = 5;
    const AI_TOKEN_DAILY_CHALLENGE = 3;

    public function awardXp(User $user, int $amount, string $reason): bool
    {
        if ($amount <= 0) return false;

        $oldLevel = $user->level;

        $user->addXp($amount);
        $user->refresh();

        $newLevel = $this->calculateLevel($user->total_xp);

        if ($newLevel > $oldLevel) {
            $user->setLevel($newLevel);
        }

        UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'xp_earned',
            'activity_text' => "+{$amount} XP: {$reason}",
            'activity_time' => now(),
        ]);

        return true;
    }

    public function awardLessonXp(User $user, string $lessonTitle): int
    {
        $xp = self::XP_LESSON_COMPLETE;
        $this->awardXp($user, $xp, "Урок: {$lessonTitle}");
        return $xp;
    }

    public function awardQuizXp(User $user, int $score): int
    {
        $xp = (int) round(self::XP_QUIZ_PASS * ($score / 100));
        $xp = max($xp, 5);
        $this->awardXp($user, $xp, "Тест: {$score}%");
        return $xp;
    }

    public function awardPracticeXp(User $user, string $taskTitle): int
    {
        $xp = self::XP_PRACTICE_PASS;
        $this->awardXp($user, $xp, "Практика: {$taskTitle}");
        return $xp;
    }

    public function awardCourseExamXp(User $user, string $courseTitle, int $score): int
    {
        $xp = (int) round(self::XP_COURSE_EXAM_PASS * ($score / 100));
        $xp = max($xp, 10);
        $this->awardXp($user, $xp, "Экзамен: {$courseTitle} ({$score}%)");
        return $xp;
    }

    public function awardCourseCompleteXp(User $user, string $courseTitle): int
    {
        $xp = self::XP_COURSE_COMPLETE;
        $this->awardXp($user, $xp, "Курс пройден: {$courseTitle}");
        return $xp;
    }

    public function awardContestXp(User $user, string $contestTitle): int
    {
        $xp = self::XP_CONTEST_PASS;
        $this->awardXp($user, $xp, "Контест: {$contestTitle}");
        return $xp;
    }

    public function awardInterviewXp(User $user, string $interviewTitle): int
    {
        $xp = self::XP_INTERVIEW_COMPLETE;
        $this->awardXp($user, $xp, "Собеседование: {$interviewTitle}");
        return $xp;
    }

    public function calculateLevel(int $totalXp): int
    {
        if ($totalXp <= 0) return 1;

        $level = 1;
        $required = 100;

        while ($totalXp >= $required) {
            $totalXp -= $required;
            $level++;
            $required = $level * 100;
        }

        return $level;
    }

    public function getXpForCurrentLevel(int $totalXp): int
    {
        $level = 1;
        $required = 100;
        $remaining = $totalXp;

        while ($remaining >= $required) {
            $remaining -= $required;
            $level++;
            $required = $level * 100;
        }

        return $remaining;
    }

    public function getXpForNextLevel(int $totalXp): int
    {
        $level = 1;
        $required = 100;
        $remaining = $totalXp;

        while ($remaining >= $required) {
            $remaining -= $required;
            $level++;
            $required = $level * 100;
        }

        return $required;
    }

    public function getLevelProgress(int $totalXp): float
    {
        $current = $this->getXpForCurrentLevel($totalXp);
        $required = $this->getXpForNextLevel($totalXp);

        return $required > 0 ? round(($current / $required) * 100, 1) : 0;
    }

    public function deductAiTokens(User $user, int $amount = 1): bool
    {
        $affected = User::where('id', $user->id)
            ->where('ai_tokens', '>=', $amount)
            ->decrement('ai_tokens', $amount);

        if ($affected > 0) {
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'tokens_spent',
                'activity_text' => "-{$amount} токенов: AI чат",
                'activity_time' => now(),
            ]);
        }

        return $affected > 0;
    }

    public function addAiTokens(User $user, int $amount, string $reason = ''): void
    {
        $user->addAiTokens($amount);

        if ($reason) {
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'tokens_earned',
                'activity_text' => "+{$amount} токенов: {$reason}",
                'activity_time' => now(),
            ]);
        }
    }

    public function dailyTokenBonus(User $user): int
    {
        return DB::transaction(function () use ($user) {
            $today = now()->toDateString();
            $alreadyReceived = UserActivity::where('user_id', $user->id)
                ->where('activity_type', 'daily_bonus')
                ->whereDate('activity_time', $today)
                ->lockForUpdate()
                ->exists();

            if ($alreadyReceived) return 0;

            $amount = self::AI_TOKEN_DAILY_BONUS;
            $user->addAiTokens($amount);

            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'daily_bonus',
                'activity_text' => "+{$amount} токенов: Ежедневный бонус",
                'activity_time' => now(),
            ]);

            return $amount;
        });
    }

    public function getLevelTitle(int $level): string
    {
        return match(true) {
            $level >= 30 => 'Expert',
            $level >= 15 => 'Продвинутый',
            $level >= 10 => 'Experienced',
            $level >= 5 => 'Student',
            default => 'Начинающий',
        };
    }

    public function getLevelColor(int $level): string
    {
        return match(true) {
            $level >= 30 => '#eab308',
            $level >= 15 => '#f97316',
            $level >= 10 => '#8b5cf6',
            $level >= 5 => '#3b82f6',
            default => '#22c55e',
        };
    }

    public function getLevelBadge(int $level): string
    {
        return match(true) {
            $level >= 30 => '<i class="fas fa-crown"></i>',
            $level >= 15 => '<i class="fas fa-fire"></i>',
            $level >= 10 => '<i class="fas fa-star"></i>',
            $level >= 5 => '<i class="fas fa-graduation-cap"></i>',
            default => '<i class="fas fa-seedling"></i>',
        };
    }
}
