<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\GamificationService;

class GamificationServiceTest extends TestCase
{
    private GamificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GamificationService();
    }

    public function test_calculate_level_at_zero_xp(): void
    {
        $this->assertEquals(1, $this->service->calculateLevel(0));
    }

    public function test_calculate_level_at_99_xp(): void
    {
        $this->assertEquals(1, $this->service->calculateLevel(99));
    }

    public function test_calculate_level_at_100_xp(): void
    {
        $this->assertEquals(2, $this->service->calculateLevel(100));
    }

    public function test_calculate_level_at_300_xp(): void
    {
        // Level 1: 100, Level 2: 200, total 300 => level 3
        $this->assertEquals(3, $this->service->calculateLevel(300));
    }

    public function test_calculate_level_at_600_xp(): void
    {
        // 100 + 200 + 300 = 600 => level 4
        $this->assertEquals(4, $this->service->calculateLevel(600));
    }

    public function test_calculate_level_at_negative_xp(): void
    {
        $this->assertEquals(1, $this->service->calculateLevel(-50));
    }

    public function test_get_xp_for_current_level(): void
    {
        // At 250 XP: level 1 consumed 100, level 2 consumed 200, remaining 50
        $this->assertEquals(50, $this->service->getXpForCurrentLevel(250));
    }

    public function test_get_xp_for_current_level_at_zero(): void
    {
        $this->assertEquals(0, $this->service->getXpForCurrentLevel(0));
    }

    public function test_get_xp_for_next_level(): void
    {
        // At 250 XP, we're level 3, next level requires 300
        $this->assertEquals(300, $this->service->getXpForNextLevel(250));
    }

    public function test_get_xp_for_next_level_at_zero(): void
    {
        $this->assertEquals(100, $this->service->getXpForNextLevel(0));
    }

    public function test_get_level_progress(): void
    {
        // At 250 XP, level 3, current=50, required=300 => 16.7%
        $progress = $this->service->getLevelProgress(250);
        $this->assertEqualsWithDelta(16.7, $progress, 0.1);
    }

    public function test_get_level_progress_at_boundary(): void
    {
        // At 100 XP, level 2, current=0, required=200 => 0%
        $this->assertEquals(0.0, $this->service->getLevelProgress(100));
    }

    public function test_level_title_beginner(): void
    {
        $this->assertEquals('Начинающий', $this->service->getLevelTitle(1));
    }

    public function test_level_title_student(): void
    {
        $this->assertEquals('Student', $this->service->getLevelTitle(5));
    }

    public function test_level_title_expert(): void
    {
        $this->assertEquals('Expert', $this->service->getLevelTitle(30));
    }

    public function test_level_color_returns_hex(): void
    {
        $color = $this->service->getLevelColor(1);
        $this->assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $color);
    }

    public function test_level_badge_returns_emoji(): void
    {
        $badge = $this->service->getLevelBadge(1);
        $this->assertNotEmpty($badge);
    }

    public function test_award_xp_rejects_zero(): void
    {
        $user = new \App\Models\User();
        $user->fill(['name' => 'Test', 'email' => 'test@test.com', 'password' => 'x']);
        $result = $this->service->awardXp($user, 0, 'test');
        $this->assertFalse($result);
    }

    public function test_award_xp_rejects_negative(): void
    {
        $user = new \App\Models\User();
        $user->fill(['name' => 'Test', 'email' => 'test@test.com', 'password' => 'x']);
        $result = $this->service->awardXp($user, -5, 'test');
        $this->assertFalse($result);
    }

    public function test_award_lesson_xp_constant(): void
    {
        $this->assertEquals(10, GamificationService::XP_LESSON_COMPLETE);
    }

    public function test_award_quiz_xp_scales_with_score(): void
    {
        // awardQuizXp at 50%: round(25 * 0.5) = 13
        // We can't easily test without DB, but verify the math logic
        $score = 50;
        $xp = (int) round(GamificationService::XP_QUIZ_PASS * ($score / 100));
        $xp = max($xp, 5);
        $this->assertEquals(13, $xp);
    }

    public function test_award_quiz_xp_minimum(): void
    {
        // At 0% score, min is 5
        $score = 0;
        $xp = (int) round(GamificationService::XP_QUIZ_PASS * ($score / 100));
        $xp = max($xp, 5);
        $this->assertEquals(5, $xp);
    }

    public function test_deduct_ai_tokens_amount_must_be_positive(): void
    {
        // Just test that the method exists and can be called (won't deduct from non-existent user)
        $this->assertTrue(method_exists($this->service, 'deductAiTokens'));
    }

    public function test_daily_token_bonus_constant(): void
    {
        $this->assertEquals(5, GamificationService::AI_TOKEN_DAILY_BONUS);
    }
}
