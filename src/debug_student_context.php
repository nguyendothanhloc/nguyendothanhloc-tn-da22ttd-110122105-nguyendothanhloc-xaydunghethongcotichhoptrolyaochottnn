<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG STUDENT CONTEXT ===\n\n";

// Lấy student ID = 1
$studentId = 1;

$geminiService = app(\App\Services\GeminiChatbotService::class);

// Sử dụng reflection để gọi private method buildStudentContext
$reflection = new ReflectionClass($geminiService);
$method = $reflection->getMethod('buildStudentContext');
$method->setAccessible(true);

echo "Gọi buildStudentContext cho student_id = {$studentId}...\n\n";

$context = $method->invoke($geminiService, $studentId);

echo "=== CONTEXT DATA ===\n";
echo json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "\n\n";

echo "=== KIỂM TRA ===\n";
echo "Số enrollments: " . count($context['enrollments'] ?? []) . "\n";
echo "Số schedules: " . count($context['schedules'] ?? []) . "\n";
echo "Số assessments: " . count($context['assessments'] ?? []) . "\n";
echo "Số payments: " . count($context['payments'] ?? []) . "\n\n";

// Kiểm tra xem enrollments có chứa thông tin course price không
if (!empty($context['enrollments'])) {
    echo "=== CHI TIẾT ENROLLMENTS ===\n";
    foreach ($context['enrollments'] as $idx => $enrollment) {
        echo ($idx + 1) . ". {$enrollment['course_name']}\n";
        echo "   Class: {$enrollment['class_name']}\n";
        echo "   Language: {$enrollment['language']}\n";
        echo "   Level: {$enrollment['level']}\n";
        echo "   Status: {$enrollment['status']}\n";
        echo "   Teacher: {$enrollment['teacher_name']}\n";
        
        // KIỂM TRA: Có trường price không?
        if (isset($enrollment['price'])) {
            echo "   ✅ Price: " . number_format($enrollment['price']) . " VND\n";
        } else {
            echo "   ❌ KHÔNG CÓ TRƯỜNG PRICE!\n";
        }
        echo "\n";
    }
}

// Test format prompt
echo "\n=== TEST FORMAT PROMPT ===\n";
$promptMethod = $reflection->getMethod('formatPrompt');
$promptMethod->setAccessible(true);

$testMessage = "Học phí của khóa Tiếng Anh là bao nhiêu?";
$prompt = $promptMethod->invoke($geminiService, $testMessage, $context);

echo "Message: {$testMessage}\n\n";
echo "Prompt length: " . strlen($prompt) . " characters\n\n";
echo "=== PROMPT CONTENT ===\n";
echo substr($prompt, 0, 2000) . "\n...\n";
