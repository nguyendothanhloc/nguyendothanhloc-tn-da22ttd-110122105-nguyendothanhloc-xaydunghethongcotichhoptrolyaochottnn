<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Enrollment;
use App\Models\Student;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║          KIỂM TRA ĐĂNG KÝ LỚP HỌC CỦA HỌC VIÊN            ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$students = Student::with('user')->get();

foreach ($students as $student) {
    echo "👤 HỌC VIÊN: {$student->user->name} (ID: {$student->id})\n";
    echo "   Email: {$student->user->email}\n";
    echo "─────────────────────────────────────────────────────────────\n";
    
    $enrollments = Enrollment::where('student_id', $student->id)
        ->with(['class.course', 'class.teacher.user'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    if ($enrollments->isEmpty()) {
        echo "   ℹ️  Chưa đăng ký lớp nào\n\n";
    } else {
        echo "   📚 Tổng số lớp đã đăng ký: {$enrollments->count()}\n\n";
        
        foreach ($enrollments as $enroll) {
            echo "   Lớp: {$enroll->class->name}\n";
            echo "   Khóa học: {$enroll->class->course->name}\n";
            echo "   Giáo viên: {$enroll->class->teacher->user->name}\n";
            echo "   Ngày đăng ký: {$enroll->created_at->format('d/m/Y H:i:s')}\n";
            echo "   Status: {$enroll->status}\n";
            
            // Check schedules
            $scheduleCount = \App\Models\Schedule::where('class_id', $enroll->class_id)->count();
            echo "   Số buổi học: {$scheduleCount}\n";
            
            echo "\n";
        }
    }
    
    echo "\n";
}
