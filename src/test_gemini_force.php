<?php

/**
 * Force Gemini AI Test - Use questions that absolutely don't match any patterns
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\RuleBasedChatbotService;
use Illuminate\Support\Facades\Auth;

echo "===========================================\n";
echo "FORCE GEMINI AI TEST\n";
echo "===========================================\n\n";

$student = User::where('role', 'student')->where('email', 'hocvien1@gmail.com')->first();

if (!$student || !$student->student) {
    echo "❌ ERROR: No test student found\n";
    exit(1);
}

Auth::login($student);
echo "Test Student: {$student->name}\n";
echo "-------------------------------------------\n\n";

$chatbotService = new RuleBasedChatbotService();

// Questions that should NOT match any Rule-Based patterns or FAQ
$uniqueQuestions = [
    "Khủng long tuyệt chủng khi nào?",  // Completely unrelated to language center
    "Viết cho tôi một bài thơ về mùa xuân",  // Creative request
    "Giải thích định luật Newton thứ hai",  // Physics question
    "Tokyo là thủ đô của nước nào?",  // Geography question
    "Làm thế nào để nấu phở?",  // Cooking question
];

echo "TESTING WITH COMPLETELY UNRELATED QUESTIONS\n";
echo "(These should force Gemini AI to respond)\n";
echo "===========================================\n\n";

$results = [
    'ai_powered' => 0,
    'error' => 0,
    'other' => 0
];

foreach ($uniqueQuestions as $index => $question) {
    $testNumber = $index + 1;
    echo "[TEST #{$testNumber}] Question: \"{$question}\"\n";
    
    try {
        $startTime = microtime(true);
        $response = $chatbotService->processMessage($question);
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);
        
        echo "Response time: {$duration}ms\n";
        echo "Response type: " . ($response['type'] ?? 'unknown') . "\n";
        
        if (isset($response['type'])) {
            if ($response['type'] === 'ai_powered') {
                echo "✅ SUCCESS: Gemini AI responded!\n";
                echo "Response: " . substr($response['response'], 0, 150) . "...\n";
                $results['ai_powered']++;
            } elseif ($response['type'] === 'error') {
                echo "⚠️ ERROR response\n";
                echo "Message: " . substr($response['response'], 0, 100) . "...\n";
                $results['error']++;
            } else {
                echo "⚠️ Matched other pattern: " . $response['type'] . "\n";
                echo "Response: " . substr($response['response'], 0, 100) . "...\n";
                $results['other']++;
            }
        }
        
    } catch (\Exception $e) {
        echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
        $results['error']++;
    }
    
    echo "-------------------------------------------\n\n";
    
    // Small delay to avoid rate limiting
    usleep(500000); // 0.5 seconds
}

// Summary
echo "===========================================\n";
echo "SUMMARY\n";
echo "===========================================\n\n";

$total = count($uniqueQuestions);
echo "Total questions: {$total}\n";
echo "AI-powered responses: {$results['ai_powered']}\n";
echo "Error responses: {$results['error']}\n";
echo "Other pattern matches: {$results['other']}\n\n";

if ($results['ai_powered'] > 0) {
    $percentage = round(($results['ai_powered'] / $total) * 100, 2);
    echo "🎉 SUCCESS! Gemini AI is working ({$percentage}% success rate)\n";
} else {
    echo "❌ FAILED: Gemini AI was never called\n";
    echo "\nPossible reasons:\n";
    echo "1. API key is invalid\n";
    echo "2. Network/firewall blocking API calls\n";
    echo "3. Patterns are too broad and catching everything\n";
    echo "4. Error fallback is being triggered\n";
}

echo "\n===========================================\n";
