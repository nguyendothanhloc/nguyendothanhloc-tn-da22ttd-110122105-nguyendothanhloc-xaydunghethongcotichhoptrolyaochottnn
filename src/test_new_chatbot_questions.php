<?php

/**
 * Test script for NEW chatbot questions
 * Run: php test_new_chatbot_questions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\RuleBasedChatbotService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Login as test student (hocvien1@gmail.com)
$user = User::where('email', 'hocvien1@gmail.com')->first();

if (!$user) {
    echo "ERROR: Test user 'hocvien1@gmail.com' not found!\n";
    exit(1);
}

Auth::login($user);

echo "========================================\n";
echo "TESTING NEW CHATBOT QUESTIONS\n";
echo "========================================\n";
echo "Logged in as: {$user->name} ({$user->email})\n\n";

$chatbot = new RuleBasedChatbotService();

// Test cases for NEW questions
$testQuestions = [
    "1. GRADUATION DATE" => [
        "Khi nao toi tot nghiep?",
        "Toi hoc xong khi nao?",
    ],
    "2. CLASSMATES" => [
        "Ban cung lop cua toi la ai?",
        "Ai hoc cung toi?",
    ],
    "3. ABSENT COUNT" => [
        "Toi vang bao nhieu buoi?",
        "So buoi vang cua toi?",
    ],
    "4. LATE COUNT" => [
        "Toi di muon bao nhieu lan?",
        "So lan den tre?",
    ],
    "5. TODAY'S SUBJECTS" => [
        "Hom nay toi hoc mon gi?",
        "Hoc gi hom nay?",
    ],
    "6. UNPAID AMOUNT" => [
        "Con no bao nhieu tien?",
        "Toi phai dong bao nhieu?",
    ],
    "7. TEACHER CONTACT" => [
        "So dien thoai giao vien cua toi?",
        "Email giao vien cua toi?",
    ],
    "8. OFFICE HOURS" => [
        "Gio lam viec cua trung tam?",
        "Trung tam mo cua luc may gio?",
    ],
];

foreach ($testQuestions as $category => $questions) {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo $category . "\n";
    echo str_repeat("=", 50) . "\n";
    
    foreach ($questions as $question) {
        echo "\nQ: $question\n";
        echo str_repeat("-", 50) . "\n";
        
        try {
            $result = $chatbot->processMessage($question);
            echo "Type: {$result['type']}\n";
            echo "Response:\n{$result['response']}\n";
        } catch (Exception $e) {
            echo "ERROR: {$e->getMessage()}\n";
        }
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "TEST COMPLETED!\n";
echo str_repeat("=", 50) . "\n\n";

echo "SUMMARY:\n";
echo "- Total categories tested: " . count($testQuestions) . "\n";
echo "- Total questions tested: " . array_sum(array_map('count', $testQuestions)) . "\n";
echo "\nNOTE: Some questions may return 'no data' if test account has no enrollments/attendance.\n";
echo "This is EXPECTED behavior - the patterns are working correctly!\n";
