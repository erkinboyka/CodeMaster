<?php
$db = new PDO('mysql:host=127.127.126.50;port=3306;dbname=codemaster;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Check if all required tables exist
$tables = ['lessons', 'courses', 'lesson_quizzes', 'quiz_questions', 'quiz_options', 'lesson_practice_tasks', 'user_lesson_progress', 'user_course_progress'];
foreach ($tables as $t) {
    $r = $db->query("SHOW TABLES LIKE '$t'")->fetch();
    echo "$t: " . ($r ? 'EXISTS' : 'MISSING') . "\n";
}

echo "\n";

// Try the exact query from the controller
try {
    $lesson = $db->query('SELECT * FROM lessons WHERE course_id = 5 AND id = 729')->fetch(PDO::FETCH_ASSOC);
    echo "Lesson query OK: " . ($lesson ? $lesson['title'] : 'NOT FOUND') . "\n";
} catch (Exception $e) {
    echo "Lesson query ERROR: " . $e->getMessage() . "\n";
}

// Try eager loading quizQuestions.options
try {
    $r = $db->query('SELECT COUNT(*) as cnt FROM quiz_questions WHERE lesson_id = 729')->fetch();
    echo "quiz_questions for lesson 729: " . $r['cnt'] . "\n";
} catch (Exception $e) {
    echo "quiz_questions ERROR: " . $e->getMessage() . "\n";
}

// Check if quiz_questions table structure is correct
try {
    $cols = $db->query('DESCRIBE quiz_questions')->fetchAll(PDO::FETCH_COLUMN);
    echo "quiz_questions columns: " . implode(', ', $cols) . "\n";
} catch (Exception $e) {
    echo "quiz_questions DESCRIBE ERROR: " . $e->getMessage() . "\n";
}

// Check quiz_options table structure
try {
    $cols = $db->query('DESCRIBE quiz_options')->fetchAll(PDO::FETCH_COLUMN);
    echo "quiz_options columns: " . implode(', ', $cols) . "\n";
} catch (Exception $e) {
    echo "quiz_options DESCRIBE ERROR: " . $e->getMessage() . "\n";
}
