<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\RuleBasedChatbotService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Login as test student
$user = User::where('email', 'hocvien1@gmail.com')->first();
Auth::login($user);

$chatbot = new RuleBasedChatbotService();

// Test cases
$testCases = [
    "lịch học hôm nay",
    "giáo viên của tôi là ai",
    "học phí tiếng nhật bao nhiêu",
    "giáo viên dạy tiếng nhật là ai",
    "còn bao nhiêu buổi học",
    "lớp học của tôi có bao nhiêu người",
    "help",
    "xin chào"
];

echo "=== CHATBOT PATTERN TESTING ===\n\n";

foreach ($testCases as $question) {
    echo "Q: {$question}\n";
    $result = $chatbot->processMessage($question);
    echo "Type: {$result['type']}\n";
    echo "Response: " . substr($result['response'], 0, 100) . "...\n";
    echo str_repeat("-", 80) . "\n\n";
}
