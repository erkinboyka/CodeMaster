<?php

namespace App\Console\Commands;

use App\Services\Parsers\LeetCodeParser;
use App\Services\Parsers\ACMPParser;
use App\Services\Parsers\StepikParser;
use App\Services\Parsers\MetanitParser;
use Illuminate\Console\Command;

class ParseProblems extends Command
{
    protected $signature = 'parse:problems
        {--source= : leetcode, acmp, stepik, metanit, or all}
        {--limit= : Max problems per source}';

    protected $description = 'Parse problems from external sources';

    public function handle(): int
    {
        $source = $this->option('source') ?? 'all';
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        $this->info('=== Problem Parser ===');
        $this->newLine();

        $parsers = match ($source) {
            'leetcode' => ['leetcode' => new LeetCodeParser()],
            'acmp' => ['acmp' => new ACMPParser()],
            'stepik' => ['stepik' => new StepikParser()],
            'metanit' => ['metanit' => new MetanitParser()],
            'all' => [
                'leetcode' => new LeetCodeParser(),
                'acmp' => new ACMPParser(),
                'stepik' => new StepikParser(),
                'metanit' => new MetanitParser(),
            ],
            default => [],
        };

        if (empty($parsers)) {
            $this->error("Unknown source: {$source}");
            return Command::FAILURE;
        }

        $total = ['total' => 0, 'created' => 0, 'skipped' => 0, 'errors' => 0];

        foreach ($parsers as $name => $parser) {
            $this->info("Parsing {$name}...");
            $this->newLine();

            $results = $parser->parse();

            $total['total'] += $results['total'];
            $total['created'] += $results['created'];
            $total['skipped'] += $results['skipped'];
            $total['errors'] += $results['errors'];

            $this->newLine();
            $this->info("  {$name}:");
            $this->line("    Found:     {$results['total']}");
            $this->line("    Created:   {$results['created']}");
            $this->line("    Skipped:   {$results['skipped']}");
            $this->line("    Errors:    {$results['errors']}");
            $this->newLine();
        }

        $this->info('=== TOTAL ===');
        $this->line("Found:     {$total['total']}");
        $this->line("Created:   {$total['created']}");
        $this->line("Skipped:   {$total['skipped']}");
        $this->line("Errors:    {$total['errors']}");

        return Command::SUCCESS;
    }
}
