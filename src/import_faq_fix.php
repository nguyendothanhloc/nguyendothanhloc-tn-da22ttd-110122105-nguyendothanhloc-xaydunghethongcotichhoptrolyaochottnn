<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== IMPORT FAQ FIX ===\n\n";

// Xóa FAQ cũ về tiếng Nhật
echo "1. Xóa FAQ cũ về tiếng Nhật...\n";
$deleted = \App\Models\ChatbotKnowledge::where(function($query) {
    $query->where('question', 'LIKE', '%tiếng Nhật%')
          ->orWhere('question', 'LIKE', '%tiếng nhật%');
})->delete();
echo "   Đã xóa: {$deleted} FAQ\n\n";

// Thêm FAQ mới
echo "2. Thêm FAQ mới với keywords đầy đủ...\n";

$faqs = [
    [
        'question' => 'Có dạy tiếng Nhật không?',
        'answer' => "Có! Trung tâm có dạy tiếng Nhật với nhiều cấp độ:\n\n📚 N5 (Sơ cấp)\n📚 N4 (Trung cấp thấp)\n📚 N3 (Trung cấp)\n📚 N2-N1 (Cao cấp)\n\nGiáo viên là người Nhật Bản và giáo viên Việt Nam có chứng chỉ JLPT. Bạn có thể đăng ký học online hoặc offline.",
        'category' => 'Khóa học',
        'keywords' => 'co day,day,hoc,tieng nhat,nhat,japanese,nhat ban,n5,n4,n3,n2,n1,jlpt,khoa hoc nhat,giao vien nhat',
        'priority' => 10
    ],
    [
        'question' => 'Có dạy tiếng Anh không?',
        'answer' => "Có! Trung tâm có nhiều khóa tiếng Anh:\n\n📚 Tiếng Anh giao tiếp (Communication)\n📚 Tiếng Anh IELTS (4.0 - 8.0+)\n📚 Tiếng Anh TOEIC (450 - 990)\n📚 Tiếng Anh thiếu nhi (Kids)\n📚 Tiếng Anh doanh nghiệp (Business)\n\nGiáo viên có chứng chỉ TESOL/CELTA và native speakers.",
        'category' => 'Khóa học',
        'keywords' => 'co day,day,hoc,tieng anh,anh,english,ielts,toeic,khoa hoc anh,giao vien anh',
        'priority' => 10
    ],
    [
        'question' => 'Có dạy tiếng Hàn không?',
        'answer' => "Có! Trung tâm có dạy tiếng Hàn với các cấp độ:\n\n🇰🇷 Topik 1 (Sơ cấp)\n🇰🇷 Topik 2 (Trung cấp)\n🇰🇷 Topik 3-4 (Nâng cao)\n\nGiáo viên là người Hàn Quốc và giáo viên Việt Nam có chứng chỉ Topik. Bạn có thể đăng ký học online hoặc offline.",
        'category' => 'Khóa học',
        'keywords' => 'co day,day,hoc,tieng han,han,korean,han quoc,topik,khoa hoc han,giao vien han',
        'priority' => 10
    ],
    [
        'question' => 'Có dạy tiếng Trung không?',
        'answer' => "Có! Trung tâm có dạy tiếng Trung với các cấp độ:\n\n🇨🇳 HSK 1-2 (Sơ cấp)\n🇨🇳 HSK 3-4 (Trung cấp)\n🇨🇳 HSK 5-6 (Nâng cao)\n\nGiáo viên là người Trung Quốc và giáo viên Việt Nam có chứng chỉ HSK. Bạn có thể đăng ký học online hoặc offline.",
        'category' => 'Khóa học',
        'keywords' => 'co day,day,hoc,tieng trung,trung,chinese,trung quoc,hsk,khoa hoc trung,giao vien trung',
        'priority' => 10
    ],
    [
        'question' => 'Có hỗ trợ học online không?',
        'answer' => "Có! Trung tâm hỗ trợ cả 2 hình thức:\n\n✅ Lớp học trực tiếp (offline)\n✅ Lớp học online qua Zoom/Google Meet\n\nBạn có thể chọn hình thức phù hợp khi đăng ký. Chất lượng giảng dạy đảm bảo như nhau.",
        'category' => 'Hình thức học',
        'keywords' => 'co,ho tro,hoc online,online,truc tuyen,tu xa,zoom,google meet,lop online,khoa online',
        'priority' => 10
    ],
    [
        'question' => 'Giáo viên có phải người bản xứ không?',
        'answer' => "Trung tâm có cả 2 loại giáo viên:\n\n👨‍🏫 Giáo viên bản địa:\nNgười Việt có chứng chỉ quốc tế (TESOL, IELTS 8.0+, JLPT N1, HSK 6...)\n\n👩‍🏫 Giáo viên nước ngoài:\nNative speakers từ Anh, Mỹ, Úc, Nhật, Hàn, Trung...\n\nTùy theo khóa học và lịch học, bạn sẽ được học với giáo viên phù hợp.",
        'category' => 'Giáo viên',
        'keywords' => 'giao vien,co phai,ban xu,native,nguoi nuoc ngoai,foreign teacher,teacher,nguoi ban xu',
        'priority' => 10
    ]
];

foreach ($faqs as $faqData) {
    $faq = \App\Models\ChatbotKnowledge::create([
        'question' => $faqData['question'],
        'answer' => $faqData['answer'],
        'category' => $faqData['category'],
        'keywords' => $faqData['keywords'],
        'priority' => $faqData['priority'],
        'is_active' => 1
    ]);
    echo "   ✅ Đã tạo: {$faq->question}\n";
}

echo "\n3. Kiểm tra lại...\n";
$total = \App\Models\ChatbotKnowledge::where('is_active', 1)->count();
echo "   Tổng FAQ active: {$total}\n";

echo "\n=== HOÀN THÀNH ===\n";
echo "Bây giờ test lại chatbot với câu: 'Có dạy tiếng Nhật không?'\n";
