<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST CHATBOT VIA UI FLOW ===\n\n";

// Simulate authenticated user
$user = DB::table('users')->where('email', 'hocvien1@gmail.com')->first();
if (!$user) {
    echo "❌ User not found\n";
    exit(1);
}

echo "✅ User: {$user->name} (ID: {$user->id})\n\n";

// Mock Auth::user()
Auth::setUser(\App\Models\User::find($user->id));

// Test chatbot controller flow
$chatbotService = app(\App\Services\RuleBasedChatbotService::class);
$conversationService = app(\App\Services\ConversationService::class);

$testQuestions = [
    "Lịch học hôm nay",
    "Học phí khóa Tiếng Anh",
];

foreach ($testQuestions as $idx => $question) {
    echo "📝 Test " . ($idx + 1) . ": {$question}\n";
    echo str_repeat("-", 60) . "\n";
    
    try {
        // Step 1: Get or create conversation
        $conversation = $conversationService->getOrCreateConversation();
        echo "✅ Conversation ID: {$conversation->id}\n";
        
        // Step 2: Save user message
        $conversationService->saveUserMessage($conversation, $question);
        echo "✅ User message saved\n";
        
        // Step 3: Process message
        $result = $chatbotService->processMessage($question);
        echo "✅ Response type: {$result['type']}\n";
        echo "🤖 Response: " . substr($result['response'], 0, 200) . "...\n\n";
        
        // Step 4: Save assistant message
        $conversationService->saveAssistantMessage($conversation, $result['response']);
        echo "✅ Assistant message saved\n";
        
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n\n";
}

echo "✅ Test completed\n";
