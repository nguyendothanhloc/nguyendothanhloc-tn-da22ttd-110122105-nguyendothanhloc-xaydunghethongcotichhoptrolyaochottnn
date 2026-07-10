<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RuleBasedChatbotService;
use App\Services\GeminiChatbotService;
use App\Models\User;

// Get test student
$student = User::where('email', 'hocvien1@gmail.com')->first();

if (!$student) {
    echo "❌ Student not found!\n";
    exit(1);
}

$ruleBasedService = new RuleBasedChatbotService();
$geminiService = new GeminiChatbotService();

// Test questions
$testQuestions = [
    "tôi muốn học tiếng trung",
    "học phí tiếng trung"
];

echo "===== CHATBOT DEBUG TEST =====\n\n";
echo "Student: {$student->name} (ID: {$student->id})\n\n";

foreach ($testQuestions as $index => $question) {
    echo "===== TEST " . ($index + 1) . " =====\n";
    echo "Question: $question\n";
    
    // Step 1: Rule-based pattern matching
    echo "Step 1: Rule-based pattern matching...\n";
    $reflection = new ReflectionClass($ruleBasedService);
    $method = $reflection->getMethod('tryRuleBasedMatch');
    $method->setAccessible(true);
    $ruleResult = $method->invoke($ruleBasedService, $question, $student->id);
    
    if ($ruleResult) {
        echo "✅ Rule-based MATCHED!\n";
        echo "Response: " . substr($ruleResult, 0, 200) . "...\n\n";
        continue;
    } else {
        echo "❌ Rule-based: No match\n\n";
    }
    
    // Step 2: Knowledge Base (FAQ) search
    echo "Step 2: Knowledge Base (FAQ) search...\n";
    $method2 = $reflection->getMethod('searchKnowledgeBase');
    $method2->setAccessible(true);
    $faqResult = $method2->invoke($ruleBasedService, $question, $student->id);
    
    if ($faqResult) {
        echo "✅ FAQ MATCHED!\n";
        if (is_array($faqResult)) {
            echo "Response type: array\n";
            echo "Response: " . substr($faqResult['response'] ?? json_encode($faqResult), 0, 200) . "...\n\n";
        } else {
            echo "Response: " . substr($faqResult, 0, 200) . "...\n\n";
        }
        continue;
    } else {
        echo "❌ FAQ: No match\n";
        
        // Debug: Check why FAQ didn't match
        echo "\n--- FAQ DEBUG ---\n";
        $reflection2 = new ReflectionClass($ruleBasedService);
        $normalizeMethod = $reflection2->getMethod('removeVietnameseAccents');
        $normalizeMethod->setAccessible(true);
        $normalized = $normalizeMethod->invoke($ruleBasedService, $question);
        echo "Normalized question: $normalized\n";
        
        // Check all FAQs with "trung" keyword
        $faqs = \App\Models\ChatbotKnowledge::where('keywords', 'like', '%trung%')
            ->orderBy('priority', 'desc')
            ->get(['id', 'question', 'keywords', 'priority']);
        
        echo "FAQs with 'trung' keyword:\n";
        foreach ($faqs as $faq) {
            echo "  ID {$faq->id}: {$faq->question}\n";
            echo "    Keywords: {$faq->keywords}\n";
            echo "    Priority: {$faq->priority}\n";
            
            // Test keyword matching
            $keywords = array_map('trim', explode(',', $faq->keywords));
            $matched = false;
            foreach ($keywords as $keyword) {
                $normalizedKeyword = $normalizeMethod->invoke($ruleBasedService, $keyword);
                if (empty($normalizedKeyword)) continue;
                
                if (str_contains($normalized, $normalizedKeyword)) {
                    echo "    ✅ MATCH with keyword: '$keyword' (normalized: '$normalizedKeyword')\n";
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                echo "    ❌ No keyword match\n";
            }
            echo "\n";
        }
        echo "--- END FAQ DEBUG ---\n\n";
    }
    
    // Step 3: Gemini AI
    echo "Step 3: Gemini AI fallback...\n";
    try {
        $geminiResponse = $geminiService->generateResponse($question, $student->id);
        echo "✅ Gemini response:\n";
        echo substr($geminiResponse, 0, 300) . "...\n\n";
    } catch (Exception $e) {
        echo "❌ Gemini error: " . $e->getMessage() . "\n\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n\n";
}

echo "===== TEST COMPLETE =====\n";
