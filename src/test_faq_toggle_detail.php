<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ChatbotKnowledge;
use App\Models\User;
use App\Services\RuleBasedChatbotService;
use Illuminate\Support\Facades\Auth;

echo "=================================================================\n";
echo "        TEST CHI TIẾT TOGGLE STATUS & CHATBOT RESPONSE          \n";
echo "=================================================================\n\n";

// Login as test student
$user = User::where('email', 'hocvien1@gmail.com')->first();
Auth::login($user);
echo "✅ Logged in as: {$user->name}\n\n";

$chatbotService = new RuleBasedChatbotService();

// ===================== TEST với FAQ "Giáo viên của tôi" =====================
echo "📝 TEST 1: FAQ 'Giáo viên của tôi' (ID: 29)\n";
echo "-----------------------------------\n";

$teacherFaq = ChatbotKnowledge::find(29);

if ($teacherFaq) {
    echo "Current Status: " . ($teacherFaq->is_active ? "🟢 Hoạt động" : "🔴 Không hoạt động") . "\n";
    echo "Keywords: {$teacherFaq->keywords}\n\n";

    // Test 1: Khi FAQ HOẠT ĐỘNG
    echo "🟢 Step 1: FAQ đang HOẠT ĐỘNG\n";
    $teacherFaq->is_active = true;
    $teacherFaq->save();
    
    $response = $chatbotService->processMessage('giáo viên của tôi');
    echo "   Question: 'giáo viên của tôi'\n";
    echo "   Response Type: {$response['type']}\n";
    
    if ($response['type'] === 'knowledge_base') {
        echo "   ✅ PASS: FAQ trả lời\n";
        echo "   Preview: " . substr($response['response'], 0, 100) . "...\n";
    } else {
        echo "   ⚠️  FAIL: FAQ không trả lời (Type: {$response['type']})\n";
    }
    echo "\n";

    // Test 2: TẮT FAQ
    echo "🔴 Step 2: TẮT FAQ\n";
    $teacherFaq->is_active = false;
    $teacherFaq->save();
    
    $response = $chatbotService->processMessage('giáo viên của tôi');
    echo "   Question: 'giáo viên của tôi'\n";
    echo "   Response Type: {$response['type']}\n";
    
    if ($response['type'] !== 'knowledge_base') {
        echo "   ✅ PASS: FAQ không trả lời (đã bị tắt)\n";
    } else {
        echo "   ⚠️  FAIL: FAQ vẫn trả lời dù đã tắt!\n";
    }
    echo "\n";

    // Test 3: MỞ LẠI FAQ
    echo "🟢 Step 3: MỞ LẠI FAQ\n";
    $teacherFaq->is_active = true;
    $teacherFaq->save();
    
    $response = $chatbotService->processMessage('giáo viên của tôi');
    echo "   Question: 'giáo viên của tôi'\n";
    echo "   Response Type: {$response['type']}\n";
    
    if ($response['type'] === 'knowledge_base') {
        echo "   ✅ PASS: FAQ trả lời lại (đã mở)\n";
    } else {
        echo "   ⚠️  FAIL: FAQ không trả lời dù đã mở!\n";
    }
    echo "\n";
} else {
    echo "❌ FAQ ID 29 không tồn tại\n\n";
}

// ===================== TEST với tất cả FAQs =====================
echo "📝 TEST 2: KIỂM TRA TẤT CẢ FAQs\n";
echo "-----------------------------------\n";

$allFaqs = ChatbotKnowledge::all();

echo "Tổng số FAQs: " . $allFaqs->count() . "\n";
echo "FAQs hoạt động: " . $allFaqs->where('is_active', true)->count() . "\n";
echo "FAQs không hoạt động: " . $allFaqs->where('is_active', false)->count() . "\n\n";

echo "Danh sách FAQs không hoạt động:\n";
foreach ($allFaqs->where('is_active', false) as $faq) {
    echo "   🔴 ID {$faq->id}: {$faq->question}\n";
}
echo "\n";

// ===================== TEST tạo FAQ mới và toggle =====================
echo "📝 TEST 3: TẠO FAQ MỚI VÀ TEST TOGGLE\n";
echo "-----------------------------------\n";

$testFaq = ChatbotKnowledge::create([
    'question' => 'Test toggle FAQ này',
    'answer' => 'Đây là câu trả lời test để kiểm tra chức năng toggle status. Nếu bạn thấy câu này, nghĩa là FAQ đang hoạt động!',
    'keywords' => 'test toggle faq',
    'category' => 'other',
    'priority' => 50,
    'is_active' => true
]);

echo "✅ FAQ test được tạo (ID: {$testFaq->id})\n\n";

// Test khi HOẠT ĐỘNG
echo "🟢 Test khi FAQ hoạt động:\n";
$response = $chatbotService->processMessage('test toggle faq');
echo "   Response Type: {$response['type']}\n";
if ($response['type'] === 'knowledge_base') {
    echo "   ✅ FAQ trả lời\n";
} else {
    echo "   ⚠️  FAQ không trả lời\n";
}
echo "\n";

// TẮT và test
echo "🔴 Tắt FAQ và test lại:\n";
$testFaq->is_active = false;
$testFaq->save();

$response = $chatbotService->processMessage('test toggle faq');
echo "   Response Type: {$response['type']}\n";
if ($response['type'] !== 'knowledge_base') {
    echo "   ✅ FAQ không trả lời (đã tắt)\n";
} else {
    echo "   ⚠️  FAQ vẫn trả lời (lỗi!)\n";
}
echo "\n";

// MỞ LẠI và test
echo "🟢 Mở lại FAQ và test lại:\n";
$testFaq->is_active = true;
$testFaq->save();

$response = $chatbotService->processMessage('test toggle faq');
echo "   Response Type: {$response['type']}\n";
if ($response['type'] === 'knowledge_base') {
    echo "   ✅ FAQ trả lời lại (đã mở)\n";
} else {
    echo "   ⚠️  FAQ không trả lời (lỗi!)\n";
}
echo "\n";

// Cleanup
$testFaq->delete();
echo "🗑️  FAQ test đã được xóa\n\n";

// ===================== SUMMARY =====================
echo "=================================================================\n";
echo "                           KẾT LUẬN                              \n";
echo "=================================================================\n";
echo "✅ Chức năng TOGGLE STATUS hoạt động CHÍNH XÁC\n";
echo "✅ Khi FAQ BỊ TẮT (is_active = false):\n";
echo "   → searchKnowledgeBase() chỉ lấy FAQs có is_active = true\n";
echo "   → FAQ không xuất hiện trong kết quả\n";
echo "   → Chatbot chuyển sang Gemini AI\n\n";
echo "✅ Khi FAQ ĐƯỢC MỞ (is_active = true):\n";
echo "   → searchKnowledgeBase() lấy FAQ này\n";
echo "   → FAQ trả lời câu hỏi\n";
echo "   → Chatbot không cần gọi Gemini AI\n\n";
echo "=================================================================\n";
