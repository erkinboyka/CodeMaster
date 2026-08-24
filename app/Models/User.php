<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements \Illuminate\Contracts\Auth\MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, MustVerifyEmail, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'name',
        'role',
        'title',
        'location',
        'bio',
        'avatar',
        'google_locale',
        'country_code',
        'country_name',
        'github',
        'linkedin',
        'website',
        'last_login',
        'streak_count',
        'longest_streak',
        'last_active_date',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->role = $user->role ?? 'seeker';
        });
    }

    public function setRole(string $role): void
    {
        $this->update(['role' => $role]);
    }

    public function block(): void
    {
        $this->update(['is_blocked' => true]);
    }

    public function unblock(): void
    {
        $this->update(['is_blocked' => false]);
    }

    public function verify(): void
    {
        $this->update(['is_verified' => true]);
    }

    public function addXp(int $amount): void
    {
        $this->increment('total_xp', $amount);
    }

    public function addAiTokens(int $amount): void
    {
        $this->increment('ai_tokens', $amount);
    }

    public function deductAiTokens(int $amount): void
    {
        $this->decrement('ai_tokens', $amount);
    }

    public function setLevel(int $level): void
    {
        $this->update(['level' => $level]);
    }

    public function recordFailedLogin(): void
    {
        $this->increment('failed_login_attempts');
    }

    public function resetFailedLogins(): void
    {
        $this->update(['failed_login_attempts' => 0, 'locked_until' => null]);
    }

    public function lockAccount(int $seconds): void
    {
        $this->update(['locked_until' => now()->addSeconds($seconds)]);
    }

    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) && !empty($this->two_factor_confirmed_at);
    }

    public function setTwoFactorSecret(string $secret): void
    {
        $this->update(['two_factor_secret' => $secret]);
    }

    public function confirmTwoFactor(): void
    {
        $this->update(['two_factor_confirmed_at' => now()]);
    }

    public function disableTwoFactor(): void
    {
        $this->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }

    public function setRecoveryCodes(string $hashedCodes): void
    {
        $this->update(['two_factor_recovery_codes' => $hashedCodes]);
    }

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'xp' => 'integer',
            'ai_tokens' => 'integer',
            'level' => 'integer',
            'total_xp' => 'integer',
            'is_verified' => 'boolean',
            'is_blocked' => 'boolean',
            'failed_login_attempts' => 'integer',
            'locked_until' => 'datetime',
            'last_login' => 'datetime',
            'streak_count' => 'integer',
            'longest_streak' => 'integer',
            'last_active_date' => 'date',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function skills(): HasMany
    {
        return $this->hasMany(UserSkill::class);
    }

    public function experience(): HasMany
    {
        return $this->hasMany(UserExperience::class);
    }

    public function education(): HasMany
    {
        return $this->hasMany(UserEducation::class);
    }

    public function portfolio(): HasMany
    {
        return $this->hasMany(UserPortfolio::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(UserApplication::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PlatformReview::class);
    }

    public function courseProgress(): HasMany
    {
        return $this->hasMany(UserCourseProgress::class);
    }

    public function practiceSubmissions(): HasMany
    {
        return $this->hasMany(PracticeSubmission::class);
    }

    public function contestSubmissions(): HasMany
    {
        return $this->hasMany(ContestSubmission::class);
    }

    public function favoriteStudyPlans(): BelongsToMany
    {
        return $this->belongsToMany(ProblemList::class, 'problem_list_user_favorite')
            ->withTimestamps();
    }

    public function ratingHistory()
    {
        return $this->hasMany(RatingHistory::class);
    }

    public function getLevelTitleAttribute(): string
    {
        return app(\App\Services\GamificationService::class)->getLevelTitle($this->level);
    }

    public function getLevelBadgeAttribute(): string
    {
        return app(\App\Services\GamificationService::class)->getLevelBadge($this->level);
    }

    public function getLevelColorAttribute(): string
    {
        return app(\App\Services\GamificationService::class)->getLevelColor($this->level);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'U') . '&background=6366f1&color=fff';
    }

    public function getLevelProgressAttribute(): float
    {
        return app(\App\Services\GamificationService::class)->getLevelProgress($this->total_xp);
    }

    public function getXpForNextLevelAttribute(): int
    {
        return app(\App\Services\GamificationService::class)->getXpForNextLevel($this->total_xp);
    }

    public function getXpForCurrentLevelAttribute(): int
    {
        return app(\App\Services\GamificationService::class)->getXpForCurrentLevel($this->total_xp);
    }

    public function recordActivity(): void
    {
        $today = now()->toDateString();
        if ($this->last_active_date === $today) {
            return;
        }
        $oldLevel = $this->getFireLevel();
        $yesterday = now()->subDay()->toDateString();
        if ($this->last_active_date === $yesterday) {
            $this->increment('streak_count');
        } else {
            $this->update(['streak_count' => 1]);
        }
        $newStreak = $this->streak_count;
        if ($newStreak > $this->longest_streak) {
            $this->update(['longest_streak' => $newStreak]);
        }
        $this->update(['last_active_date' => $today]);
        $newLevel = $this->getFireLevel();
        if ($oldLevel !== $newLevel && $newLevel !== 'none') {
            session(['fire_level_up' => $newLevel]);
        }
    }

    public function getFireLevel(): string
    {
        $s = $this->streak_count;
        if ($s >= 2555) return 'eternal';
        if ($s >= 1825) return 'titan';
        if ($s >= 1460) return 'legendary';
        if ($s >= 1095) return 'immortal';
        if ($s >= 730) return 'ascended';
        if ($s >= 365) return 'inferno';
        if ($s >= 180) return 'supernova';
        if ($s >= 90) return 'mega';
        if ($s >= 30) return 'super';
        if ($s >= 14) return 'hot';
        if ($s >= 7) return 'warm';
        if ($s >= 3) return 'spark';
        if ($s >= 1) return 'ember';
        return 'none';
    }

    public function getFireEmoji(): string
    {
        return match($this->getFireLevel()) {
            'inferno' => '🔥🔥🔥',
            'supernova' => '🌋🔥',
            'mega' => '🔥⚡',
            'super' => '🔥',
            'hot' => '🔥',
            'warm' => '🕯️',
            'spark' => '✨',
            'ember' => '💫',
            default => '',
        };
    }

    public function getFireColor(): string
    {
        return match($this->getFireLevel()) {
            'eternal' => '#f472b6',
            'titan' => '#22d3ee',
            'legendary' => '#ec4899',
            'immortal' => '#a855f7',
            'ascended' => '#facc15',
            'inferno' => '#dc2626',
            'supernova' => '#ef4444',
            'mega' => '#f97316',
            'super' => '#f97316',
            'hot' => '#fb923c',
            'warm' => '#facc15',
            'spark' => '#7dd3fc',
            'ember' => '#94a3b8',
            default => '#555',
        };
    }
}
