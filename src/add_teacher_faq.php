<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ChatbotKnowledge;

// Add FAQ entry for "giáo viên của tôi"
$faq = ChatbotKnowledge::create([
    'question' => 'Giáo viên của tôi là ai?',
    'answer' => "Để biết thông tin giáo viên của bạn:\n\n" .
                "1️⃣ Vào trang **Dashboard** của bạn\n" .
                "2️⃣ Xem mục **Khóa học đang học**\n" .
                "3️⃣ Mỗi khóa học sẽ hiển thị tên giáo viên phụ trách\n\n" .
                "Hoặc bạn có thể hỏi cụ thể hơn như:\n" .
                "- 'Giáo viên dạy tiếng Anh của tôi là ai?'\n" .
                "- 'Thông tin liên hệ giáo viên tiếng Nhật'\n\n" .
                "Tôi sẽ tra cứu thông tin chi tiết cho bạn! 😊",
    'keywords' => 'giao vien cua toi, thay cua toi, co cua toi, my teacher',
    'category' => 'teacher',
    'priority' => 100, // High priority to match first
    'is_active' => true,
    'created_at' => now(),
    'updated_at' => now()
]);

echo "✅ FAQ entry created successfully!\n";
echo "ID: {$faq->id}\n";
echo "Question: {$faq->question}\n";
echo "Priority: {$faq->priority}\n";
echo "Status: " . ($faq->is_active ? 'Active' : 'Inactive') . "\n";
