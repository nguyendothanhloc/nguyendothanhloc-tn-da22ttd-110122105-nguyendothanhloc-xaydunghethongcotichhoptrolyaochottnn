<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Services\RuleBasedChatbotService;
use Illuminate\Support\Facades\Auth;

// Login as test student
$user = User::where('email', 'hocvien1@gmail.com')->first();

if (!$user) {
    echo "❌ User not found\n";
    exit(1);
}

Auth::login($user);
echo "✅ Logged in as: {$user->name}\n\n";

// Test the question
$chatbotService = new RuleBasedChatbotService();

echo "=== TEST: 'giáo viên của tôi' ===\n\n";

$response = $chatbotService->processMessage('giáo viên của tôi');

echo "Response Type: {$response['type']}\n\n";
echo "Response:\n";
echo "---\n";
echo $response['response'];
echo "\n---\n";
