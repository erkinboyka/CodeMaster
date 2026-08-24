<?php

namespace App\Services;

class EloCalculator
{
    private const K_FACTOR = 32;

    public static function expectedScore(float $ratingA, float $ratingB): float
    {
        return 1 / (1 + pow(10, ($ratingB - $ratingA) / 400));
    }

    public static function calculate(array $participants): array
    {
        $ratings = [];
        foreach ($participants as $p) {
            $ratings[$p['user_id']] = $p['rating'] ?? 1200;
        }

        $results = [];
        $totalParticipants = count($participants);

        usort($participants, fn($a, $b) => ($b['score'] ?? 0) <=> ($a['score'] ?? 0));

        foreach ($participants as $rank => $participant) {
            $userId = $participant['user_id'];
            $currentRating = $ratings[$userId];
            $actualScore = self::actualScore($rank, $totalParticipants);

            $expectedTotal = 0;
            foreach ($participants as $other) {
                if ($other['user_id'] === $userId) continue;
                $expectedTotal += self::expectedScore($currentRating, $ratings[$other['user_id']]);
            }
            $expectedTotal /= max(1, $totalParticipants - 1);

            $newRating = max(100, round($currentRating + self::K_FACTOR * ($actualScore - $expectedTotal)));

            $results[] = [
                'user_id' => $userId,
                'rating_before' => $currentRating,
                'rating_after' => $newRating,
                'rating_change' => $newRating - $currentRating,
                'rank_position' => $rank + 1,
                'participants_count' => $totalParticipants,
            ];
        }

        return $results;
    }

    private static function actualScore(int $rank, int $total): float
    {
        if ($total <= 1) return 0.5;
        if ($rank === 0) return 1.0;
        if ($rank === $total - 1) return 0.0;
        return ($total - 1 - $rank) / ($total - 1);
    }
}
