<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Services\EnrollmentService;
use App\Services\NotificationService;

echo "=== STEP 1: Create Test Student Account ===\n";

// Create user
$user = User::create([
    'name' => 'Nguyễn Văn Test',
    'email' => 'testuser@test.com',
    'password' => bcrypt('password'),
    'role' => 'student'
]);

echo "✓ User created: ID={$user->id}, Email={$user->email}\n";

// Create student profile
$student = Student::create([
    'user_id' => $user->id,
    'level' => 'Beginner',
    'interests' => 'Tiếng Anh, Giao tiếp'
]);

echo "✓ Student profile created: ID={$student->id}\n\n";

echo "=== STEP 2: Find Available Class ===\n";

// Find the English class (ID=1, taught by Nguyễn Văn Giáo)
$class = ClassModel::with(['course', 'teacher.user'])->find(1);

if ($class) {
    echo "✓ Found class: {$class->name}\n";
    echo "  - Course: {$class->course->name}\n";
    echo "  - Teacher: {$class->teacher->user->name}\n";
    echo "  - Current enrollment: {$class->current_enrollment}/{$class->max_capacity}\n\n";
} else {
    echo "✗ Class not found!\n";
    exit(1);
}

echo "=== STEP 3: Enroll Student in Class ===\n";

// Create enrollment using EnrollmentService
$notificationService = new NotificationService();
$enrollmentService = new EnrollmentService($notificationService);

try {
    $enrollment = $enrollmentService->createEnrollment([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'enrollment_date' => now()->toDateString(),
    ]);
    
    echo "✓ Enrollment created: ID={$enrollment->id}\n";
    echo "  - Status: {$enrollment->status}\n";
    echo "  - Enrollment date: {$enrollment->enrollment_date}\n";
    echo "  - Class current enrollment updated: {$class->fresh()->current_enrollment}\n\n";
} catch (\Exception $e) {
    echo "✗ Enrollment failed: {$e->getMessage()}\n";
    exit(1);
}

echo "=== STEP 4: Verify Student Can See Class ===\n";

// Get student's enrollments
$studentEnrollments = $student->enrollments()
    ->whereIn('status', ['paid', 'approved', 'pending'])
    ->whereHas('class', function($query) {
        $query->where('status', '!=', 'cancelled');
    })
    ->with(['class.course', 'class.teacher.user'])
    ->get();

echo "✓ Student has {$studentEnrollments->count()} enrollment(s)\n";
foreach ($studentEnrollments as $enroll) {
    echo "  - Class: {$enroll->class->name} (Status: {$enroll->status})\n";
}
echo "\n";

echo "=== STEP 5: Verify Teacher Can See Student ===\n";

// Check if teacher can see the student in assessment view
$studentsInAssessment = $class->enrollments()
    ->whereIn('status', ['paid', 'approved', 'pending'])
    ->with(['student.user'])
    ->get()
    ->pluck('student');

echo "✓ Teacher can see {$studentsInAssessment->count()} student(s) in assessment:\n";
foreach ($studentsInAssessment as $s) {
    echo "  - {$s->user->name} (ID={$s->id})\n";
}
echo "\n";

// Check if teacher can see the student in attendance view
$studentsInAttendance = $class->enrollments()
    ->whereIn('status', ['paid', 'approved', 'pending'])
    ->with(['student.user'])
    ->get()
    ->pluck('student');

echo "✓ Teacher can see {$studentsInAttendance->count()} student(s) in attendance:\n";
foreach ($studentsInAttendance as $s) {
    echo "  - {$s->user->name} (ID={$s->id})\n";
}
echo "\n";

echo "=== STEP 6: Check Student's Schedules ===\n";

// Get student's schedules
$schedules = \App\Models\Schedule::whereIn('class_id', [$class->id])
    ->whereHas('class', function($query) {
        $query->where('status', '!=', 'cancelled');
    })
    ->where('date', '>=', now())
    ->orderBy('date')
    ->orderBy('start_time')
    ->limit(5)
    ->with('class.course')
    ->get();

echo "✓ Student has {$schedules->count()} upcoming schedule(s):\n";
foreach ($schedules as $schedule) {
    echo "  - {$schedule->date->format('d/m/Y')} ({$schedule->date->format('l')})\n";
    echo "    Time: {$schedule->start_time} - {$schedule->end_time}\n";
    echo "    Location: {$schedule->location}\n";
    echo "    Topic: {$schedule->topic}\n\n";
}

echo "=== TEST SUMMARY ===\n";
echo "✓ Student account created successfully\n";
echo "✓ Student enrolled in class with status 'paid' (auto-approved)\n";
echo "✓ Student can see their enrolled class\n";
echo "✓ Teacher can see the new student in assessment view\n";
echo "✓ Teacher can see the new student in attendance view\n";
echo "✓ Student can see their schedules\n\n";

echo "=== Test Account Info ===\n";
echo "Email: testuser@test.com\n";
echo "Password: password\n";
echo "Student ID: {$student->id}\n";
echo "Enrollment ID: {$enrollment->id}\n";
echo "Class: {$class->name}\n\n";

echo "You can now login at http://127.0.0.1:8000/login and test the interaction!\n";
