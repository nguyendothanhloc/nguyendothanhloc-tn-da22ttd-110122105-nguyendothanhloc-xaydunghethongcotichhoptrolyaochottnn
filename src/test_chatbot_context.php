<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\Schedule;

echo "=== TEST CHATBOT STUDENT CONTEXT ===\n\n";

// Test với student_id = 1 (hocvien1@gmail.com)
$studentId = 1;

echo "1. KIỂM TRA STUDENT\n";
$student = Student::with([
    'user',
    'enrollments' => function ($query) {
        $query->whereIn('status', ['paid', 'pending', 'completed', 'approved'])
              ->whereHas('class', function($q) {
                  $q->where('status', '!=', 'cancelled');
              });
    },
    'enrollments.class.course',
    'enrollments.class.teacher.user',
])->find($studentId);

if (!$student) {
    echo "❌ KHÔNG TÌM THẤY STUDENT với ID: $studentId\n";
    exit;
}

echo "✅ Tìm thấy: {$student->user->name} ({$student->user->email})\n\n";

echo "2. KIỂM TRA ENROLLMENTS\n";
$enrollments = $student->enrollments;
echo "Số lượng enrollments: " . $enrollments->count() . "\n";

foreach ($enrollments as $idx => $enrollment) {
    echo "\nEnrollment #" . ($idx + 1) . ":\n";
    echo "  - Khóa học: {$enrollment->class->course->name}\n";
    echo "  - Lớp: {$enrollment->class->name}\n";
    echo "  - Class ID: {$enrollment->class_id}\n";
    echo "  - Enrollment Status: {$enrollment->status}\n";
    echo "  - Class Status: {$enrollment->class->status}\n";
    echo "  - Giáo viên: " . ($enrollment->class->teacher->user->name ?? 'N/A') . "\n";
}

echo "\n3. KIỂM TRA SCHEDULES\n";

$classIds = $enrollments->pluck('class_id')->toArray();
echo "Class IDs: " . implode(', ', $classIds) . "\n\n";

if (empty($classIds)) {
    echo "❌ KHÔNG CÓ CLASS ID (enrollment trống hoặc class bị cancelled)\n";
    exit;
}

// Query giống GeminiChatbotService
$upcomingSchedules = Schedule::whereIn('class_id', $classIds)
    ->whereHas('class', function($query) {
        $query->where('status', '!=', 'cancelled');
    })
    ->where('date', '>=', now())
    ->orderBy('date')
    ->orderBy('start_time')
    ->limit(10)
    ->with('class')
    ->get();

echo "Số lượng lịch học sắp tới: " . $upcomingSchedules->count() . "\n";

if ($upcomingSchedules->count() === 0) {
    echo "\n❌ KHÔNG CÓ LỊCH HỌC SẮP TỚI!\n";
    echo "\nNguyên nhân có thể:\n";
    echo "1. Lớp bị cancelled\n";
    echo "2. Không có schedule trong database\n";
    echo "3. Tất cả schedule đều trong quá khứ (date < today)\n";
    
    // Check schedules trong quá khứ
    $pastSchedules = Schedule::whereIn('class_id', $classIds)
        ->where('date', '<', now())
        ->count();
    echo "\nSố lịch học trong QUÁ KHỨ: $pastSchedules\n";
    
    // Check ALL schedules (bỏ filter date)
    $allSchedules = Schedule::whereIn('class_id', $classIds)->get();
    echo "Tổng số lịch học (bao gồm quá khứ): " . $allSchedules->count() . "\n";
    
    if ($allSchedules->count() > 0) {
        echo "\nCÁC LỊCH HỌC HIỆN CÓ:\n";
        foreach ($allSchedules as $s) {
            $isPast = $s->date < now() ? '(QUÁ KHỨ)' : '(SẮP TỚI)';
            echo "  - {$s->date->format('d/m/Y')} {$s->start_time} - {$s->end_time} $isPast\n";
        }
    }
    
    exit;
}

echo "\n✅ CÓ LỊCH HỌC! Chi tiết:\n\n";

foreach ($upcomingSchedules as $idx => $schedule) {
    echo "Lịch #" . ($idx + 1) . ":\n";
    echo "  - Lớp: {$schedule->class->name}\n";
    echo "  - Ngày: " . $schedule->date->format('d/m/Y (l)') . "\n";
    echo "  - Giờ: {$schedule->start_time} - {$schedule->end_time}\n";
    echo "  - Phòng: " . ($schedule->location ?? 'Chưa xác định') . "\n";
    echo "  - Chủ đề: " . ($schedule->topic ?? 'Chưa có') . "\n";
    echo "\n";
}

echo "\n4. KẾT LUẬN\n";
echo "✅ Student Context CÓ DỮ LIỆU!\n";
echo "✅ Chatbot NÊN trả lời được về lịch học.\n\n";

echo "Nếu chatbot vẫn nói 'Chưa có lịch học', nguyên nhân:\n";
echo "1. Gemini đang HALLUCINATE (bịa đặt, không đọc Student Context)\n";
echo "2. Prompt chưa đủ mạnh để ép Gemini đọc dữ liệu\n";
echo "3. Temperature quá cao (0.5) → Gemini tự sáng tạo thay vì đọc data\n";
echo "\n==> Cần TĂNG CƯỜNG PROMPT để ép Gemini đọc đúng!\n";
