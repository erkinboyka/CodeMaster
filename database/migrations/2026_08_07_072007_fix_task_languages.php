<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $updates = [
            // MySQL tasks
            [143, 'mysql'], [144, 'mysql'], [145, 'mysql'], [146, 'mysql'], [147, 'mysql'],
            // C++ tasks
            [151, 'cpp'], [152, 'cpp'], [153, 'cpp'],
            // Python tasks
            [154, 'python'], [155, 'python'], [156, 'python'],
            // Java tasks
            [157, 'java'], [158, 'java'], [159, 'java'],
            // C# tasks
            [160, 'csharp'], [161, 'csharp'], [162, 'csharp'],
            // PHP tasks (PDO, файлы, маршруты, миграции, blade, middleware, api)
            [133, 'php'], [134, 'php'], [135, 'php'], [136, 'php'], [137, 'php'],
            [138, 'php'], [139, 'php'], [140, 'php'], [141, 'php'], [142, 'php'],
            // JavaScript tasks (fetch, DOM, async, калькулятор, список задач)
            [128, 'javascript'], [129, 'javascript'], [130, 'javascript'],
            [131, 'javascript'], [132, 'javascript'],
            // TypeScript tasks
            [204, 'typescript'], [205, 'typescript'], [206, 'typescript'], [207, 'typescript'],
            // Node.js/Express tasks
            [195, 'javascript'], [196, 'javascript'], [197, 'javascript'],
            [198, 'javascript'], [199, 'javascript'], [200, 'javascript'],
            [201, 'javascript'], [202, 'javascript'], [203, 'javascript'],
            // React tasks
            [187, 'javascript'], [188, 'javascript'], [189, 'javascript'],
            [190, 'javascript'], [191, 'javascript'], [192, 'javascript'],
            [193, 'javascript'], [194, 'javascript'],
        ];

        foreach ($updates as [$id, $lang]) {
            DB::table('lesson_practice_tasks')
                ->where('id', $id)
                ->update(['language' => $lang]);
        }
    }

    public function down(): void
    {
        DB::table('lesson_practice_tasks')
            ->whereIn('id', range(128, 207))
            ->update(['language' => 'html']);
    }
};
