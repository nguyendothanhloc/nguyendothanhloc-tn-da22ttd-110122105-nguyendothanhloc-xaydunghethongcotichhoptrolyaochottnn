<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\GeminiChatbotService;
use App\Models\User;

// Get test student
$user = User::where('email', 'hocvien1@gmail.com')->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

$student = \App\Models\Student::where('user_id', $user->id)->first();

if (!$student) {
    echo "❌ Student record not found!\n";
    exit(1);
}

$geminiService = new GeminiChatbotService();

// Test question
$question = "học phí tiếng trung";

echo "===== TEST: HỌC PHÍ TIẾNG TRUNG =====\n\n";
echo "User: {$user->name} (User ID: {$user->id})\n";
echo "Student ID: {$student->id}\n";
echo "Question: $question\n\n";

echo "Calling Gemini API...\n\n";

try {
    $response = $geminiService->generateResponse($question, $student->id);
    echo "✅ Gemini response:\n";
    echo str_repeat("=", 50) . "\n";
    echo $response . "\n";
    echo str_repeat("=", 50) . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n===== TEST COMPLETE =====\n";
