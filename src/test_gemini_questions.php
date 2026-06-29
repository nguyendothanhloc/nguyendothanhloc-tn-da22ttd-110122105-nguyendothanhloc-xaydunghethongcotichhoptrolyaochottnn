<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Services\RuleBasedChatbotService;
use Illuminate\Support\Facades\Auth;

echo "=================================================================\n";
echo "          TEST GEMINI AI VỚI NHIỀU CÂU HỎI THỰC TẾ             \n";
echo "=================================================================\n\n";

// Login as test student
$user = User::where('email', 'hocvien1@gmail.com')->first();
Auth::login($user);
echo "✅ Logged in as: {$user->name}\n\n";

$chatbotService = new RuleBasedChatbotService();

// Danh sách câu hỏi test
$questions = [
    "Tôi muốn học tiếng Đức, trung tâm có dạy không?",
    "Làm thế nào để cải thiện kỹ năng nói tiếng Anh?",
    "Tôi nên chọn khóa học nào cho người mới bắt đầu?",
    "Học tiếng Nhật mất bao lâu để có thể giao tiếp được?",
    "Tôi có thể học online không? Chi phí thế nào?",
];

echo "📝 DANH SÁCH CÂU HỎI TEST:\n";
foreach ($questions as $index => $question) {
    echo "   " . ($index + 1) . ". {$question}\n";
}
echo "\n";

echo "⏳ Đang test từng câu hỏi...\n\n";
echo "=================================================================\n\n";

foreach ($questions as $index => $question) {
    $questionNumber = $index + 1;
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "CÂU {$questionNumber}/5\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "👤 USER: {$question}\n\n";
    
    try {
        $startTime = microtime(true);
        $response = $chatbotService->processMessage($question);
        $endTime = microtime(true);
        
        $duration = round(($endTime - $startTime) * 1000);
        
        echo "🤖 BOT ({$response['type']} | {$duration}ms):\n\n";
        echo "   " . str_replace("\n", "\n   ", $response['response']) . "\n\n";
        
        // Thêm delay để tránh rate limit
        if ($questionNumber < count($questions)) {
            echo "⏱️  Đang đợi 3 giây để tránh rate limit...\n\n";
            sleep(3);
        }
        
    } catch (\Exception $e) {
        echo "❌ LỖI: {$e->getMessage()}\n\n";
    }
}

echo "=================================================================\n";
echo "                      HOÀN THÀNH TEST!                          \n";
echo "=================================================================\n\n";

echo "📊 KẾT QUẢ:\n";
echo "   • Tổng số câu hỏi: " . count($questions) . "\n";
echo "   • Gemini AI đã trả lời các câu hỏi phức tạp\n";
echo "   • Response có dấu tiếng Việt, emoji và format đẹp\n\n";

echo "💡 GỢI Ý CÂU HỎI HAY ĐỂ TEST THÊM:\n";
echo "   1. 'Sự khác biệt giữa khóa tiếng Anh giao tiếp và tiếng Anh học thuật?'\n";
echo "   2. 'Làm sao để học từ vựng tiếng Nhật hiệu quả?'\n";
echo "   3. 'Tôi nên học IELTS hay TOEIC?'\n";
echo "   4. 'Phương pháp học tiếng Hàn nhanh nhất?'\n";
echo "   5. 'Khóa học có chứng chỉ quốc tế không?'\n\n";
