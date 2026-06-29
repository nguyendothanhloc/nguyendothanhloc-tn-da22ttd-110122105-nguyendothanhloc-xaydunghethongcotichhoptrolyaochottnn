<?php

/**
 * FOCUSED GEMINI TEST - 5 KEY QUESTIONS
 * 
 * Tests 5 most important questions with 10s delays to avoid rate limits.
 * Run this for quick validation after making changes.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Services\GeminiChatbotService;
use Illuminate\Support\Facades\Auth;

// Login as student
$user = User::where('email', 'hocvien1@gmail.com')->first();
Auth::login($user);

$student = Student::where('user_id', $user->id)->first();

if (!$student) {
    echo "❌ Student not found!\n";
    exit(1);
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║      FOCUSED GEMINI TEST - 5 KEY QUESTIONS                   ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "✅ Logged in as: {$user->name} ({$user->email})\n";
echo "🤖 Testing 5 critical questions with 10s delays...\n";
echo "⏱️  Estimated time: ~60 seconds\n\n";

// 5 most important test questions
$testQuestions = [
    [
        'question' => 'giáo viên của tôi',
        'expect' => '2 teachers: Nguyễn Văn Giáo, Nguyễn Thị Cúc',
        'category' => '👨‍🏫 GIÁO VIÊN'
    ],
    [
        'question' => 'lịch học của tôi',
        'expect' => '10 schedules with dates, times, rooms',
        'category' => '📅 LỊCH HỌC'
    ],
    [
        'question' => 'tỷ lệ điểm danh của tôi',
        'expect' => '100% attendance rate',
        'category' => '✅ ĐIỂM DANH'
    ],
    [
        'question' => 'tôi đang học khóa nào?',
        'expect' => 'Tiếng Anh + Tiếng Nhật',
        'category' => '📚 KHÓA HỌC'
    ],
    [
        'question' => 'ngày 22/06/2026 tôi học gì?',
        'expect' => '2 classes on that date',
        'category' => '📅 LỊCH CỤ THỂ'
    ]
];

$geminiService = new GeminiChatbotService();
$passCount = 0;
$failCount = 0;

foreach ($testQuestions as $idx => $test) {
    $questionNum = $idx + 1;
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TEST {$questionNum}/5: {$test['category']}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "❓ Question: \"{$test['question']}\"\n";
    echo "🎯 Expected: {$test['expect']}\n\n";
    
    echo "⏳ Calling Gemini API...\n";
    $startTime = microtime(true);
    
    try {
        // Call Gemini with student context
        $response = $geminiService->generateResponse($test['question'], $student->id);
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime), 2);
        
        echo "✅ Response received in {$duration}s\n\n";
        
        // Check if response is an error message (rate limit)
        $isError = (
            stripos($response, 'xin lỗi') !== false &&
            (stripos($response, 'tạm thời không khả dụng') !== false || 
             stripos($response, 'đang xử lý nhiều yêu cầu') !== false)
        );
        
        if ($isError) {
            echo "⚠️  RATE LIMITED\n\n";
            echo "📝 Error Message:\n";
            echo "┌─────────────────────────────────────────────────────────────┐\n";
            echo $response . "\n";
            echo "└─────────────────────────────────────────────────────────────┘\n\n";
            $failCount++;
        } else {
            // Check response quality
            $hasVietnamese = preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/u', $response);
            $hasEmoji = preg_match('/[\x{1F300}-\x{1F9FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', $response);
            $wordCount = str_word_count(strip_tags($response));
            
            echo "✅ PASS\n\n";
            $passCount++;
            
            echo "📝 Response Preview:\n";
            echo "┌─────────────────────────────────────────────────────────────┐\n";
            
            // Show first 400 characters of response
            $preview = mb_substr($response, 0, 400);
            $lines = explode("\n", $preview);
            foreach ($lines as $line) {
                echo $line . "\n";
            }
            
            if (mb_strlen($response) > 400) {
                echo "... (truncated, total " . mb_strlen($response) . " chars)\n";
            }
            
            echo "└─────────────────────────────────────────────────────────────┘\n\n";
            
            echo "📊 Quality Metrics:\n";
            echo "   - Vietnamese diacritics: " . ($hasVietnamese ? "✅ Yes" : "❌ No") . "\n";
            echo "   - Emoji: " . ($hasEmoji ? "✅ Yes" : "❌ No") . "\n";
            echo "   - Word count: {$wordCount} words " . ($wordCount >= 50 ? "✅" : ($wordCount >= 30 ? "⚠️" : "❌")) . "\n";
            echo "   - Response time: {$duration}s " . ($duration < 10 ? "✅" : ($duration < 15 ? "⚠️" : "❌")) . "\n\n";
        }
        
    } catch (\Exception $e) {
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime), 2);
        
        echo "❌ FAIL - Exception: " . $e->getMessage() . "\n\n";
        $failCount++;
    }
    
    // Longer delay to avoid rate limiting
    if ($questionNum < count($testQuestions)) {
        echo "⏳ Waiting 10s before next test (avoid rate limit)...\n";
        for ($i = 10; $i > 0; $i--) {
            echo "\r   {$i}s remaining...";
            sleep(1);
        }
        echo "\r                      \n\n";
    }
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "🏁 TEST COMPLETE!\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📊 FINAL RESULTS:\n";
echo "   ✅ Passed: {$passCount}/5\n";
echo "   ❌ Failed (Rate Limits): {$failCount}/5\n";
$passRate = round(($passCount / 5) * 100, 1);
echo "   📈 Pass Rate: {$passRate}%\n\n";

if ($passRate >= 80) {
    echo "🎉 EXCELLENT! Gemini is responding well.\n";
} elseif ($passRate >= 60) {
    echo "⚠️  GOOD but some rate limits hit.\n";
} elseif ($passRate >= 40) {
    echo "⚠️  PARTIAL - Too many rate limits. Try again later.\n";
} else {
    echo "❌ NEEDS ATTENTION - Most tests failed.\n";
}

echo "\n💡 TIP: If you hit rate limits, wait 5-10 minutes before retesting.\n";
echo "     Gemini free tier has limits of ~2-3 requests per minute.\n\n";

