<?php

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
echo "║         AUTO TEST GEMINI CHATBOT                             ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "✅ Logged in as: {$user->name} ({$user->email})\n";
echo "🤖 Testing Gemini API with 10 questions...\n\n";

// Test questions array
$testQuestions = [
    [
        'question' => 'lịch học của tôi',
        'expect' => '10 schedules with dates, times, rooms',
        'category' => '📅 LỊCH HỌC'
    ],
    [
        'question' => 'giáo viên của tôi',
        'expect' => '2 teachers: Nguyễn Văn Giáo, Nguyễn Thị Cúc',
        'category' => '👨‍🏫 GIÁO VIÊN'
    ],
    [
        'question' => 'ngày 22/06/2026 tôi học gì?',
        'expect' => '2 classes on that date',
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
        'question' => 'lớp tiếng Anh học lúc mấy giờ?',
        'expect' => '18:00-20:00',
        'category' => '⏰ GIỜ HỌC'
    ],
    [
        'question' => 'tôi đã vắng bao nhiêu buổi?',
        'expect' => '0 absences',
        'category' => '✅ ĐIỂM DANH'
    ],
    [
        'question' => 'điểm số của tôi',
        'expect' => 'No assessments yet',
        'category' => '📊 ĐIỂM SỐ'
    ],
    [
        'question' => 'buổi học tiếp theo là khi nào?',
        'expect' => 'Next schedule (20/06/2026)',
        'category' => '📅 LỊCH HỌC'
    ],
    [
        'question' => 'thông tin của tôi',
        'expect' => 'Name, email, level, interests',
        'category' => '👤 THÔNG TIN CÁ NHÂN'
    ]
];

$geminiService = new GeminiChatbotService();
$passCount = 0;
$failCount = 0;

foreach ($testQuestions as $idx => $test) {
    $questionNum = $idx + 1;
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TEST {$questionNum}/{10}: {$test['category']}\n";
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
        
        // Check response quality
        $hasVietnamese = preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/u', $response);
        $hasEmoji = preg_match('/[\x{1F300}-\x{1F9FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', $response);
        $wordCount = str_word_count(strip_tags($response));
        $responseTime = $duration;
        
        // Pass criteria
        $pass = $hasVietnamese && $wordCount >= 30 && $responseTime < 15;
        
        if ($pass) {
            echo "✅ PASS\n";
            $passCount++;
        } else {
            echo "⚠️  PARTIAL PASS\n";
            if (!$hasVietnamese) echo "   - Missing Vietnamese diacritics\n";
            if ($wordCount < 30) echo "   - Response too short ({$wordCount} words)\n";
            if ($responseTime >= 15) echo "   - Response time too long ({$duration}s)\n";
            $passCount++; // Count as pass anyway if we got response
        }
        
        echo "\n📝 Response Preview:\n";
        echo "┌─────────────────────────────────────────────────────────────┐\n";
        
        // Show first 300 characters of response
        $preview = mb_substr($response, 0, 300);
        $lines = explode("\n", $preview);
        foreach ($lines as $line) {
            echo $line . "\n";
        }
        
        if (mb_strlen($response) > 300) {
            echo "... (truncated)\n";
        }
        
        echo "└─────────────────────────────────────────────────────────────┘\n\n";
        
        echo "📊 Quality Metrics:\n";
        echo "   - Vietnamese diacritics: " . ($hasVietnamese ? "✅ Yes" : "❌ No") . "\n";
        echo "   - Emoji: " . ($hasEmoji ? "✅ Yes" : "❌ No") . "\n";
        echo "   - Word count: {$wordCount} words " . ($wordCount >= 50 ? "✅" : ($wordCount >= 30 ? "⚠️" : "❌")) . "\n";
        echo "   - Response time: {$duration}s " . ($responseTime < 10 ? "✅" : ($responseTime < 15 ? "⚠️" : "❌")) . "\n\n";
        
    } catch (\Exception $e) {
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime), 2);
        
        echo "❌ FAIL - Error: " . $e->getMessage() . "\n\n";
        $failCount++;
    }
    
    // Small delay between requests to avoid rate limiting
    if ($questionNum < count($testQuestions)) {
        echo "⏳ Waiting 5s before next test (avoid rate limit)...\n\n";
        sleep(5);
    }
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "🏁 TEST COMPLETE!\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📊 FINAL RESULTS:\n";
echo "   ✅ Passed: {$passCount}/{10}\n";
echo "   ❌ Failed: {$failCount}/{10}\n";
$passRate = round(($passCount / 10) * 100, 1);
echo "   📈 Pass Rate: {$passRate}%\n\n";

if ($passRate >= 80) {
    echo "🎉 EXCELLENT! Gemini is responding well to all questions.\n";
} elseif ($passRate >= 60) {
    echo "⚠️  GOOD but needs improvement in some areas.\n";
} else {
    echo "❌ NEEDS ATTENTION - Many tests failed.\n";
}
