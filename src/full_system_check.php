<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Course;

echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║                 BÁO CÁO TOÀN DIỆN HỆ THỐNG                          ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

$errors = [];
$fixes = [];

// ============================================================================
// 1. KIỂM TRA DATABASE CONNECTION
// ============================================================================
echo "🔌 1. KIỂM TRA KẾT NỐI DATABASE\n";
echo "────────────────────────────────────────────────────────────────────\n";
try {
    \DB::connection()->getPdo();
    echo "   ✅ Kết nối database thành công\n";
    echo "   Database: " . \DB::connection()->getDatabaseName() . "\n\n";
} catch (\Exception $e) {
    echo "   ❌ LỖI kết nối database: " . $e->getMessage() . "\n\n";
    $errors[] = "Lỗi kết nối database";
}

// ============================================================================
// 2. KIỂM TRA USERS & AUTHENTICATION
// ============================================================================
echo "👥 2. KIỂM TRA TÀI KHOẢN NGƯỜI DÙNG\n";
echo "────────────────────────────────────────────────────────────────────\n";

$adminCount = User::where('role', 'admin')->count();
$teacherCount = User::where('role', 'teacher')->count();
$studentCount = User::where('role', 'student')->count();

echo "   Số admin: {$adminCount}\n";
echo "   Số giáo viên: {$teacherCount}\n";
echo "   Số học viên: {$studentCount}\n\n";

// Check if teachers have profiles
echo "   🔍 Kiểm tra teacher profiles:\n";
$teachersWithoutProfile = User::where('role', 'teacher')
    ->whereDoesntHave('teacher')
    ->get();

if ($teachersWithoutProfile->count() > 0) {
    echo "   ❌ LỖI: {$teachersWithoutProfile->count()} giáo viên không có profile!\n";
    foreach ($teachersWithoutProfile as $teacher) {
        echo "      - {$teacher->name} ({$teacher->email})\n";
        $errors[] = "Teacher {$teacher->email} không có profile";
    }
    echo "\n";
} else {
    echo "   ✅ Tất cả giáo viên đều có profile\n\n";
}

// Check if students have profiles
echo "   🔍 Kiểm tra student profiles:\n";
$studentsWithoutProfile = User::where('role', 'student')
    ->whereDoesntHave('student')
    ->get();

if ($studentsWithoutProfile->count() > 0) {
    echo "   ❌ LỖI: {$studentsWithoutProfile->count()} học viên không có profile!\n";
    foreach ($studentsWithoutProfile as $student) {
        echo "      - {$student->name} ({$student->email})\n";
        $errors[] = "Student {$student->email} không có profile";
    }
    echo "\n";
} else {
    echo "   ✅ Tất cả học viên đều có profile\n\n";
}

// ============================================================================
// 3. KIỂM TRA COURSES & CLASSES
// ============================================================================
echo "📚 3. KIỂM TRA KHÓA HỌC & LỚP HỌC\n";
echo "────────────────────────────────────────────────────────────────────\n";

$activeCourses = Course::where('is_active', true)->count();
$totalClasses = ClassModel::count();
$upcomingClasses = ClassModel::where('status', 'upcoming')->count();
$ongoingClasses = ClassModel::where('status', 'ongoing')->count();

echo "   Khóa học đang hoạt động: {$activeCourses}\n";
echo "   Tổng số lớp học: {$totalClasses}\n";
echo "   Lớp sắp diễn ra: {$upcomingClasses}\n";
echo "   Lớp đang diễn ra: {$ongoingClasses}\n\n";

// Check if all classes have teachers
echo "   🔍 Kiểm tra giáo viên phụ trách lớp:\n";
$classesWithoutTeacher = ClassModel::whereNull('teacher_id')->orWhere('teacher_id', 0)->get();

if ($classesWithoutTeacher->count() > 0) {
    echo "   ❌ LỖI: {$classesWithoutTeacher->count()} lớp không có giáo viên!\n";
    foreach ($classesWithoutTeacher as $class) {
        echo "      - {$class->name} (ID: {$class->id})\n";
        $errors[] = "Lớp {$class->name} không có giáo viên";
    }
    echo "\n";
} else {
    echo "   ✅ Tất cả lớp đều có giáo viên\n\n";
}

// Check class capacity vs enrollment
echo "   🔍 Kiểm tra sức chứa lớp học:\n";
$capacityIssues = 0;
foreach (ClassModel::all() as $class) {
    $actualEnrollment = Enrollment::where('class_id', $class->id)
        ->where('status', 'approved')
        ->count();
    
    if ($class->current_enrollment != $actualEnrollment) {
        echo "   ⚠️  Lớp {$class->name}: DB lưu {$class->current_enrollment} nhưng thực tế {$actualEnrollment}\n";
        $capacityIssues++;
        
        // Auto-fix
        $class->current_enrollment = $actualEnrollment;
        $class->save();
        $fixes[] = "Đã sửa current_enrollment cho lớp {$class->name}";
    }
}

if ($capacityIssues > 0) {
    echo "   ✅ Đã tự động sửa {$capacityIssues} lỗi về sức chứa\n\n";
} else {
    echo "   ✅ Sức chứa lớp học chính xác\n\n";
}

// ============================================================================
// 4. KIỂM TRA ENROLLMENT WORKFLOW
// ============================================================================
echo "📝 4. KIỂM TRA QUY TRÌNH ĐĂNG KÝ HỌC\n";
echo "────────────────────────────────────────────────────────────────────\n";

$pendingEnrollments = Enrollment::where('status', 'pending')->count();
$approvedEnrollments = Enrollment::where('status', 'approved')->count();
$rejectedEnrollments = Enrollment::where('status', 'rejected')->count();

echo "   Đơn chờ duyệt: {$pendingEnrollments}\n";
echo "   Đơn đã duyệt: {$approvedEnrollments}\n";
echo "   Đơn bị từ chối: {$rejectedEnrollments}\n\n";

if ($pendingEnrollments > 0) {
    echo "   ⚠️  CÓ {$pendingEnrollments} ĐƠN CHỜ ADMIN DUYỆT!\n";
    echo "      URL: http://127.0.0.1:8000/admin/enrollments\n\n";
}

// Check for duplicate enrollments
echo "   🔍 Kiểm tra đăng ký trùng lặp:\n";
$duplicates = Enrollment::select('student_id', 'class_id', \DB::raw('COUNT(*) as count'))
    ->groupBy('student_id', 'class_id')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->count() > 0) {
    echo "   ❌ LỖI: Phát hiện {$duplicates->count()} trường hợp trùng lặp!\n";
    foreach ($duplicates as $dup) {
        $enrollments = Enrollment::where('student_id', $dup->student_id)
            ->where('class_id', $dup->class_id)
            ->orderBy('created_at', 'asc')
            ->get();
        
        $student = Student::find($dup->student_id);
        $class = ClassModel::find($dup->class_id);
        
        echo "      - Student: {$student->user->name}, Lớp: {$class->name} ({$dup->count} lần)\n";
        
        // Keep first, delete rest
        $first = true;
        foreach ($enrollments as $enroll) {
            if (!$first) {
                $enroll->delete();
                $fixes[] = "Đã xóa enrollment trùng ID {$enroll->id}";
            }
            $first = false;
        }
    }
    echo "   ✅ Đã tự động xóa các đăng ký trùng lặp\n\n";
} else {
    echo "   ✅ Không có đăng ký trùng lặp\n\n";
}

// ============================================================================
// 5. KIỂM TRA SCHEDULE (LỊCH HỌC)
// ============================================================================
echo "📅 5. KIỂM TRA LỊCH HỌC\n";
echo "────────────────────────────────────────────────────────────────────\n";

$totalSchedules = Schedule::count();
echo "   Tổng số buổi học trong hệ thống: {$totalSchedules}\n\n";

// Check if approved enrollments have schedules
echo "   🔍 Kiểm tra lịch học cho học viên đã đăng ký:\n";
$scheduleIssues = 0;

foreach (Student::all() as $student) {
    $approvedClasses = Enrollment::where('student_id', $student->id)
        ->where('status', 'approved')
        ->with('class')
        ->get();
    
    if ($approvedClasses->count() > 0) {
        $totalStudentSchedules = 0;
        
        foreach ($approvedClasses as $enroll) {
            $classSchedules = Schedule::where('class_id', $enroll->class_id)->count();
            $totalStudentSchedules += $classSchedules;
            
            if ($classSchedules === 0) {
                echo "   ⚠️  Học viên {$student->user->name}: Lớp {$enroll->class->name} không có lịch học\n";
                $scheduleIssues++;
            }
        }
        
        if ($totalStudentSchedules > 0) {
            echo "   ✅ Học viên {$student->user->name}: {$approvedClasses->count()} lớp, {$totalStudentSchedules} buổi học\n";
        }
    }
}

if ($scheduleIssues > 0) {
    echo "\n   ⚠️  Phát hiện {$scheduleIssues} lớp học không có lịch!\n";
    echo "      → Cần tạo schedule cho các lớp này\n\n";
} else {
    echo "\n   ✅ Tất cả lớp đã đăng ký đều có lịch học\n\n";
}

// ============================================================================
// 6. KIỂM TRA ROUTES & VIEWS
// ============================================================================
echo "🛣️  6. KIỂM TRA ROUTES QUAN TRỌNG\n";
echo "────────────────────────────────────────────────────────────────────\n";

$importantRoutes = [
    'login' => '/login',
    'admin.dashboard' => '/admin/dashboard',
    'enrollments.admin' => '/admin/enrollments',
    'classes.index' => '/classes',
    'teachers.index' => '/teachers',
    'courses.index' => '/courses',
    'student.dashboard' => '/student/dashboard',
    'student.schedule' => '/student/schedule',
];

foreach ($importantRoutes as $name => $path) {
    if (route($name, [], false) === $path) {
        echo "   ✅ {$name}: {$path}\n";
    } else {
        echo "   ⚠️  {$name}: Expected {$path}, got " . route($name, [], false) . "\n";
    }
}

echo "\n";

// ============================================================================
// FINAL SUMMARY
// ============================================================================
echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║                            TÓM TẮT                                   ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

echo "📊 THỐNG KÊ HỆ THỐNG:\n";
echo "   - Tổng users: " . User::count() . " (Admin: {$adminCount}, Teacher: {$teacherCount}, Student: {$studentCount})\n";
echo "   - Khóa học hoạt động: {$activeCourses}\n";
echo "   - Lớp học: {$totalClasses} (Upcoming: {$upcomingClasses}, Ongoing: {$ongoingClasses})\n";
echo "   - Đơn đăng ký: " . Enrollment::count() . " (Pending: {$pendingEnrollments}, Approved: {$approvedEnrollments})\n";
echo "   - Lịch học: {$totalSchedules} buổi\n\n";

if (count($errors) > 0) {
    echo "❌ PHÁT HIỆN " . count($errors) . " LỖI:\n";
    foreach ($errors as $i => $error) {
        echo "   " . ($i + 1) . ". {$error}\n";
    }
    echo "\n";
}

if (count($fixes) > 0) {
    echo "✅ ĐÃ TỰ ĐỘNG SỬA " . count($fixes) . " LỖI:\n";
    foreach ($fixes as $i => $fix) {
        echo "   " . ($i + 1) . ". {$fix}\n";
    }
    echo "\n";
}

if (count($errors) === 0 && count($fixes) === 0) {
    echo "✅ HỆ THỐNG HOẠT ĐỘNG TỐT!\n\n";
}

echo "📍 ĐƯỜNG DẪN QUAN TRỌNG:\n";
echo "   🌐 Trang chủ: http://127.0.0.1:8000\n";
echo "   🔐 Đăng nhập: http://127.0.0.1:8000/login\n";
echo "   👑 Admin dashboard: http://127.0.0.1:8000/admin/dashboard\n";
echo "   📋 Quản lý đăng ký: http://127.0.0.1:8000/admin/enrollments\n";
echo "   🎓 Student dashboard: http://127.0.0.1:8000/student/dashboard\n\n";

echo "🔑 TÀI KHOẢN TEST:\n";
echo "   Admin: admin1@admin.com / password\n";
echo "   Teacher: teacher1@teacher.com / password\n";
echo "   Student: hocvien1@gmail.com / password\n\n";
