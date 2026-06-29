<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('email', 'hocvien1@gmail.com')->first();

if (!$user) {
    echo "❌ Không tìm thấy user hocvien1@gmail.com\n";
    exit(1);
}

echo "✅ User: {$user->name} (ID: {$user->id})\n\n";

if (!$user->student) {
    echo "❌ User không có student record\n";
    exit(1);
}

$student = $user->student;
echo "✅ Student ID: {$student->id}\n\n";

// Get ALL enrollments (không filter status)
$allEnrollments = $student->enrollments;

echo "📋 TỔNG SỐ ENROLLMENTS: " . $allEnrollments->count() . "\n\n";

if ($allEnrollments->isEmpty()) {
    echo "⚠️ Không có enrollment nào!\n";
    echo "\nℹ️ Đây là lý do chatbot trả lời 'Ban chua dang ky lop hoc nao.'\n";
    exit(0);
}

foreach ($allEnrollments as $enrollment) {
    echo "─────────────────────────────────────────────\n";
    echo "Enrollment ID: {$enrollment->id}\n";
    echo "Status: {$enrollment->status}\n";
    echo "Class ID: {$enrollment->class_id}\n";
    
    if ($enrollment->class) {
        echo "Class Name: {$enrollment->class->name}\n";
        
        if ($enrollment->class->teacher) {
            $teacher = $enrollment->class->teacher;
            echo "Teacher: {$teacher->user->name}\n";
            echo "Teacher Email: {$teacher->user->email}\n";
            echo "Teacher Phone: " . ($teacher->phone ?? 'N/A') . "\n";
        } else {
            echo "⚠️ Lớp học không có teacher\n";
        }
    } else {
        echo "⚠️ Class không tồn tại (class_id: {$enrollment->class_id})\n";
    }
    
    echo "\n";
}

// Check paid/pending enrollments
$paidOrPending = $student->enrollments()->whereIn('status', ['paid', 'pending'])->get();

echo "═════════════════════════════════════════════\n";
echo "📊 KẾT LUẬN:\n";
echo "═════════════════════════════════════════════\n";
echo "Tổng enrollments: {$allEnrollments->count()}\n";
echo "Enrollments có status 'paid' hoặc 'pending': {$paidOrPending->count()}\n\n";

if ($paidOrPending->isEmpty()) {
    echo "⚠️ KHÔNG CÓ enrollment nào có status 'paid' hoặc 'pending'\n";
    echo "→ Đây là lý do chatbot trả lời: 'Ban chua dang ky lop hoc nao.'\n\n";
    echo "💡 Giải pháp:\n";
    echo "   1. Cập nhật status của enrollment thành 'paid' hoặc 'pending'\n";
    echo "   2. Hoặc sửa code để chấp nhận thêm status khác (ví dụ: 'active', 'enrolled')\n";
} else {
    echo "✅ Có {$paidOrPending->count()} enrollment(s) hợp lệ\n";
    echo "→ Chatbot NÊN trả về thông tin giáo viên\n";
}
