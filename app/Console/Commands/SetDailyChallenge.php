<?php

namespace App\Console\Commands;

use App\Models\DailyChallenge;
use App\Models\Problem;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SetDailyChallenge extends Command
{
    protected $signature = 'challenge:set {--date=today}';
    protected $description = 'Set the daily challenge problem and notify all users';

    public function handle()
    {
        $date = $this->option('date') === 'today' ? Carbon::today() : Carbon::parse($this->option('date'));

        $existing = DailyChallenge::where('challenge_date', $date)->first();
        if ($existing) {
            $this->warn("Challenge already set for {$date->toDateString()}: Problem #{$existing->problem_id}");
            return 0;
        }

        $problem = Problem::whereNotIn('id', DailyChallenge::pluck('problem_id'))
            ->inRandomOrder()
            ->first();

        if (!$problem) {
            $problem = Problem::inRandomOrder()->first();
        }

        if (!$problem) {
            $this->error('No problems available');
            return 1;
        }

        DailyChallenge::create([
            'challenge_date' => $date,
            'problem_id' => $problem->id,
        ]);

        $this->info("Daily challenge for {$date->toDateString()}: {$problem->title}");

        $this->notifyUsers($problem);

        return 0;
    }

    private function notifyUsers(Problem $problem): void
    {
        $difficultyEmoji = match ($problem->difficulty) {
            'easy' => '🟢',
            'medium' => '🟡',
            'hard' => '🔴',
            default => '⚡',
        };

        $message = "{$difficultyEmoji} Новый Daily Challenge: \"{$problem->title}\" ({$problem->difficulty}) — Решай сейчас!";

        $userIds = User::where('is_blocked', false)->pluck('id');

        $notifications = $userIds->map(fn($userId) => [
            'user_id' => $userId,
            'message' => $message,
            'notification_time' => now(),
            'is_read' => false,
        ])->toArray();

        foreach (array_chunk($notifications, 500) as $chunk) {
            Notification::insert($chunk);
        }

        $this->info("Notified {$userIds->count()} users");
    }
}
