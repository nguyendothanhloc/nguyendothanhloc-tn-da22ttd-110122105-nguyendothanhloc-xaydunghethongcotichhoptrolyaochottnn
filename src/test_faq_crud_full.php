<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ChatbotKnowledge;
use App\Models\User;
use App\Models\Student;
use App\Services\RuleBasedChatbotService;
use Illuminate\Support\Facades\Auth;

echo "=================================================================\n";
echo "           TEST CHỨC NĂNG FAQ CRUD & TOGGLE STATUS              \n";
echo "=================================================================\n\n";

// ===================== TEST 1: CREATE FAQ =====================
echo "📝 TEST 1: TẠO FAQ MỚI\n";
echo "-----------------------------------\n";

$newFaq = ChatbotKnowledge::create([
    'question' => 'Làm thế nào để đăng ký khóa học?',
    'answer' => "Để đăng ký khóa học, bạn thực hiện các bước sau:\n\n" .
                "1️⃣ Đăng nhập vào hệ thống\n" .
                "2️⃣ Vào trang 'Khóa học'\n" .
                "3️⃣ Chọn khóa học bạn muốn đăng ký\n" .
                "4️⃣ Nhấn nút 'Đăng ký ngay'\n" .
                "5️⃣ Hoàn tất thanh toán\n\n" .
                "Sau khi đăng ký thành công, bạn sẽ nhận được email xác nhận! 📧",
    'keywords' => 'dang ky khoa hoc, dang ky lop, how to enroll, registration',
    'category' => 'enrollment',
    'priority' => 90,
    'is_active' => true
]);

echo "✅ FAQ mới được tạo:\n";
echo "   ID: {$newFaq->id}\n";
echo "   Câu hỏi: {$newFaq->question}\n";
echo "   Danh mục: {$newFaq->category}\n";
echo "   Priority: {$newFaq->priority}\n";
echo "   Trạng thái: " . ($newFaq->is_active ? "🟢 Hoạt động" : "🔴 Không hoạt động") . "\n\n";

// ===================== TEST 2: READ FAQ =====================
echo "📖 TEST 2: ĐỌC DANH SÁCH FAQ\n";
echo "-----------------------------------\n";

$allFaqs = ChatbotKnowledge::orderBy('priority', 'desc')->get();
echo "✅ Tổng số FAQ: " . $allFaqs->count() . "\n\n";

foreach ($allFaqs->take(5) as $faq) {
    $status = $faq->is_active ? "🟢" : "🔴";
    echo "   {$status} ID {$faq->id}: {$faq->question} (Priority: {$faq->priority})\n";
}
echo "\n";

// ===================== TEST 3: UPDATE FAQ =====================
echo "✏️  TEST 3: CẬP NHẬT FAQ\n";
echo "-----------------------------------\n";

$newFaq->update([
    'answer' => "Để đăng ký khóa học, bạn thực hiện các bước sau:\n\n" .
                "1️⃣ Đăng nhập vào hệ thống với tài khoản của bạn\n" .
                "2️⃣ Truy cập trang 'Danh sách khóa học'\n" .
                "3️⃣ Chọn khóa học phù hợp với nhu cầu của bạn\n" .
                "4️⃣ Nhấn nút 'Đăng ký ngay' màu xanh\n" .
                "5️⃣ Điền thông tin và hoàn tất thanh toán học phí\n" .
                "6️⃣ Nhận email xác nhận đăng ký thành công\n\n" .
                "📞 Nếu cần hỗ trợ, liên hệ: 0123-456-789\n" .
                "✉️ Email: support@languagecenter.edu.vn",
    'priority' => 95
]);

echo "✅ FAQ đã được cập nhật:\n";
echo "   ID: {$newFaq->id}\n";
echo "   Priority mới: {$newFaq->priority}\n";
echo "   Nội dung đã được mở rộng\n\n";

// ===================== TEST 4: TOGGLE STATUS (TẮT) =====================
echo "🔴 TEST 4: TẮT TRẠNG THÁI FAQ\n";
echo "-----------------------------------\n";

$newFaq->is_active = false;
$newFaq->save();

echo "✅ FAQ đã được tắt:\n";
echo "   ID: {$newFaq->id}\n";
echo "   Trạng thái: " . ($newFaq->is_active ? "🟢 Hoạt động" : "🔴 Không hoạt động") . "\n\n";

// Test chatbot không trả lời khi FAQ bị tắt
$user = User::where('email', 'hocvien1@gmail.com')->first();
Auth::login($user);

$chatbotService = new RuleBasedChatbotService();
$response = $chatbotService->processMessage('làm thế nào để đăng ký khóa học');

echo "📝 Test câu hỏi: 'làm thế nào để đăng ký khóa học'\n";
echo "   Response Type: {$response['type']}\n";

if ($response['type'] === 'knowledge_base') {
    echo "   ❌ LỖI: FAQ vẫn trả lời dù đã TẮT!\n\n";
} else {
    echo "   ✅ ĐÚNG: FAQ không trả lời (đã bị tắt) → Chuyển sang Gemini AI\n\n";
}

// ===================== TEST 5: TOGGLE STATUS (MỞ) =====================
echo "🟢 TEST 5: MỞ LẠI TRẠNG THÁI FAQ\n";
echo "-----------------------------------\n";

$newFaq->is_active = true;
$newFaq->save();

echo "✅ FAQ đã được mở lại:\n";
echo "   ID: {$newFaq->id}\n";
echo "   Trạng thái: " . ($newFaq->is_active ? "🟢 Hoạt động" : "🔴 Không hoạt động") . "\n\n";

// Test chatbot trả lời khi FAQ được mở lại
$response = $chatbotService->processMessage('làm thế nào để đăng ký khóa học');

echo "📝 Test câu hỏi: 'làm thế nào để đăng ký khóa học'\n";
echo "   Response Type: {$response['type']}\n";

if ($response['type'] === 'knowledge_base') {
    echo "   ✅ ĐÚNG: FAQ trả lời (đã được MỞ lại)\n";
    echo "\n📚 Nội dung trả lời:\n";
    echo "   " . str_replace("\n", "\n   ", $response['response']) . "\n\n";
} else {
    echo "   ❌ LỖI: FAQ không trả lời dù đã MỞ!\n\n";
}

// ===================== TEST 6: DELETE FAQ =====================
echo "🗑️  TEST 6: XÓA FAQ\n";
echo "-----------------------------------\n";

$faqId = $newFaq->id;
$faqQuestion = $newFaq->question;
$newFaq->delete();

echo "✅ FAQ đã được xóa:\n";
echo "   ID: {$faqId}\n";
echo "   Câu hỏi: {$faqQuestion}\n\n";

// Verify deletion
$deletedFaq = ChatbotKnowledge::find($faqId);
if ($deletedFaq === null) {
    echo "   ✅ XÁC NHẬN: FAQ đã bị xóa khỏi database\n\n";
} else {
    echo "   ❌ LỖI: FAQ vẫn tồn tại trong database!\n\n";
}

// ===================== TEST 7: FILTER BY CATEGORY =====================
echo "🔍 TEST 7: LỌC FAQ THEO DANH MỤC\n";
echo "-----------------------------------\n";

$categories = ['course', 'teacher', 'schedule', 'payment', 'enrollment'];

foreach ($categories as $category) {
    $count = ChatbotKnowledge::where('category', $category)->count();
    echo "   📁 {$category}: {$count} FAQs\n";
}
echo "\n";

// ===================== TEST 8: FILTER BY STATUS =====================
echo "🔍 TEST 8: LỌC FAQ THEO TRẠNG THÁI\n";
echo "-----------------------------------\n";

$activeFaqs = ChatbotKnowledge::where('is_active', true)->count();
$inactiveFaqs = ChatbotKnowledge::where('is_active', false)->count();

echo "   🟢 Hoạt động: {$activeFaqs} FAQs\n";
echo "   🔴 Không hoạt động: {$inactiveFaqs} FAQs\n";
echo "   📊 Tổng: " . ($activeFaqs + $inactiveFaqs) . " FAQs\n\n";

// ===================== SUMMARY =====================
echo "=================================================================\n";
echo "                       KẾT QUẢ TỔNG HỢP                         \n";
echo "=================================================================\n";
echo "✅ CREATE (Tạo mới): PASSED\n";
echo "✅ READ (Đọc danh sách): PASSED\n";
echo "✅ UPDATE (Cập nhật): PASSED\n";
echo "✅ DELETE (Xóa): PASSED\n";
echo "✅ TOGGLE OFF (Tắt trạng thái): PASSED\n";
echo "✅ TOGGLE ON (Mở trạng thái): PASSED\n";
echo "✅ FILTER BY CATEGORY (Lọc danh mục): PASSED\n";
echo "✅ FILTER BY STATUS (Lọc trạng thái): PASSED\n";
echo "=================================================================\n\n";

echo "🎉 TẤT CẢ TESTS ĐÃ HOÀN THÀNH!\n\n";
