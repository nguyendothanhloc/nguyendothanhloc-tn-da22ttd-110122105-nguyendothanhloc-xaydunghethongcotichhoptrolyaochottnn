<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::setUser(\App\Models\User::find(6));

$chatbotService = app(\App\Services\RuleBasedChatbotService::class);

$testMessage = "Học phí khóa Tiếng Anh";

echo "=== TEST PATTERN MATCHING ===\n";
echo "Message: {$testMessage}\n\n";

// Use reflection to call private method
$reflection = new ReflectionClass($chatbotService);

// Test tryRuleBasedMatch
$method = $reflection->getMethod('tryRuleBasedMatch');
$method->setAccessible(true);

$result = $method->invoke($chatbotService, $testMessage);

if ($result === null) {
    echo "✅ No rule-based match found (will go to Gemini)\n";
} else {
    echo "⚠️  Rule-based match found:\n";
    echo "Type: {$result['type']}\n";
    echo "Response preview: " . substr($result['response'], 0, 100) . "...\n";
}
