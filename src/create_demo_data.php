<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\Schedule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║               TẠO DỮ LIỆU DEMO ĐẦY ĐỦ CHO HỆ THỐNG                 ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

// ============================================================================
// 1. TẠO THÊM HỌC VIÊN
// ============================================================================
echo "🎓 Tạo thêm học viên...\n";

$students = [
    [
        'name' => 'Trần Thị Hương',
        'email' => 'hocvien2@gmail.com',
        'level' => 'intermediate',
        'interests' => 'Tiếng Anh giao tiếp, du lịch'
    ],
    [
        'name' => 'Lê Văn Nam',
        'email' => 'hocvien3@gmail.com',
        'level' => 'beginner',
        'interests' => 'Tiếng Nhật, văn hóa Nhật Bản'
    ],
];

foreach ($students as $studentData) {
    $existingUser = User::where('email', $studentData['email'])->first();
    
    if (!$existingUser) {
        $user = User::create([
            'name' => $studentData['name'],
            'email' => $studentData['email'],
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);
        
        Student::create([
            'user_id' => $user->id,
            'level' => $studentData['level'],
            'interests' => $studentData['interests'],
        ]);
        
        echo "   ✅ Tạo học viên: {$studentData['name']} ({$studentData['email']})\n";
    } else {
        echo "   ℹ️  Học viên {$studentData['email']} đã tồn tại\n";
    }
}

echo "\n";

// ============================================================================
// 2. TẠO ENROLLMENTS MỚI (MỘT SỐ PENDING, MỘT SỐ APPROVED)
// ============================================================================
echo "📝 Tạo đơn đăng ký học...\n";

$allStudents = Student::all();
$allClasses = ClassModel::all();

if ($allStudents->count() < 2) {
    echo "   ⚠️  Cần ít nhất 2 học viên để tạo demo data\n\n";
} else {
    // Create some pending enrollments (chờ admin duyệt)
    $student1 = $allStudents->get(0);
    $student2 = $allStudents->count() > 1 ? $allStudents->get(1) : null;
    $student3 = $allStudents->count() > 2 ? $allStudents->get(2) : null;
    
    // Check if classes exist
    if ($allClasses->count() < 2) {
        echo "   ⚠️  Cần ít nhất 2 lớp học để tạo demo data\n\n";
    } else {
        $class1 = $allClasses->get(0);
        $class2 = $allClasses->get(1);
        
        // Student 1 enrollment to class 2 (PENDING - chờ duyệt)
        $enroll1 = Enrollment::where('student_id', $student1->id)
            ->where('class_id', $class2->id)
            ->first();
        
        if (!$enroll1) {
            Enrollment::create([
                'student_id' => $student1->id,
                'class_id' => $class2->id,
                'enrollment_date' => now(),
                'status' => 'pending',
            ]);
            echo "   ✅ Tạo đơn PENDING: {$student1->user->name} → {$class2->name}\n";
        } else {
            echo "   ℹ️  Enrollment đã tồn tại (ID: {$enroll1->id})\n";
        }
        
        // Student 2 enrollments (nếu có)
        if ($student2) {
            // To class 1 (PENDING)
            $enroll2 = Enrollment::where('student_id', $student2->id)
                ->where('class_id', $class1->id)
                ->first();
            
            if (!$enroll2) {
                Enrollment::create([
                    'student_id' => $student2->id,
                    'class_id' => $class1->id,
                    'enrollment_date' => now()->subDays(1),
                    'status' => 'pending',
                ]);
                echo "   ✅ Tạo đơn PENDING: {$student2->user->name} → {$class1->name}\n";
            }
            
            // To class 2 (APPROVED - đã duyệt)
            $enroll3 = Enrollment::where('student_id', $student2->id)
                ->where('class_id', $class2->id)
                ->first();
            
            if (!$enroll3) {
                Enrollment::create([
                    'student_id' => $student2->id,
                    'class_id' => $class2->id,
                    'enrollment_date' => now()->subDays(2),
                    'status' => 'approved',
                ]);
                
                // Update class enrollment count
                $class2->current_enrollment = Enrollment::where('class_id', $class2->id)
                    ->where('status', 'approved')
                    ->count();
                $class2->save();
                
                echo "   ✅ Tạo đơn APPROVED: {$student2->user->name} → {$class2->name}\n";
            }
        }
        
        // Student 3 enrollment (nếu có)
        if ($student3) {
            $enroll4 = Enrollment::where('student_id', $student3->id)
                ->where('class_id', $class1->id)
                ->first();
            
            if (!$enroll4) {
                Enrollment::create([
                    'student_id' => $student3->id,
                    'class_id' => $class1->id,
                    'enrollment_date' => now()->subHours(3),
                    'status' => 'pending',
                ]);
                echo "   ✅ Tạo đơn PENDING: {$student3->user->name} → {$class1->name}\n";
            }
        }
    }
}

echo "\n";

// ============================================================================
// 3. TẠO SCHEDULES CHO CÁC LỚP (NẾU CHƯA CÓ)
// ============================================================================
echo "📅 Tạo lịch học cho các lớp...\n";

foreach ($allClasses as $class) {
    $existingSchedules = Schedule::where('class_id', $class->id)->count();
    
    if ($existingSchedules === 0) {
        echo "   Tạo lịch cho lớp: {$class->name}\n";
        
        // Parse weekdays
        $weekdays = explode(',', $class->weekdays ?? '2,4,6'); // Default Mon, Wed, Fri
        $startDate = Carbon::parse($class->start_date);
        $endDate = Carbon::parse($class->end_date);
        
        $currentDate = $startDate->copy();
        $scheduleCount = 0;
        
        while ($currentDate->lte($endDate) && $scheduleCount < 30) {
            // Check if current day is in weekdays
            $dayOfWeek = $currentDate->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
            // Convert to our format (2=Monday, 3=Tuesday, ..., 8=Sunday)
            $ourFormat = $dayOfWeek === 0 ? 8 : $dayOfWeek + 1;
            
            if (in_array($ourFormat, $weekdays)) {
                // Determine time based on shift
                $startTime = '08:00';
                $endTime = '10:00';
                
                if ($class->shift === 'afternoon') {
                    $startTime = '14:00';
                    $endTime = '16:00';
                } elseif ($class->shift === 'evening') {
                    $startTime = '18:00';
                    $endTime = '20:00';
                }
                
                Schedule::create([
                    'class_id' => $class->id,
                    'date' => $currentDate->format('Y-m-d'),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'room' => 'Phòng ' . rand(101, 105),
                    'topic' => 'Bài ' . ($scheduleCount + 1),
                ]);
                
                $scheduleCount++;
            }
            
            $currentDate->addDay();
        }
        
        echo "      ✅ Đã tạo {$scheduleCount} buổi học\n";
    } else {
        echo "   ℹ️  Lớp {$class->name} đã có {$existingSchedules} buổi học\n";
    }
}

echo "\n";

// ============================================================================
// SUMMARY
// ============================================================================
echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║                            HOÀN TẤT                                  ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

$pendingCount = Enrollment::where('status', 'pending')->count();
$approvedCount = Enrollment::where('status', 'approved')->count();

echo "📊 THỐNG KÊ:\n";
echo "   - Tổng học viên: " . Student::count() . "\n";
echo "   - Tổng giáo viên: " . Teacher::count() . "\n";
echo "   - Tổng lớp học: " . ClassModel::count() . "\n";
echo "   - Đơn đăng ký PENDING (chờ duyệt): {$pendingCount}\n";
echo "   - Đơn đăng ký APPROVED (đã duyệt): {$approvedCount}\n";
echo "   - Tổng buổi học: " . Schedule::count() . "\n\n";

if ($pendingCount > 0) {
    echo "⚠️  CÓ {$pendingCount} ĐƠN ĐĂNG KÝ CHỜ ADMIN DUYỆT!\n\n";
    echo "📍 Cách duyệt:\n";
    echo "   1. Đăng nhập admin: http://127.0.0.1:8000/login\n";
    echo "   2. Email: admin1@admin.com | Password: password\n";
    echo "   3. Vào: Admin Dashboard → Click 'Xem đăng ký mới'\n";
    echo "   4. Hoặc truy cập: http://127.0.0.1:8000/admin/enrollments\n";
    echo "   5. Click nút 'Duyệt' màu xanh trên từng đơn\n\n";
}

echo "🔑 TÀI KHOẢN TEST:\n";
echo "   Admin: admin1@admin.com / password\n";
echo "   Teacher: teacher1@teacher.com / password\n";
echo "   Student 1: hocvien1@gmail.com / password\n";
echo "   Student 2: hocvien2@gmail.com / password\n";
echo "   Student 3: hocvien3@gmail.com / password\n\n";

echo "✅ DỮ LIỆU DEMO ĐÃ SẴN SÀNG!\n\n";
