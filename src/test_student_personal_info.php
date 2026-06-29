<?php

/**
 * Test Student Personal Information Pattern
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\RuleBasedChatbotService;
use Illuminate\Support\Facades\Auth;

echo "===========================================\n";
echo "STUDENT PERSONAL INFO PATTERN TEST\n";
echo "===========================================\n\n";

// Get test student
$student = User::where('role', 'student')->where('email', 'hocvien1@gmail.com')->first();

if (!$student || !$student->student) {
    echo "❌ ERROR: No test student found (hocvien1@gmail.com)\n";
    exit(1);
}

// Manually set auth user for testing
Auth::login($student);

echo "Test Student: {$student->name} ({$student->email})\n";
echo "-------------------------------------------\n\n";

$chatbotService = new RuleBasedChatbotService();

// Test queries
$testQueries = [
    'Thông tin của tôi',
    'Tên tôi là gì?',
    'Email của tôi',
    'Số điện thoại của tôi',
    'Địa chỉ của tôi',
    'Ngày sinh của tôi',
    'my info',
    'my email',
    'my phone',
];

echo "TESTING PERSONAL INFO QUERIES\n";
echo "===========================================\n\n";

$passed = 0;
$failed = 0;

foreach ($testQueries as $index => $query) {
    $testNumber = $index + 1;
    echo "[TEST #{$testNumber}] Query: \"{$query}\"\n";
    echo "-------------------------------------------\n";
    
    try {
        $response = $chatbotService->processMessage($query);
        
        // Check if response type is 'student_info'
        if (isset($response['type']) && $response['type'] === 'student_info') {
            echo "✅ SUCCESS: Pattern matched (type: student_info)\n";
            echo "Response:\n";
            echo $response['response'] . "\n";
            
            // Check if response contains student data
            if (isset($response['data']) && !empty($response['data'])) {
                echo "\nData returned:\n";
                echo "  - Name: " . ($response['data']['name'] ?? 'N/A') . "\n";
                echo "  - Email: " . ($response['data']['email'] ?? 'N/A') . "\n";
                echo "  - Phone: " . ($response['data']['phone'] ?? 'N/A') . "\n";
                echo "  - Address: " . ($response['data']['address'] ?? 'N/A') . "\n";
            }
            
            $passed++;
        } else {
            echo "⚠️ WARNING: Pattern did not match student_info\n";
            echo "Response type: " . ($response['type'] ?? 'unknown') . "\n";
            echo "Response preview: " . substr($response['response'], 0, 100) . "...\n";
            $failed++;
        }
        
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
        $failed++;
    }
    
    echo "\n";
}

echo "===========================================\n";
echo "TEST SUMMARY\n";
echo "===========================================\n";
echo "Total queries: " . count($testQueries) . "\n";
echo "Passed: {$passed}\n";
echo "Failed: {$failed}\n";

$successRate = count($testQueries) > 0 ? round(($passed / count($testQueries)) * 100, 2) : 0;
echo "Success Rate: {$successRate}%\n";

if ($failed === 0) {
    echo "\n🎉 ALL TESTS PASSED!\n";
} else {
    echo "\n⚠️ Some tests failed.\n";
}

echo "\n===========================================\n";
