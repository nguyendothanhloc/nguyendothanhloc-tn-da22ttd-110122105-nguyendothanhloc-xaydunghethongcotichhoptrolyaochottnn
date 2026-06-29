<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use App\Models\Schedule;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║     KIỂM TRA TOÀN BỘ LOGIC HỆ THỐNG QUẢN LÝ TRUNG TÂM     ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$errors = [];
$warnings = [];

// ============================================
// 1. KIỂM TRA ENROLLMENT APPROVAL WORKFLOW
// ============================================
echo "📋 1. KIỂM TRA QUY TRÌNH DUYỆT ĐĂNG KÝ HỌC\n";
echo "─────────────────────────────────────────────────\n";

$pendingEnrollments = Enrollment::where('status', 'pending')->count();
$approvedEnrollments = Enrollment::where('status', 'approved')->count();
$rejectedEnrollments = Enrollment::where('status', 'rejected')->count();

echo "   Đơn đăng ký chờ duyệt: {$pendingEnrollments}\n";
echo "   Đơn đăng ký đã duyệt: {$approvedEnrollments}\n";
echo "   Đơn đăng ký bị từ chối: {$rejectedEnrollments}\n";

if ($pendingEnrollments > 0) {
    echo "\n   ⚠️  CÓ {$pendingEnrollments} ĐƠN ĐĂNG KÝ CHỜ ADMIN DUYỆT!\n";
    $warnings[] = "Có {$pendingEnrollments} đơn đăng ký chờ duyệt - Admin cần vào menu 'Quản lý đăng ký' để duyệt";
    
    echo "\n   Chi tiết các đơn chờ duyệt:\n";
    $pending = Enrollment::with(['student.user', 'class.course'])
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->get();
    
    foreach ($pending as $enroll) {
        echo "   - ID {$enroll->id}: {$enroll->student->user->name} đăng ký {$enroll->class->name}\n";
        echo "     Ngày đăng ký: {$enroll->created_at->format('d/m/Y H:i')}\n";
    }
}

// ============================================
// 2. KIỂM TRA SCHEDULE (LỊCH HỌC)
// ============================================
echo "\n\n📅 2. KIỂM TRA LỊCH HỌC\n";
echo "─────────────────────────────────────────────────\n";

$students = Student::with('user')->get();
foreach ($students as $student) {
    $approvedEnrollments = Enrollment::where('student_id', $student->id)
        ->where('status', 'approved')
        ->count();
    
    if ($approvedEnrollments > 0) {
        // Count schedules for this student
        $scheduleCount = Schedule::whereHas('class.enrollments', function($q) use ($student) {
            $q->where('student_id', $student->id)
              ->where('status', 'approved');
        })->count();
        
        echo "   Học viên: {$student->user->name}\n";
        echo "   - Số lớp đã đăng ký: {$approvedEnrollments}\n";
        echo "   - Số buổi học trong lịch: {$scheduleCount}\n";
        
        if ($approvedEnrollments > 0 && $scheduleCount === 0) {
            echo "   ❌ LỖI: Học viên đã đăng ký lớp nhưng KHÔNG CÓ LỊCH HỌC!\n";
            $errors[] = "Học viên {$student->user->name} (ID: {$student->id}) đã đăng ký {$approvedEnrollments} lớp nhưng không có lịch học";
        } elseif ($approvedEnrollments * 10 > $scheduleCount * 2) {
            // Estimate: each class should have ~10-20 schedule entries
            echo "   ⚠️  CẢNH BÁO: Lịch học có vẻ ít hơn bình thường\n";
            $warnings[] = "Học viên {$student->user->name} có {$approvedEnrollments} lớp nhưng chỉ có {$scheduleCount} buổi học";
        } else {
            echo "   ✅ Lịch học hợp lý\n";
        }
    }
}

// ============================================
// 3. KIỂM TRA CLASS CAPACITY
// ============================================
echo "\n\n👥 3. KIỂM TRA SỨC CHỨA LỚP HỌC\n";
echo "─────────────────────────────────────────────────\n";

$classes = ClassModel::with('enrollments')->get();
foreach ($classes as $class) {
    $actualEnrollment = $class->enrollments()->where('status', 'approved')->count();
    $storedEnrollment = $class->current_enrollment;
    
    echo "   Lớp: {$class->name}\n";
    echo "   - Sức chứa tối đa: {$class->max_capacity}\n";
    echo "   - Số học viên thực tế: {$actualEnrollment}\n";
    echo "   - Số học viên lưu trong DB: {$storedEnrollment}\n";
    
    if ($actualEnrollment != $storedEnrollment) {
        echo "   ❌ LỖI: Số liệu không khớp!\n";
        $errors[] = "Lớp {$class->name} (ID: {$class->id}) có {$actualEnrollment} học viên thực tế nhưng DB lưu {$storedEnrollment}";
    }
    
    if ($actualEnrollment > $class->max_capacity) {
        echo "   ❌ LỖI: Vượt quá sức chứa!\n";
        $errors[] = "Lớp {$class->name} (ID: {$class->id}) có {$actualEnrollment} học viên vượt quá sức chứa {$class->max_capacity}";
    }
}

// ============================================
// 4. KIỂM TRA ADMIN NOTIFICATION SYSTEM
// ============================================
echo "\n\n🔔 4. KIỂM TRA HỆ THỐNG THÔNG BÁO ADMIN\n";
echo "─────────────────────────────────────────────────\n";

// Check if admin dashboard shows pending enrollments
$adminDashboardPath = resource_path('views/admin/dashboard.blade.php');
$dashboardContent = file_get_contents($adminDashboardPath);

if (strpos($dashboardContent, 'pending') !== false || strpos($dashboardContent, 'enrollments') !== false) {
    echo "   ✅ Admin dashboard có hiển thị enrollment\n";
} else {
    echo "   ⚠️  Admin dashboard có thể thiếu thông báo enrollment\n";
    $warnings[] = "Admin dashboard có thể không hiển thị đơn đăng ký chờ duyệt";
}

// ============================================
// 5. KIỂM TRA DUPLICATE ENROLLMENTS
// ============================================
echo "\n\n🔄 5. KIỂM TRA ĐĂNG KÝ TRÙNG LẶP\n";
echo "─────────────────────────────────────────────────\n";

$duplicates = Enrollment::select('student_id', 'class_id')
    ->groupBy('student_id', 'class_id')
    ->havingRaw('COUNT(*) > 1')
    ->get();

if ($duplicates->count() > 0) {
    echo "   ❌ LỖI: Phát hiện {$duplicates->count()} trường hợp đăng ký trùng lặp!\n";
    foreach ($duplicates as $dup) {
        $enrolls = Enrollment::where('student_id', $dup->student_id)
            ->where('class_id', $dup->class_id)
            ->get();
        echo "   - Student ID {$dup->student_id} đăng ký Class ID {$dup->class_id} {$enrolls->count()} lần\n";
        $errors[] = "Student ID {$dup->student_id} đăng ký Class ID {$dup->class_id} nhiều lần";
    }
} else {
    echo "   ✅ Không có đăng ký trùng lặp\n";
}

// ============================================
// SUMMARY
// ============================================
echo "\n\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                         TÓM TẮT KẾT QUẢ                      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

if (count($errors) === 0 && count($warnings) === 0) {
    echo "✅ HỆ THỐNG HOẠT ĐỘNG TỐT!\n";
    echo "   Không phát hiện lỗi nghiêm trọng.\n\n";
} else {
    if (count($errors) > 0) {
        echo "❌ PHÁT HIỆN " . count($errors) . " LỖI NGHIÊM TRỌNG:\n";
        foreach ($errors as $i => $error) {
            echo "   " . ($i + 1) . ". {$error}\n";
        }
        echo "\n";
    }
    
    if (count($warnings) > 0) {
        echo "⚠️  PHÁT HIỆN " . count($warnings) . " CẢNH BÁO:\n";
        foreach ($warnings as $i => $warning) {
            echo "   " . ($i + 1) . ". {$warning}\n";
        }
        echo "\n";
    }
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    ĐƯỜNG DẪN ADMIN CẦN BIẾT                  ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";
echo "📍 Menu Quản lý đăng ký: http://127.0.0.1:8000/admin/enrollments\n";
echo "📍 Dashboard Admin: http://127.0.0.1:8000/admin/dashboard\n";
echo "📍 Quản lý lớp học: http://127.0.0.1:8000/classes\n";
echo "📍 Quản lý giáo viên: http://127.0.0.1:8000/teachers\n\n";
