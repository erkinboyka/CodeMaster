<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'name',
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
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->role = $user->role ?? 'user';
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
        $this->increment('xp', $amount);
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ai_coins' => 'integer',
            'xp' => 'integer',
            'ai_tokens' => 'integer',
            'level' => 'integer',
            'total_xp' => 'integer',
            'is_verified' => 'boolean',
            'is_blocked' => 'boolean',
            'failed_login_attempts' => 'integer',
            'locked_until' => 'datetime',
            'last_login' => 'datetime',
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
}
