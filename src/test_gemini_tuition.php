<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== KIỂM TRA GEMINI VỀ HỌC PHÍ CÁC LỚP VÀ KHÓA HỌC ===\n\n";

// Lấy thông tin user test
$user = DB::table('users')->where('email', 'hocvien1@gmail.com')->first();
if (!$user) {
    echo "❌ Không tìm thấy user hocvien1@gmail.com\n";
    exit(1);
}

echo "✅ User: {$user->name} (ID: {$user->id})\n\n";

// Lấy thông tin student
$student = DB::table('students')->where('user_id', $user->id)->first();
if (!$student) {
    echo "❌ Không tìm thấy student record\n";
    exit(1);
}

echo "✅ Student ID: {$student->id}\n\n";

// Lấy thông tin enrollments với course và class
$enrollments = DB::table('enrollments')
    ->join('classes', 'enrollments.class_id', '=', 'classes.id')
    ->join('courses', 'classes.course_id', '=', 'courses.id')
    ->where('enrollments.student_id', $student->id)
    ->select(
        'courses.name as course_name',
        'courses.price as course_price',
        'courses.language',
        'courses.level',
        'classes.id as class_id',
        'classes.name as class_name',
        'enrollments.status as enrollment_status'
    )
    ->get();

echo "=== THÔNG TIN KHÓA HỌC ĐÃ ĐĂNG KÝ ===\n";
foreach ($enrollments as $enrollment) {
    echo "- {$enrollment->course_name}\n";
    echo "  Tên lớp: {$enrollment->class_name}\n";
    echo "  Học phí: " . number_format($enrollment->course_price) . " VND\n";
    echo "  Ngôn ngữ: {$enrollment->language}\n";
    echo "  Trình độ: {$enrollment->level}\n";
    echo "  Trạng thái: {$enrollment->enrollment_status}\n\n";
}

// Test các câu hỏi với Gemini
$questions = [
    "Học phí của khóa học Tiếng Anh là bao nhiêu?",
    "Cho tôi biết học phí các khóa học tôi đang học",
    "Khóa Tiếng Nhật giá bao nhiêu?",
    "Tôi phải trả bao nhiêu tiền cho lớp học của tôi?",
    "So sánh học phí giữa khóa Tiếng Anh và Tiếng Nhật"
];

echo "\n=== TEST GEMINI VỚI CÂU HỎI VỀ HỌC PHÍ ===\n\n";

$geminiService = app(\App\Services\GeminiChatbotService::class);
$conversationService = app(\App\Services\ConversationService::class);

foreach ($questions as $index => $question) {
    echo "📝 Câu hỏi " . ($index + 1) . ": {$question}\n";
    echo str_repeat("-", 80) . "\n";
    
    try {
        // Gọi Gemini với student_id trực tiếp
        $response = $geminiService->generateResponse($question, $student->id);
        
        echo "🤖 Gemini: {$response}\n\n";
        
        // Kiểm tra response có chứa thông tin học phí không
        $containsPrice = false;
        // Match both dot and comma separators: 1.000.000 or 1,000,000
        if (preg_match('/\d+[.,]?\d+[.,]?\d*\s*(VN[DĐ]|vnđ|đồng|triệu)/i', $response)) {
            $containsPrice = true;
        }
        
        if ($containsPrice) {
            echo "✅ Response chứa thông tin học phí\n";
        } else {
            echo "⚠️  Response không chứa thông tin học phí cụ thể\n";
        }
        
        // Lưu conversation
        try {
            $conversationId = $conversationService->getOrCreateConversation($user->id, false);  // false = return ID
            $conversationService->saveMessage($conversationId, 'student', $question);
            $conversationService->saveMessage($conversationId, 'assistant', $response);
            echo "✅ Đã lưu conversation\n";
        } catch (\Exception $saveError) {
            echo "⚠️  Không lưu được conversation: " . $saveError->getMessage() . "\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Lỗi: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("=", 80) . "\n\n";
    
    // Delay để tránh rate limit
    sleep(2);
}

echo "\n=== KẾT QUẢ KIỂM TRA ===\n";
echo "✅ Đã test " . count($questions) . " câu hỏi về học phí\n";
echo "💡 Kiểm tra xem Gemini có trả lời chính xác học phí từ database không\n";
echo "📊 Học phí thực tế trong database:\n";
foreach ($enrollments as $enrollment) {
    echo "   - {$enrollment->course_name}: " . number_format($enrollment->course_price) . " VND\n";
}
