<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║          TEST QUY TRÌNH ĐĂNG KÝ HỌC VÀ THÔNG BÁO ADMIN      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// 1. TẠO ENROLLMENT MỚI (GIẢ LẬP HỌC VIÊN ĐĂNG KÝ)
echo "📝 BƯỚC 1: Tạo đơn đăng ký mới (giả lập học viên đăng ký)\n";
echo "─────────────────────────────────────────────────────────────\n";

$student = Student::with('user')->first();
$class = ClassModel::with('course')->where('status', 'upcoming')->first();

if (!$student || !$class) {
    echo "❌ Không tìm thấy student hoặc class để test!\n";
    exit(1);
}

echo "   Học viên: {$student->user->name} (ID: {$student->id})\n";
echo "   Lớp học: {$class->name} (ID: {$class->id})\n\n";

// Check if already enrolled
$existingEnrollment = Enrollment::where('student_id', $student->id)
    ->where('class_id', $class->id)
    ->first();

if ($existingEnrollment) {
    echo "   ℹ️  Đã tồn tại enrollment (ID: {$existingEnrollment->id}) với status: {$existingEnrollment->status}\n";
    $enrollment = $existingEnrollment;
    
    if ($enrollment->status !== 'pending') {
        echo "   🔄 Đổi status về 'pending' để test...\n";
        $enrollment->update(['status' => 'pending']);
    }
} else {
    echo "   ✅ Tạo enrollment mới...\n";
    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'enrollment_date' => now(),
        'status' => 'pending',
    ]);
    echo "   ✅ Đã tạo enrollment ID: {$enrollment->id}\n";
}

echo "\n";

// 2. KIỂM TRA XEM ADMIN CÓ NHÌN THẤY KHÔNG
echo "🔍 BƯỚC 2: Kiểm tra xem admin có nhìn thấy không\n";
echo "─────────────────────────────────────────────────────────────\n";

$pendingCount = Enrollment::where('status', 'pending')->count();
echo "   Tổng số đơn chờ duyệt: {$pendingCount}\n";

if ($pendingCount === 0) {
    echo "   ❌ LỖI: Không có đơn pending nào!\n";
} else {
    echo "   ✅ Có {$pendingCount} đơn đăng ký chờ duyệt\n\n";
    
    echo "   📋 Danh sách đơn chờ duyệt:\n";
    $pending = Enrollment::with(['student.user', 'class.course'])
        ->where('status', 'pending')
        ->get();
    
    foreach ($pending as $p) {
        echo "   - ID {$p->id}: {$p->student->user->name} → {$p->class->name}\n";
        echo "     Ngày đăng ký: {$p->created_at->format('d/m/Y H:i:s')}\n";
    }
}

echo "\n";

// 3. KIỂM TRA ADMIN DASHBOARD
echo "🎯 BƯỚC 3: Kiểm tra admin dashboard\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "   URL Admin Dashboard: http://127.0.0.1:8000/admin/dashboard\n";
echo "   URL Quản lý đăng ký: http://127.0.0.1:8000/admin/enrollments\n\n";

echo "   📊 Thống kê sẽ hiển thị trên dashboard:\n";
echo "   - Đăng ký chờ duyệt: {$pendingCount}\n";
echo "   - Tổng học viên: " . Student::count() . "\n";
echo "   - Tổng khóa học: " . \App\Models\Course::where('is_active', true)->count() . "\n";
echo "   - Tổng giáo viên: " . \App\Models\Teacher::count() . "\n\n";

if ($pendingCount > 0) {
    echo "   ✅ Admin SẼ THẤY BADGE CẢNH BÁO ({$pendingCount}) trên dashboard!\n";
} else {
    echo "   ℹ️  Không có badge cảnh báo vì không có đơn pending\n";
}

echo "\n";

// 4. TEST APPROVE ENROLLMENT
echo "✅ BƯỚC 4: Test duyệt đơn đăng ký\n";
echo "─────────────────────────────────────────────────────────────\n";

if ($pendingCount > 0) {
    $testEnrollment = Enrollment::where('status', 'pending')->first();
    
    echo "   Đang duyệt enrollment ID: {$testEnrollment->id}\n";
    
    // Simulate approve action
    $testEnrollment->update(['status' => 'approved']);
    
    // Update class enrollment count
    $class = ClassModel::find($testEnrollment->class_id);
    $approvedCount = Enrollment::where('class_id', $class->id)
        ->where('status', 'approved')
        ->count();
    $class->update(['current_enrollment' => $approvedCount]);
    
    echo "   ✅ Đã duyệt thành công!\n";
    echo "   ✅ Lớp học {$class->name} hiện có {$class->current_enrollment} học viên\n\n";
    
    // Check pending count again
    $newPendingCount = Enrollment::where('status', 'pending')->count();
    echo "   Số đơn chờ duyệt còn lại: {$newPendingCount}\n";
}

echo "\n";

// 5. KIỂM TRA SCHEDULE
echo "📅 BƯỚC 5: Kiểm tra lịch học\n";
echo "─────────────────────────────────────────────────────────────\n";

$studentEnrollments = Enrollment::where('student_id', $student->id)
    ->where('status', 'approved')
    ->with('class')
    ->get();

echo "   Học viên {$student->user->name} đã đăng ký {$studentEnrollments->count()} lớp (approved)\n\n";

foreach ($studentEnrollments as $enroll) {
    $schedules = \App\Models\Schedule::where('class_id', $enroll->class_id)->count();
    echo "   - Lớp: {$enroll->class->name}\n";
    echo "     Số buổi học: {$schedules}\n";
    
    if ($schedules === 0) {
        echo "     ❌ LỖI: Lớp này không có lịch học!\n";
    } else {
        echo "     ✅ Có lịch học\n";
    }
}

echo "\n";

// FINAL SUMMARY
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                          KẾT LUẬN                            ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$finalPendingCount = Enrollment::where('status', 'pending')->count();

if ($finalPendingCount > 0) {
    echo "⚠️  ADMIN CẦN DUYỆT {$finalPendingCount} ĐƠN ĐĂNG KÝ!\n\n";
    echo "📍 Cách duyệt:\n";
    echo "   1. Đăng nhập admin: http://127.0.0.1:8000/login\n";
    echo "   2. Email: admin1@admin.com | Password: password\n";
    echo "   3. Vào: http://127.0.0.1:8000/admin/enrollments\n";
    echo "   4. Click nút 'Duyệt' trên đơn đăng ký\n\n";
} else {
    echo "✅ HỆ THỐNG HOẠT ĐỘNG BÌNH THƯỜNG!\n";
    echo "   Không có đơn đăng ký chờ duyệt.\n\n";
}

echo "📊 THỐNG KÊ:\n";
echo "   - Tổng enrollments: " . Enrollment::count() . "\n";
echo "   - Pending: " . Enrollment::where('status', 'pending')->count() . "\n";
echo "   - Approved: " . Enrollment::where('status', 'approved')->count() . "\n";
echo "   - Rejected: " . Enrollment::where('status', 'rejected')->count() . "\n\n";
