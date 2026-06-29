<?php

/**
 * Comprehensive FAQ Testing Script
 * Tests:
 * 1. FAQ CRUD operations (Create, Read, Update, Delete)
 * 2. Chatbot responses for all FAQ entries
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChatbotKnowledge;
use App\Models\User;
use App\Services\RuleBasedChatbotService;
use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "FAQ COMPLETE TESTING SCRIPT\n";
echo "===========================================\n\n";

// ============================================================
// TEST 1: FAQ CRUD OPERATIONS
// ============================================================
echo "TEST 1: FAQ CRUD OPERATIONS\n";
echo "-------------------------------------------\n";

// TEST 1.1: CREATE - Thêm mới FAQ
echo "\n[1.1] Testing CREATE - Thêm mới FAQ...\n";
try {
    $newFAQ = ChatbotKnowledge::create([
        'category' => 'policy',
        'question' => 'Tôi có thể hoàn tiền không nếu tôi muốn hủy khóa học?',
        'answer' => 'Bạn có thể hoàn lại 70% học phí nếu hủy trước khi khóa học bắt đầu 7 ngày. Sau thời gian này, học phí sẽ không được hoàn lại.',
        'keywords' => 'hoàn tiền, hủy khóa học, chính sách hoàn tiền, refund',
        'priority' => 80,
        'is_active' => true,
    ]);
    
    echo "✅ CREATE SUCCESS: FAQ ID #{$newFAQ->id} - '{$newFAQ->question}'\n";
    echo "   Category: {$newFAQ->category}\n";
    echo "   Priority: {$newFAQ->priority}\n";
    echo "   Is Active: " . ($newFAQ->is_active ? 'Yes' : 'No') . "\n";
    
} catch (\Exception $e) {
    echo "❌ CREATE FAILED: " . $e->getMessage() . "\n";
    exit(1);
}

// TEST 1.2: READ - Đọc FAQ vừa tạo
echo "\n[1.2] Testing READ - Đọc FAQ vừa tạo...\n";
$readFAQ = ChatbotKnowledge::find($newFAQ->id);
if ($readFAQ && $readFAQ->question === $newFAQ->question) {
    echo "✅ READ SUCCESS: Tìm thấy FAQ ID #{$readFAQ->id}\n";
    echo "   Question: {$readFAQ->question}\n";
    echo "   Answer: " . substr($readFAQ->answer, 0, 50) . "...\n";
} else {
    echo "❌ READ FAILED: Không tìm thấy FAQ vừa tạo\n";
    exit(1);
}

// TEST 1.3: UPDATE - Cập nhật FAQ
echo "\n[1.3] Testing UPDATE - Cập nhật FAQ...\n";
try {
    $newFAQ->question = 'Chính sách hoàn tiền của trung tâm như thế nào?';
    $newFAQ->priority = 90;
    $newFAQ->save();
    
    $updatedFAQ = ChatbotKnowledge::find($newFAQ->id);
    if ($updatedFAQ->question === 'Chính sách hoàn tiền của trung tâm như thế nào?' && $updatedFAQ->priority === 90) {
        echo "✅ UPDATE SUCCESS: FAQ ID #{$updatedFAQ->id} đã được cập nhật\n";
        echo "   New Question: {$updatedFAQ->question}\n";
        echo "   New Priority: {$updatedFAQ->priority}\n";
    } else {
        echo "❌ UPDATE FAILED: Dữ liệu không được cập nhật đúng\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "❌ UPDATE FAILED: " . $e->getMessage() . "\n";
    exit(1);
}

// TEST 1.4: DELETE - Xóa FAQ
echo "\n[1.4] Testing DELETE - Xóa FAQ...\n";
try {
    $deletedId = $newFAQ->id;
    $newFAQ->delete();
    
    $checkDeleted = ChatbotKnowledge::find($deletedId);
    if (!$checkDeleted) {
        echo "✅ DELETE SUCCESS: FAQ ID #{$deletedId} đã được xóa\n";
    } else {
        echo "❌ DELETE FAILED: FAQ vẫn tồn tại trong database\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "❌ DELETE FAILED: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ ALL CRUD TESTS PASSED!\n";

// ============================================================
// TEST 2: CHATBOT RESPONSES FOR ALL FAQ ENTRIES
// ============================================================
echo "\n\n===========================================\n";
echo "TEST 2: CHATBOT FAQ RESPONSES\n";
echo "===========================================\n";

// Get test student
$student = User::where('role', 'student')->first();
if (!$student || !$student->student) {
    echo "❌ ERROR: No test student found\n";
    exit(1);
}

$chatbotService = new RuleBasedChatbotService();

// Get all active FAQs
$allFAQs = ChatbotKnowledge::where('is_active', true)
    ->orderBy('category')
    ->orderBy('priority', 'desc')
    ->get();

echo "\nFound " . $allFAQs->count() . " active FAQ entries\n";
echo "-------------------------------------------\n";

$testResults = [
    'total' => 0,
    'success' => 0,
    'failed' => 0,
    'details' => []
];

foreach ($allFAQs as $faq) {
    $testResults['total']++;
    
    echo "\n[TEST #{$testResults['total']}] Category: {$faq->category} | Priority: {$faq->priority}\n";
    echo "Question: {$faq->question}\n";
    
    // Test với keyword đầu tiên
    $keywords = explode(',', $faq->keywords);
    $testKeyword = trim($keywords[0]);
    
    echo "Testing with keyword: '{$testKeyword}'\n";
    
    try {
        $response = $chatbotService->processMessage($student->id, $testKeyword);
        
        // Check if response is from FAQ/Knowledge Base layer (accept both 'faq' and 'knowledge_base')
        if (isset($response['type']) && in_array($response['type'], ['faq', 'knowledge_base'])) {
            echo "✅ SUCCESS: Chatbot returned FAQ/Knowledge Base response\n";
            echo "   Response type: {$response['type']}\n";
            echo "   Response preview: " . substr($response['response'], 0, 100) . "...\n";
            
            // Verify response contains FAQ question or answer
            if (strpos($response['response'], $faq->question) !== false || 
                strpos($response['response'], $faq->answer) !== false) {
                echo "   ✓ Response contains correct FAQ content\n";
            }
            
            $testResults['success']++;
            $testResults['details'][] = [
                'faq_id' => $faq->id,
                'question' => $faq->question,
                'test_keyword' => $testKeyword,
                'status' => 'SUCCESS',
                'response_type' => $response['type']
            ];
        } else {
            echo "⚠️ WARNING: Chatbot did not return FAQ response\n";
            echo "   Response type: " . ($response['type'] ?? 'unknown') . "\n";
            echo "   Response: " . substr($response['response'], 0, 100) . "...\n";
            
            $testResults['failed']++;
            $testResults['details'][] = [
                'faq_id' => $faq->id,
                'question' => $faq->question,
                'test_keyword' => $testKeyword,
                'status' => 'FAILED',
                'reason' => 'Response type is not FAQ/Knowledge Base: ' . ($response['type'] ?? 'unknown')
            ];
        }
        
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
        $testResults['failed']++;
        $testResults['details'][] = [
            'faq_id' => $faq->id,
            'question' => $faq->question,
            'test_keyword' => $testKeyword,
            'status' => 'ERROR',
            'reason' => $e->getMessage()
        ];
    }
    
    echo "-------------------------------------------\n";
}

// ============================================================
// TEST SUMMARY
// ============================================================
echo "\n\n===========================================\n";
echo "TEST SUMMARY\n";
echo "===========================================\n";

echo "\n📊 CRUD OPERATIONS: ✅ ALL PASSED (4/4)\n";
echo "   - CREATE: ✅\n";
echo "   - READ: ✅\n";
echo "   - UPDATE: ✅\n";
echo "   - DELETE: ✅\n";

echo "\n📊 CHATBOT FAQ RESPONSES:\n";
echo "   - Total FAQs Tested: {$testResults['total']}\n";
echo "   - Success: {$testResults['success']}\n";
echo "   - Failed: {$testResults['failed']}\n";

if ($testResults['failed'] > 0) {
    echo "\n⚠️ FAILED TESTS:\n";
    foreach ($testResults['details'] as $detail) {
        if ($detail['status'] !== 'SUCCESS') {
            echo "   - FAQ ID #{$detail['faq_id']}: {$detail['question']}\n";
            echo "     Keyword: '{$detail['test_keyword']}'\n";
            echo "     Reason: {$detail['reason']}\n";
        }
    }
}

$successRate = $testResults['total'] > 0 ? round(($testResults['success'] / $testResults['total']) * 100, 2) : 0;
echo "\n✨ SUCCESS RATE: {$successRate}%\n";

if ($testResults['failed'] === 0) {
    echo "\n🎉 ALL TESTS PASSED! FAQ system is working perfectly!\n";
} else {
    echo "\n⚠️ Some tests failed. Please review the failed cases above.\n";
}

echo "\n===========================================\n";
echo "TEST COMPLETED\n";
echo "===========================================\n";
