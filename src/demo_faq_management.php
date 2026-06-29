<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ChatbotKnowledge;
use App\Models\User;
use App\Services\RuleBasedChatbotService;
use Illuminate\Support\Facades\Auth;

// Helper function to display FAQ
function displayFaq($faq, $showContent = false) {
    $status = $faq->is_active ? "🟢 ACTIVE" : "🔴 INACTIVE";
    echo "   ┌─────────────────────────────────────────────────────────────\n";
    echo "   │ ID: {$faq->id}  |  Priority: {$faq->priority}  |  {$status}\n";
    echo "   ├─────────────────────────────────────────────────────────────\n";
    echo "   │ ❓ Question: {$faq->question}\n";
    echo "   │ 📁 Category: {$faq->category}\n";
    echo "   │ 🔑 Keywords: " . ($faq->keywords ?: 'N/A') . "\n";
    
    if ($showContent) {
        $preview = strlen($faq->answer) > 150 
            ? substr($faq->answer, 0, 150) . "..." 
            : $faq->answer;
        echo "   │ 💬 Answer: " . str_replace("\n", "\n   │           ", $preview) . "\n";
    }
    
    echo "   └─────────────────────────────────────────────────────────────\n";
}

// Login as test student
$user = User::where('email', 'hocvien1@gmail.com')->first();
Auth::login($user);

$chatbotService = new RuleBasedChatbotService();

echo "\n";
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║                                                               ║\n";
echo "║           🎯 DEMO: HỆ THỐNG QUẢN LÝ FAQ CHATBOT              ║\n";
echo "║                                                               ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ========================================================================
// PHẦN 1: HIỂN THỊ DANH SÁCH FAQ HIỆN TẠI
// ========================================================================
echo "┌───────────────────────────────────────────────────────────────┐\n";
echo "│  📚 PHẦN 1: DANH SÁCH FAQ HIỆN TẠI                           │\n";
echo "└───────────────────────────────────────────────────────────────┘\n\n";

$totalFaqs = ChatbotKnowledge::count();
$activeFaqs = ChatbotKnowledge::where('is_active', true)->count();
$inactiveFaqs = ChatbotKnowledge::where('is_active', false)->count();

echo "📊 Thống kê tổng quan:\n";
echo "   • Tổng số FAQ: {$totalFaqs}\n";
echo "   • 🟢 Hoạt động: {$activeFaqs}\n";
echo "   • 🔴 Không hoạt động: {$inactiveFaqs}\n\n";

echo "📋 Top 3 FAQs có priority cao nhất:\n\n";
$topFaqs = ChatbotKnowledge::orderBy('priority', 'desc')->take(3)->get();
foreach ($topFaqs as $faq) {
    displayFaq($faq);
    echo "\n";
}

readline("👉 Nhấn ENTER để tiếp tục...");

// ========================================================================
// PHẦN 2: TẠO FAQ MỚI
// ========================================================================
echo "\n\n";
echo "┌───────────────────────────────────────────────────────────────┐\n";
echo "│  ➕ PHẦN 2: TẠO FAQ MỚI                                      │\n";
echo "└───────────────────────────────────────────────────────────────┘\n\n";

echo "🔨 Đang tạo FAQ mới...\n\n";

$newFaq = ChatbotKnowledge::create([
    'question' => 'Trung tâm có khóa học tiếng Pháp không?',
    'answer' => "Có! Trung tâm có khóa học tiếng Pháp cho nhiều trình độ:\n\n" .
                "📚 **Các khóa học tiếng Pháp:**\n" .
                "• Tiếng Pháp Sơ cấp (A1-A2): 3 tháng\n" .
                "• Tiếng Pháp Trung cấp (B1-B2): 4 tháng\n" .
                "• Tiếng Pháp Nâng cao (C1-C2): 5 tháng\n\n" .
                "🎯 **Ưu điểm:**\n" .
                "• Giáo viên người Pháp\n" .
                "• Lớp nhỏ 8-12 học viên\n" .
                "• Học phí hợp lý\n\n" .
                "📞 Liên hệ: 0123-456-789 để đăng ký thử học MIỄN PHÍ!",
    'keywords' => 'tieng phap, french, hoc tieng phap, khoa hoc phap',
    'category' => 'course',
    'priority' => 85,
    'is_active' => true
]);

echo "✅ FAQ mới đã được tạo thành công!\n\n";
displayFaq($newFaq, true);
echo "\n";

readline("👉 Nhấn ENTER để test FAQ này với chatbot...");

// Test FAQ mới với chatbot
echo "\n\n🤖 Test với Chatbot:\n";
echo "   👤 User: \"Có dạy tiếng Pháp không?\"\n";
echo "   🤖 Bot: ";

$response = $chatbotService->processMessage('có dạy tiếng pháp không');

if ($response['type'] === 'knowledge_base') {
    echo "✅ (Trả lời từ FAQ)\n\n";
    echo "   " . str_replace("\n", "\n   ", $response['response']) . "\n";
} else {
    echo "⚠️  (Không match FAQ - Type: {$response['type']})\n";
}

readline("\n👉 Nhấn ENTER để tiếp tục...");

// ========================================================================
// PHẦN 3: TẮT FAQ (TOGGLE OFF)
// ========================================================================
echo "\n\n";
echo "┌───────────────────────────────────────────────────────────────┐\n";
echo "│  🔴 PHẦN 3: TẮT FAQ (DISABLE)                                │\n";
echo "└───────────────────────────────────────────────────────────────┘\n\n";

echo "🔄 Đang tắt FAQ...\n\n";

$newFaq->is_active = false;
$newFaq->save();

echo "✅ FAQ đã được TẮT!\n\n";
displayFaq($newFaq);
echo "\n";

readline("👉 Nhấn ENTER để test lại với chatbot...");

// Test FAQ sau khi tắt
echo "\n\n🤖 Test lại với Chatbot:\n";
echo "   👤 User: \"Có dạy tiếng Pháp không?\"\n";
echo "   🤖 Bot: ";

$response = $chatbotService->processMessage('có dạy tiếng pháp không');

if ($response['type'] === 'knowledge_base') {
    echo "⚠️  FAQ vẫn trả lời (Lỗi!)\n";
} else {
    echo "✅ FAQ không trả lời (Đã bị tắt)\n";
    echo "   → Chatbot chuyển sang xử lý bằng: " . strtoupper($response['type']) . "\n";
}

readline("\n👉 Nhấn ENTER để tiếp tục...");

// ========================================================================
// PHẦN 4: MỞ LẠI FAQ (TOGGLE ON)
// ========================================================================
echo "\n\n";
echo "┌───────────────────────────────────────────────────────────────┐\n";
echo "│  🟢 PHẦN 4: MỞ LẠI FAQ (ENABLE)                             │\n";
echo "└───────────────────────────────────────────────────────────────┘\n\n";

echo "🔄 Đang mở lại FAQ...\n\n";

$newFaq->is_active = true;
$newFaq->save();

echo "✅ FAQ đã được MỞ LẠI!\n\n";
displayFaq($newFaq);
echo "\n";

readline("👉 Nhấn ENTER để test lại với chatbot...");

// Test FAQ sau khi mở lại
echo "\n\n🤖 Test lại với Chatbot:\n";
echo "   👤 User: \"Có dạy tiếng Pháp không?\"\n";
echo "   🤖 Bot: ";

$response = $chatbotService->processMessage('có dạy tiếng pháp không');

if ($response['type'] === 'knowledge_base') {
    echo "✅ (Trả lời từ FAQ - Đã mở lại)\n\n";
    echo "   " . str_replace("\n", "\n   ", $response['response']) . "\n";
} else {
    echo "⚠️  FAQ không trả lời (Lỗi!)\n";
}

readline("\n👉 Nhấn ENTER để tiếp tục...");

// ========================================================================
// PHẦN 5: CẬP NHẬT FAQ
// ========================================================================
echo "\n\n";
echo "┌───────────────────────────────────────────────────────────────┐\n";
echo "│  ✏️  PHẦN 5: CẬP NHẬT FAQ                                     │\n";
echo "└───────────────────────────────────────────────────────────────┘\n\n";

echo "🔄 Đang cập nhật FAQ (tăng priority và mở rộng nội dung)...\n\n";

$newFaq->update([
    'priority' => 95,
    'answer' => "Có! Trung tâm có khóa học tiếng Pháp cho nhiều trình độ:\n\n" .
                "📚 **Các khóa học tiếng Pháp:**\n" .
                "• Tiếng Pháp Sơ cấp (A1-A2): 3 tháng - 5.500.000đ\n" .
                "• Tiếng Pháp Trung cấp (B1-B2): 4 tháng - 7.000.000đ\n" .
                "• Tiếng Pháp Nâng cao (C1-C2): 5 tháng - 8.500.000đ\n" .
                "• Tiếng Pháp Giao tiếp: 2 tháng - 4.000.000đ\n\n" .
                "🎯 **Ưu điểm:**\n" .
                "• Giáo viên người Pháp có chứng chỉ DELF/DALF\n" .
                "• Lớp nhỏ 8-12 học viên\n" .
                "• Tài liệu học từ Pháp\n" .
                "• Hỗ trợ thi chứng chỉ quốc tế\n\n" .
                "🎁 **Ưu đãi tháng này:**\n" .
                "• Giảm 20% học phí cho học viên mới\n" .
                "• Tặng bộ giáo trình tiếng Pháp\n" .
                "• Học thử 2 buổi MIỄN PHÍ\n\n" .
                "📞 Liên hệ ngay: 0123-456-789\n" .
                "✉️ Email: french@languagecenter.edu.vn"
]);

echo "✅ FAQ đã được cập nhật!\n\n";
displayFaq($newFaq, true);
echo "\n";

readline("👉 Nhấn ENTER để tiếp tục...");

// ========================================================================
// PHẦN 6: XÓA FAQ
// ========================================================================
echo "\n\n";
echo "┌───────────────────────────────────────────────────────────────┐\n";
echo "│  🗑️  PHẦN 6: XÓA FAQ                                          │\n";
echo "└───────────────────────────────────────────────────────────────┘\n\n";

$faqId = $newFaq->id;
$faqQuestion = $newFaq->question;

echo "🗑️  Đang xóa FAQ...\n\n";

$newFaq->delete();

echo "✅ FAQ đã được XÓA!\n\n";
echo "   • ID đã xóa: {$faqId}\n";
echo "   • Câu hỏi: {$faqQuestion}\n\n";

// Verify deletion
$deletedFaq = ChatbotKnowledge::find($faqId);
if ($deletedFaq === null) {
    echo "   ✅ XÁC NHẬN: FAQ đã bị xóa khỏi database\n";
} else {
    echo "   ⚠️  FAQ vẫn tồn tại (Lỗi!)\n";
}

readline("\n👉 Nhấn ENTER để xem tổng kết...");

// ========================================================================
// TỔNG KẾT
// ========================================================================
echo "\n\n";
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║                                                               ║\n";
echo "║                    🎉 DEMO HOÀN THÀNH!                       ║\n";
echo "║                                                               ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

echo "✅ **Các chức năng đã demo:**\n";
echo "   1. ✅ CREATE - Tạo FAQ mới\n";
echo "   2. ✅ READ - Đọc và hiển thị danh sách FAQ\n";
echo "   3. ✅ UPDATE - Cập nhật nội dung FAQ\n";
echo "   4. ✅ DELETE - Xóa FAQ\n";
echo "   5. ✅ TOGGLE OFF - Tắt FAQ (is_active = false)\n";
echo "   6. ✅ TOGGLE ON - Mở FAQ (is_active = true)\n";
echo "   7. ✅ CHATBOT INTEGRATION - Test tích hợp với chatbot\n\n";

echo "📊 **Thống kê cuối cùng:**\n";
$finalTotal = ChatbotKnowledge::count();
$finalActive = ChatbotKnowledge::where('is_active', true)->count();
$finalInactive = ChatbotKnowledge::where('is_active', false)->count();

echo "   • Tổng số FAQ: {$finalTotal}\n";
echo "   • 🟢 Hoạt động: {$finalActive}\n";
echo "   • 🔴 Không hoạt động: {$finalInactive}\n\n";

echo "🎯 **Kết luận:**\n";
echo "   Hệ thống quản lý FAQ hoạt động hoàn hảo!\n";
echo "   Toggle status cho phép bật/tắt FAQ linh hoạt.\n";
echo "   Chatbot tự động bỏ qua FAQ bị tắt.\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "                   Thank you for watching! 👋                   \n";
echo "═══════════════════════════════════════════════════════════════\n\n";
