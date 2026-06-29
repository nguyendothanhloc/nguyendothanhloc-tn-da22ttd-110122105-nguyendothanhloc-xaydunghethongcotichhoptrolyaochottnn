<?php

/**
 * Test Gemini AI Integration
 * This script tests if Gemini API is working by asking complex questions
 * that don't match any Rule-Based patterns or FAQ entries
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\RuleBasedChatbotService;
use App\Services\GeminiChatbotService;
use Illuminate\Support\Facades\Auth;

echo "===========================================\n";
echo "GEMINI AI INTEGRATION TEST\n";
echo "===========================================\n\n";

// Check API key
$apiKey = env('GEMINI_API_KEY');
if (!$apiKey) {
    echo "❌ ERROR: GEMINI_API_KEY not set in .env file\n";
    exit(1);
}

echo "✅ API Key found: " . substr($apiKey, 0, 20) . "...\n";
echo "-------------------------------------------\n\n";

// Get test student
$student = User::where('role', 'student')->where('email', 'hocvien1@gmail.com')->first();

if (!$student || !$student->student) {
    echo "❌ ERROR: No test student found (hocvien1@gmail.com)\n";
    exit(1);
}

Auth::login($student);

echo "Test Student: {$student->name} ({$student->email})\n";
echo "-------------------------------------------\n\n";

// Test 1: Direct Gemini API test
echo "TEST 1: DIRECT GEMINI API TEST\n";
echo "===========================================\n";

try {
    $geminiService = new GeminiChatbotService();
    
    $testQuestion = "Xin chào, bạn có thể giúp tôi không?";
    echo "Question: \"{$testQuestion}\"\n\n";
    
    echo "Calling Gemini API...\n";
    $response = $geminiService->generateResponse($testQuestion, $student->id);
    
    echo "✅ SUCCESS: Gemini API responded!\n";
    echo "\nResponse:\n";
    echo "-------------------------------------------\n";
    echo $response . "\n";
    echo "-------------------------------------------\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: Gemini API failed\n";
    echo "Error message: " . $e->getMessage() . "\n";
    echo "Error trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n\n";

// Test 2: Chatbot with complex question (should use Gemini)
echo "TEST 2: COMPLEX QUESTION VIA CHATBOT\n";
echo "===========================================\n";

$chatbotService = new RuleBasedChatbotService();

// Complex questions that don't match any patterns or FAQ
$complexQuestions = [
    "Bạn nghĩ tôi nên học tiếng gì để có triển vọng công việc tốt?",
    "So sánh ưu nhược điểm giữa tiếng Nhật và tiếng Hàn",
    "Tôi nên học bao lâu để đạt trình độ giao tiếp được?",
];

$aiResponseCount = 0;
$totalQuestions = count($complexQuestions);

foreach ($complexQuestions as $index => $question) {
    $testNumber = $index + 1;
    echo "\n[TEST 2.{$testNumber}] Question: \"{$question}\"\n";
    echo "-------------------------------------------\n";
    
    try {
        $response = $chatbotService->processMessage($question);
        
        // Check if response is from AI
        if (isset($response['type']) && $response['type'] === 'ai_powered') {
            echo "✅ SUCCESS: Gemini AI was used (type: ai_powered)\n";
            echo "\nResponse preview:\n";
            echo substr($response['response'], 0, 200) . "...\n";
            $aiResponseCount++;
        } else {
            echo "⚠️ WARNING: Response type is not ai_powered\n";
            echo "Response type: " . ($response['type'] ?? 'unknown') . "\n";
            echo "Response preview: " . substr($response['response'], 0, 100) . "...\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n\n";

// Test 3: Check .env configuration
echo "TEST 3: CONFIGURATION CHECK\n";
echo "===========================================\n";

$config = [
    'GEMINI_API_KEY' => env('GEMINI_API_KEY') ? '✅ Set' : '❌ Not set',
    'GEMINI_API_URL' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent'),
];

foreach ($config as $key => $value) {
    echo "{$key}: {$value}\n";
}

echo "\n";

// Summary
echo "===========================================\n";
echo "TEST SUMMARY\n";
echo "===========================================\n";

echo "\n📊 RESULTS:\n";
echo "  - Direct API test: ✅ Passed\n";
echo "  - Complex questions: {$aiResponseCount}/{$totalQuestions} used AI\n";
echo "  - Configuration: ✅ Valid\n";

if ($aiResponseCount === $totalQuestions) {
    echo "\n🎉 ALL TESTS PASSED! Gemini AI is working perfectly!\n";
} elseif ($aiResponseCount > 0) {
    echo "\n⚠️ PARTIAL SUCCESS: Some questions used AI, some didn't.\n";
    echo "   This might be expected if questions matched patterns/FAQ.\n";
} else {
    echo "\n❌ FAILED: No questions used AI. Check Layer 1 & 2 patterns.\n";
}

echo "\n===========================================\n";
