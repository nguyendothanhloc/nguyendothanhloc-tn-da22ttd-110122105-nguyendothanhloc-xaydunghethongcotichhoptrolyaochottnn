<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║  KIỂM TRA LOGIC GIẢNG VIÊN - HỌC SINH                    ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

// 1. Lấy thông tin Student
$student = App\Models\Student::find(2);
if (!$student) {
    echo "❌ ERROR: Student ID 2 không tồn tại\n";
    exit(1);
}

echo "👨‍🎓 STUDENT:\n";
echo "   Name: {$student->user->name}\n";
echo "   Email: {$student->user->email}\n";
echo "   ID: {$student->id}\n\n";

// 2. Lấy enrollments của student
$enrollments = $student->enrollments()->with(['class.teacher.user', 'class.course'])->get();
echo "📚 ENROLLMENTS: {$enrollments->count()}\n";

$classIds = [];
foreach ($enrollments as $e) {
    echo "\n   Enrollment ID: {$e->id}\n";
    echo "   ├─ Class: {$e->class->name} (ID: {$e->class_id})\n";
    echo "   ├─ Course: {$e->class->course->name}\n";
    echo "   ├─ Status: {$e->status}\n";
    echo "   └─ Teacher: {$e->class->teacher->user->name} (ID: {$e->class->teacher_id})\n";
    
    $classIds[] = $e->class_id;
}

echo "\n" . str_repeat("─", 60) . "\n\n";

// 3. Kiểm tra từng Teacher xem có thấy student này không
foreach ($enrollments as $e) {
    $teacher = $e->class->teacher;
    $class = $e->class;
    
    echo "👨‍🏫 TEACHER: {$teacher->user->name}\n";
    echo "   Class: {$class->name} (ID: {$class->id})\n\n";
    
    // Check 1: Teacher's classes
    echo "   ✓ CHECK 1: Teacher có thấy class này không?\n";
    $teacherClasses = App\Models\ClassModel::where('teacher_id', $teacher->id)
        ->where('id', $class->id)
        ->count();
    echo "     → " . ($teacherClasses > 0 ? "✅ CÓ" : "❌ KHÔNG") . "\n\n";
    
    // Check 2: Students in class (from Enrollment)
    echo "   ✓ CHECK 2: Teacher có thấy student trong class không?\n";
    
    // Method 1: Query như AssessmentController
    $studentsMethod1 = App\Models\Enrollment::where('class_id', $class->id)
        ->whereIn('status', ['paid', 'approved', 'pending'])
        ->with('student.user')
        ->get();
    echo "     Method 1 (whereIn paid/approved/pending): {$studentsMethod1->count()} students\n";
    foreach ($studentsMethod1 as $enr) {
        echo "       - {$enr->student->user->name} (Status: {$enr->status})\n";
    }
    
    // Method 2: Old query (nếu có filter 'approved' only)
    $studentsMethod2 = App\Models\Enrollment::where('class_id', $class->id)
        ->where('status', 'approved')
        ->with('student.user')
        ->get();
    echo "     Method 2 (only 'approved'): {$studentsMethod2->count()} students\n";
    foreach ($studentsMethod2 as $enr) {
        echo "       - {$enr->student->user->name} (Status: {$enr->status})\n";
    }
    
    // Check 3: Schedules
    echo "\n   ✓ CHECK 3: Class có schedules không?\n";
    $scheduleCount = App\Models\Schedule::where('class_id', $class->id)->count();
    echo "     → " . ($scheduleCount > 0 ? "✅ CÓ: {$scheduleCount} schedules" : "❌ KHÔNG") . "\n\n";
    
    // Check 4: Assessments
    echo "   ✓ CHECK 4: Class có assessments không?\n";
    $assessmentCount = App\Models\Assessment::where('class_id', $class->id)->count();
    echo "     → " . ($assessmentCount > 0 ? "✅ CÓ: {$assessmentCount} assessments" : "⚠️  KHÔNG") . "\n\n";
    
    echo str_repeat("─", 60) . "\n\n";
}

// 4. Tổng kết
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║  PHÂN TÍCH VẤN ĐỀ                                         ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

echo "Nếu Teacher KHÔNG thấy student, nguyên nhân có thể là:\n\n";
echo "1. ❌ AssessmentController filter 'approved' only\n";
echo "   → Cần sửa thành whereIn(['paid', 'approved', 'pending'])\n\n";
echo "2. ❌ AttendanceController filter 'approved' only\n";
echo "   → Cần sửa thành whereIn(['paid', 'approved', 'pending'])\n\n";
echo "3. ❌ ClassController filter 'approved' only\n";
echo "   → Cần sửa thành whereIn(['paid', 'approved', 'pending'])\n\n";

echo "Hãy kiểm tra các file Controller sau:\n";
echo "  - app/Http/Controllers/AssessmentController.php\n";
echo "  - app/Http/Controllers/AttendanceController.php\n";
echo "  - app/Http/Controllers/ClassController.php\n";
echo "  - app/Http/Controllers/TeacherController.php\n\n";
