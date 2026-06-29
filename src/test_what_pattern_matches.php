<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Services\RuleBasedChatbotService;
use Illuminate\Support\Facades\Auth;

// Login as student
$user = User::where('email', 'hocvien1@gmail.com')->first();
Auth::login($user);

$testMessages = [
    'giáo viên của tôi',
    'thông tin giáo viên',
    'khóa học',
];

$service = new RuleBasedChatbotService();

// Use reflection to access private method
$reflection = new ReflectionClass($service);
$tryMatchMethod = $reflection->getMethod('tryRuleBasedMatch');
$tryMatchMethod->setAccessible(true);

foreach ($testMessages as $msg) {
    echo "=== Testing: '$msg' ===\n";
    
    $result = $tryMatchMethod->invoke($service, $msg);
    
    if ($result === null) {
        echo "✅ NO PATTERN MATCHED → Will fallback to Gemini AI\n\n";
    } else {
        echo "❌ Pattern matched!\n";
        echo "Type: {$result['type']}\n";
        echo "Response preview: " . substr($result['response'], 0, 80) . "...\n\n";
    }
}
