<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Models\Student;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║     TEST ĐĂNG KÝ NHIỀU LỚP CHO HỌC VIÊN                    ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$student = Student::first();
$japaneseClass = ClassModel::where('name', 'LIKE', '%Nhật%')->orWhere('name', 'LIKE', '%Japanese%')->first();

if (!$japaneseClass) {
    $japaneseClass = ClassModel::find(2); // Try ID 2
}

if (!$student || !$japaneseClass) {
    echo "❌ Không tìm thấy student hoặc lớp tiếng Nhật!\n";
    exit(1);
}

echo "👤 Học viên: {$student->user->name}\n";
echo "📚 Lớp học: {$japaneseClass->name}\n\n";

// Check if already enrolled
$existing = Enrollment::where('student_id', $student->id)
    ->where('class_id', $japaneseClass->id)
    ->first();

if ($existing) {
    echo "ℹ️  Đã có enrollment tồn tại (ID: {$existing->id})\n";
    echo "   Status: {$existing->status}\n\n";
} else {
    echo "✅ Tạo enrollment mới...\n";
    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'class_id' => $japaneseClass->id,
        'enrollment_date' => now(),
        'status' => 'approved',
        'completion_percentage' => 0,
    ]);
    
    // Update class enrollment count
    $japaneseClass->increment('current_enrollment');
    
    echo "✅ Đã tạo enrollment ID: {$enrollment->id}\n\n";
}

// Show all enrollments
echo "📋 DANH SÁCH TẤT CẢ LỚP HỌC CỦA HỌC VIÊN:\n";
echo "─────────────────────────────────────────────────\n";

$enrollments = Enrollment::where('student_id', $student->id)
    ->with(['class.course', 'class.teacher.user'])
    ->get();

foreach ($enrollments as $enroll) {
    echo "✓ {$enroll->class->name} ({$enroll->class->course->name}) - {$enroll->status}\n";
}

echo "\nTổng: {$enrollments->count()} lớp\n";
