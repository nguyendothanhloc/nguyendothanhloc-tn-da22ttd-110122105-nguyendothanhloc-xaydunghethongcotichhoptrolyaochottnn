<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$chatbotService = app(\App\Services\RuleBasedChatbotService::class);

$testMessages = [
    "Học phí khóa Tiếng Anh",
    "Xin chào",
    "Hello",
    "Hi",
    "hoc phi khoa tieng anh"
];

$reflection = new ReflectionClass($chatbotService);
$removeAccents = $reflection->getMethod('removeVietnameseAccents');
$removeAccents->setAccessible(true);

echo "=== TEST NORMALIZE ===\n\n";

foreach ($testMessages as $msg) {
    $normalized = $removeAccents->invoke($chatbotService, $msg);
    echo "Original: '{$msg}'\n";
    echo "Normalized: '{$normalized}'\n\n";
}

// Test pattern matching
$matchesPattern = $reflection->getMethod('matchesPattern');
$matchesPattern->setAccessible(true);

$message = "Học phí khóa Tiếng Anh";
$normalizedMsg = $removeAccents->invoke($chatbotService, trim($message));

echo "=== PATTERN MATCHING FOR: '{$message}' ===\n";
echo "Normalized: '{$normalizedMsg}'\n\n";

$patterns = [
    ['xin chao', 'hello', 'hi'],
    ['khoa hoc', 'course', 'co khoa nao'],
    ['hoc phi', 'phi', 'gia'],
];

foreach ($patterns as $idx => $patternSet) {
    $match = $matchesPattern->invoke($chatbotService, $normalizedMsg, $patternSet);
    echo "Pattern " . ($idx + 1) . " " . json_encode($patternSet) . ": " . ($match ? "✅ MATCH" : "❌ NO MATCH") . "\n";
}
