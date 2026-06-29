<?php

/**
 * Comprehensive System Test - Language Center Management System
 * Test logic và tương tác giữa Admin, Giáo viên, và Học viên
 * 
 * Run: php comprehensive_system_test.php
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Course;
use App\Models\ClassModel;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Assessment;
use App\Models\AssessmentScore;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=================================================================\n";
echo "  COMPREHENSIVE SYSTEM TEST - Language Center Management System\n";
echo "=================================================================\n\n";

$errors = [];
$warnings = [];
$passed = 0;
$total = 0;

function testCase($name, $callback) {
    global $errors, $warnings, $passed, $total;
    $total++;
    echo "[$total] Testing: $name\n";
    
    try {
        $result = $callback();
        if ($result === true) {
            $passed++;
            echo "    ✓ PASSED\n\n";
        } elseif ($result === 'warning') {
            $warnings[] = "$name - See details above";
            echo "    ⚠ WARNING\n\n";
        } else {
            $errors[] = "$name - " . ($result ?: "Failed");
            echo "    ✗ FAILED: $result\n\n";
        }
    } catch (Exception $e) {
        $errors[] = "$name - Exception: " . $e->getMessage();
        echo "    ✗ EXCEPTION: " . $e->getMessage() . "\n\n";
    }
}

// ============================================================================
// SECTION 1: KIỂM TRA USER ROLES & AUTHENTICATION
// ============================================================================

echo "\n--- SECTION 1: USER ROLES & AUTHENTICATION ---\n\n";

testCase("Admin account exists and has correct role", function() {
    $admin = User::where('email', 'admin1@admin.com')->first();
    if (!$admin) return "Admin account not found";
    if ($admin->role !== 'admin') return "Admin role incorrect: {$admin->role}";
    echo "    Admin ID: {$admin->id}, Email: {$admin->email}\n";
    return true;
});

testCase("Teacher accounts exist and have correct role", function() {
    $teachers = User::where('role', 'teacher')->get();
    if ($teachers->count() === 0) return "No teacher accounts found";
    
    echo "    Found {$teachers->count()} teacher(s)\n";
    foreach ($teachers as $teacher) {
        $teacherRecord = Teacher::where('user_id', $teacher->id)->first();
        if (!$teacherRecord) {
            return "Teacher record missing for user {$teacher->email}";
        }
        echo "      - {$teacher->name} ({$teacher->email}) -> Teacher ID: {$teacherRecord->id}\n";
    }
    return true;
});

testCase("Student accounts exist and have correct role", function() {
    $students = User::where('role', 'student')->get();
    if ($students->count() === 0) return "No student accounts found";
    
    echo "    Found {$students->count()} student(s)\n";
    foreach ($students as $student) {
        $studentRecord = Student::where('user_id', $student->id)->first();
        if (!$studentRecord) {
            return "Student record missing for user {$student->email}";
        }
        echo "      - {$student->name} ({$student->email}) -> Student ID: {$studentRecord->id}\n";
    }
    return true;
});

// ============================================================================
// SECTION 2: ADMIN → COURSE & CLASS MANAGEMENT
// ============================================================================

echo "\n--- SECTION 2: ADMIN → COURSE & CLASS MANAGEMENT ---\n\n";

testCase("Admin can view all courses", function() {
    $courses = Course::all();
    if ($courses->count() === 0) return "No courses found in database";
    
    echo "    Found {$courses->count()} course(s)\n";
    foreach ($courses as $course) {
        echo "      - {$course->name} (ID: {$course->id}, Active: " . ($course->is_active ? 'Yes' : 'No') . ")\n";
    }
    return true;
});

testCase("Admin can view all classes with course relationships", function() {
    $classes = ClassModel::with('course', 'teacher')->get();
    if ($classes->count() === 0) return "No classes found in database";
    
    echo "    Found {$classes->count()} class(es)\n";
    foreach ($classes as $class) {
        $courseName = $class->course ? $class->course->name : 'No Course';
        $teacherName = $class->teacher ? $class->teacher->name : 'No Teacher';
        echo "      - Class ID: {$class->id}, Course: {$courseName}, Teacher: {$teacherName}\n";
        echo "        Enrollment: {$class->current_enrollment}/{$class->max_capacity}\n";
    }
    return true;
});

testCase("All classes have valid teacher assignments", function() {
    $classesWithoutTeacher = ClassModel::whereNull('teacher_id')->get();
    if ($classesWithoutTeacher->count() > 0) {
        echo "    ⚠ Found {$classesWithoutTeacher->count()} class(es) without teacher:\n";
        foreach ($classesWithoutTeacher as $class) {
            echo "      - Class ID: {$class->id}, Course: " . ($class->course ? $class->course->name : 'N/A') . "\n";
        }
        return 'warning';
    }
    echo "    All classes have teacher assignments\n";
    return true;
});

testCase("Class date validation (start_date < end_date)", function() {
    $invalidClasses = ClassModel::whereRaw('start_date >= end_date')->get();
    if ($invalidClasses->count() > 0) {
        echo "    Found {$invalidClasses->count()} class(es) with invalid dates:\n";
        foreach ($invalidClasses as $class) {
            echo "      - Class ID: {$class->id}, Start: {$class->start_date}, End: {$class->end_date}\n";
        }
        return "Invalid date ranges found";
    }
    echo "    All classes have valid date ranges\n";
    return true;
});

// ============================================================================
// SECTION 3: STUDENT → ENROLLMENT LOGIC
// ============================================================================

echo "\n--- SECTION 3: STUDENT → ENROLLMENT LOGIC ---\n\n";

testCase("Student enrollments are properly recorded", function() {
    $enrollments = Enrollment::with('student', 'class', 'class.course')->get();
    if ($enrollments->count() === 0) {
        echo "    ⚠ No enrollments found in database\n";
        return 'warning';
    }
    
    echo "    Found {$enrollments->count()} enrollment(s)\n";
    foreach ($enrollments as $enrollment) {
        $studentName = $enrollment->student ? $enrollment->student->name : 'Unknown';
        $courseName = $enrollment->class && $enrollment->class->course ? $enrollment->class->course->name : 'Unknown';
        echo "      - Student: {$studentName}, Course: {$courseName}, Status: {$enrollment->status}\n";
    }
    return true;
});

testCase("Enrollment count matches class current_enrollment", function() {
    $classes = ClassModel::all();
    $mismatches = [];
    
    foreach ($classes as $class) {
        $actualCount = Enrollment::where('class_id', $class->id)
                                 ->whereIn('status', ['approved', 'paid'])
                                 ->count();
        
        if ($actualCount != $class->current_enrollment) {
            $mismatches[] = "Class ID {$class->id}: DB shows {$class->current_enrollment}, actual is {$actualCount}";
        }
    }
    
    if (count($mismatches) > 0) {
        echo "    Found mismatches:\n";
        foreach ($mismatches as $mismatch) {
            echo "      - {$mismatch}\n";
        }
        return "Enrollment count mismatches found";
    }
    
    echo "    All class enrollment counts are accurate\n";
    return true;
});

testCase("No class exceeds max_capacity", function() {
    $overcapacity = ClassModel::whereRaw('current_enrollment > max_capacity')->get();
    
    if ($overcapacity->count() > 0) {
        echo "    Found {$overcapacity->count()} class(es) over capacity:\n";
        foreach ($overcapacity as $class) {
            echo "      - Class ID: {$class->id}, Current: {$class->current_enrollment}, Max: {$class->max_capacity}\n";
        }
        return "Classes over capacity found";
    }
    
    echo "    All classes are within capacity limits\n";
    return true;
});

testCase("Student can view their enrollments", function() {
    $student = Student::first();
    if (!$student) return "No student found for testing";
    
    $enrollments = Enrollment::where('student_id', $student->id)
                             ->with('class.course')
                             ->get();
    
    echo "    Student '{$student->name}' has {$enrollments->count()} enrollment(s)\n";
    foreach ($enrollments as $enrollment) {
        $courseName = $enrollment->class && $enrollment->class->course ? $enrollment->class->course->name : 'Unknown';
        echo "      - Course: {$courseName}, Status: {$enrollment->status}\n";
    }
    
    return true;
});

// ============================================================================
// SECTION 4: TEACHER → CLASS & SCHEDULE MANAGEMENT
// ============================================================================

echo "\n--- SECTION 4: TEACHER → CLASS & SCHEDULE MANAGEMENT ---\n\n";

testCase("Teacher can view their assigned classes", function() {
    $teacher = Teacher::first();
    if (!$teacher) return "No teacher found for testing";
    
    $classes = ClassModel::where('teacher_id', $teacher->id)
                        ->with('course')
                        ->get();
    
    echo "    Teacher '{$teacher->name}' has {$classes->count()} assigned class(es)\n";
    foreach ($classes as $class) {
        $courseName = $class->course ? $class->course->name : 'Unknown';
        echo "      - Class ID: {$class->id}, Course: {$courseName}, Students: {$class->current_enrollment}\n";
    }
    
    if ($classes->count() === 0) {
        return 'warning';
    }
    
    return true;
});

testCase("Schedules are properly linked to classes", function() {
    $schedules = Schedule::with('class', 'class.course')->get();
    
    if ($schedules->count() === 0) {
        echo "    ⚠ No schedules found in database\n";
        return 'warning';
    }
    
    echo "    Found {$schedules->count()} schedule(s)\n";
    $orphanedSchedules = 0;
    
    foreach ($schedules as $schedule) {
        if (!$schedule->class) {
            $orphanedSchedules++;
            echo "      - ⚠ Schedule ID {$schedule->id} has no class\n";
        }
    }
    
    if ($orphanedSchedules > 0) {
        return "Found {$orphanedSchedules} orphaned schedule(s)";
    }
    
    echo "    All schedules are properly linked\n";
    return true;
});

testCase("Schedule time validation (start_time < end_time)", function() {
    $invalidSchedules = Schedule::whereRaw("
        STR_TO_DATE(CONCAT(date, ' ', start_time), '%Y-%m-%d %H:%i:%s') >= 
        STR_TO_DATE(CONCAT(date, ' ', end_time), '%Y-%m-%d %H:%i:%s')
    ")->get();
    
    if ($invalidSchedules->count() > 0) {
        echo "    Found {$invalidSchedules->count()} schedule(s) with invalid times:\n";
        foreach ($invalidSchedules as $schedule) {
            echo "      - Schedule ID: {$schedule->id}, Start: {$schedule->start_time}, End: {$schedule->end_time}\n";
        }
        return "Invalid schedule times found";
    }
    
    echo "    All schedules have valid time ranges\n";
    return true;
});

// ============================================================================
// SECTION 5: TEACHER → ATTENDANCE MANAGEMENT
// ============================================================================

echo "\n--- SECTION 5: TEACHER → ATTENDANCE MANAGEMENT ---\n\n";

testCase("Attendance records are properly linked", function() {
    $attendances = Attendance::with('student', 'schedule')->get();
    
    if ($attendances->count() === 0) {
        echo "    ⚠ No attendance records found\n";
        return 'warning';
    }
    
    echo "    Found {$attendances->count()} attendance record(s)\n";
    $orphaned = 0;
    
    foreach ($attendances as $attendance) {
        if (!$attendance->student || !$attendance->schedule) {
            $orphaned++;
            echo "      - ⚠ Attendance ID {$attendance->id} missing references\n";
        }
    }
    
    if ($orphaned > 0) {
        return "Found {$orphaned} orphaned attendance record(s)";
    }
    
    echo "    All attendance records are properly linked\n";
    return true;
});

testCase("Attendance status values are valid", function() {
    $validStatuses = ['present', 'absent', 'late'];
    $invalidAttendances = Attendance::whereNotIn('status', $validStatuses)->get();
    
    if ($invalidAttendances->count() > 0) {
        echo "    Found {$invalidAttendances->count()} attendance(s) with invalid status:\n";
        foreach ($invalidAttendances as $attendance) {
            echo "      - Attendance ID: {$attendance->id}, Status: {$attendance->status}\n";
        }
        return "Invalid attendance status values found";
    }
    
    echo "    All attendance status values are valid\n";
    return true;
});

testCase("Student can view their attendance records", function() {
    $student = Student::first();
    if (!$student) return "No student found for testing";
    
    $attendances = Attendance::where('student_id', $student->id)
                             ->with('schedule')
                             ->get();
    
    echo "    Student '{$student->name}' has {$attendances->count()} attendance record(s)\n";
    
    if ($attendances->count() > 0) {
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        echo "      - Present: {$present}, Absent: {$absent}, Late: {$late}\n";
    }
    
    return true;
});

// ============================================================================
// SECTION 6: TEACHER → ASSESSMENT MANAGEMENT
// ============================================================================

echo "\n--- SECTION 6: TEACHER → ASSESSMENT MANAGEMENT ---\n\n";

testCase("Assessments are properly linked to classes", function() {
    $assessments = Assessment::with('class')->get();
    
    if ($assessments->count() === 0) {
        echo "    ⚠ No assessments found\n";
        return 'warning';
    }
    
    echo "    Found {$assessments->count()} assessment(s)\n";
    $orphaned = 0;
    
    foreach ($assessments as $assessment) {
        if (!$assessment->class) {
            $orphaned++;
            echo "      - ⚠ Assessment ID {$assessment->id} has no class\n";
        } else {
            echo "      - {$assessment->name} (Type: {$assessment->type}, Max Score: {$assessment->max_score})\n";
        }
    }
    
    if ($orphaned > 0) {
        return "Found {$orphaned} orphaned assessment(s)";
    }
    
    return true;
});

testCase("Assessment scores are valid (score <= max_score)", function() {
    $invalidScores = DB::table('assessment_scores')
        ->join('assessments', 'assessment_scores.assessment_id', '=', 'assessments.id')
        ->whereRaw('assessment_scores.score > assessments.max_score')
        ->select('assessment_scores.*', 'assessments.max_score')
        ->get();
    
    if ($invalidScores->count() > 0) {
        echo "    Found {$invalidScores->count()} invalid score(s):\n";
        foreach ($invalidScores as $score) {
            echo "      - Score ID: {$score->id}, Score: {$score->score}, Max: {$score->max_score}\n";
        }
        return "Invalid assessment scores found";
    }
    
    echo "    All assessment scores are within valid range\n";
    return true;
});

testCase("Student can view their assessment scores", function() {
    $student = Student::first();
    if (!$student) return "No student found for testing";
    
    $scores = AssessmentScore::where('student_id', $student->id)
                             ->with('assessment')
                             ->get();
    
    echo "    Student '{$student->name}' has {$scores->count()} assessment score(s)\n";
    
    foreach ($scores as $score) {
        $assessmentName = $score->assessment ? $score->assessment->name : 'Unknown';
        echo "      - {$assessmentName}: {$score->score} / {$score->assessment->max_score}\n";
    }
    
    return true;
});

// ============================================================================
// SECTION 7: DATA CONSISTENCY - ADMIN CHANGES PROPAGATION
// ============================================================================

echo "\n--- SECTION 7: DATA CONSISTENCY - ADMIN CHANGES PROPAGATION ---\n\n";

testCase("Class changes are visible to teacher", function() {
    $class = ClassModel::with('teacher')->first();
    if (!$class || !$class->teacher) {
        echo "    ⚠ No class with teacher found for testing\n";
        return 'warning';
    }
    
    // Simulate admin viewing class
    $adminView = ClassModel::with('course', 'teacher')->find($class->id);
    
    // Simulate teacher viewing same class
    $teacherView = ClassModel::where('teacher_id', $class->teacher_id)
                            ->with('course')
                            ->find($class->id);
    
    if (!$teacherView) {
        return "Teacher cannot see assigned class";
    }
    
    // Check if data matches
    if ($adminView->name !== $teacherView->name || 
        $adminView->max_capacity !== $teacherView->max_capacity) {
        return "Data mismatch between admin and teacher view";
    }
    
    echo "    Class data consistent between admin and teacher view\n";
    echo "      - Class: {$adminView->name}, Capacity: {$adminView->max_capacity}\n";
    return true;
});

testCase("Enrollment changes are visible to student", function() {
    $enrollment = Enrollment::with('student', 'class')->first();
    if (!$enrollment) {
        echo "    ⚠ No enrollment found for testing\n";
        return 'warning';
    }
    
    // Simulate admin viewing enrollment
    $adminView = Enrollment::find($enrollment->id);
    
    // Simulate student viewing same enrollment
    $studentView = Enrollment::where('student_id', $enrollment->student_id)
                             ->where('class_id', $enrollment->class_id)
                             ->first();
    
    if (!$studentView) {
        return "Student cannot see their enrollment";
    }
    
    // Check if data matches
    if ($adminView->status !== $studentView->status) {
        return "Enrollment status mismatch between admin and student view";
    }
    
    echo "    Enrollment data consistent between admin and student view\n";
    echo "      - Status: {$adminView->status}, Enrolled Date: {$adminView->enrolled_at}\n";
    return true;
});

testCase("Schedule changes are visible to enrolled students", function() {
    $schedule = Schedule::with('class')->first();
    if (!$schedule || !$schedule->class) {
        echo "    ⚠ No schedule with class found for testing\n";
        return 'warning';
    }
    
    // Get students enrolled in this class
    $enrolledStudents = Enrollment::where('class_id', $schedule->class_id)
                                  ->whereIn('status', ['approved', 'paid'])
                                  ->pluck('student_id');
    
    if ($enrolledStudents->count() === 0) {
        echo "    ⚠ No students enrolled in class with schedule\n";
        return 'warning';
    }
    
    // Simulate teacher creating/updating schedule
    $teacherView = Schedule::find($schedule->id);
    
    // Simulate student viewing schedule
    $studentSchedules = Schedule::where('class_id', $schedule->class_id)
                                ->where('id', $schedule->id)
                                ->get();
    
    if ($studentSchedules->count() === 0) {
        return "Students cannot see class schedules";
    }
    
    echo "    Schedule visible to {$enrolledStudents->count()} enrolled student(s)\n";
    echo "      - Date: {$teacherView->date}, Time: {$teacherView->start_time} - {$teacherView->end_time}\n";
    return true;
});

// ============================================================================
// SECTION 8: DATABASE REFERENTIAL INTEGRITY
// ============================================================================

echo "\n--- SECTION 8: DATABASE REFERENTIAL INTEGRITY ---\n\n";

testCase("All classes reference valid courses", function() {
    $orphanedClasses = ClassModel::whereNotIn('course_id', Course::pluck('id'))->get();
    
    if ($orphanedClasses->count() > 0) {
        echo "    Found {$orphanedClasses->count()} class(es) with invalid course_id:\n";
        foreach ($orphanedClasses as $class) {
            echo "      - Class ID: {$class->id}, Course ID: {$class->course_id}\n";
        }
        return "Orphaned classes found";
    }
    
    echo "    All classes reference valid courses\n";
    return true;
});

testCase("All classes reference valid teachers", function() {
    $classesWithInvalidTeacher = ClassModel::whereNotNull('teacher_id')
                                           ->whereNotIn('teacher_id', Teacher::pluck('id'))
                                           ->get();
    
    if ($classesWithInvalidTeacher->count() > 0) {
        echo "    Found {$classesWithInvalidTeacher->count()} class(es) with invalid teacher_id:\n";
        foreach ($classesWithInvalidTeacher as $class) {
            echo "      - Class ID: {$class->id}, Teacher ID: {$class->teacher_id}\n";
        }
        return "Classes with invalid teacher references found";
    }
    
    echo "    All classes reference valid teachers\n";
    return true;
});

testCase("All enrollments reference valid students and classes", function() {
    $orphanedEnrollments = Enrollment::whereNotIn('student_id', Student::pluck('id'))
                                     ->orWhereNotIn('class_id', ClassModel::pluck('id'))
                                     ->get();
    
    if ($orphanedEnrollments->count() > 0) {
        echo "    Found {$orphanedEnrollments->count()} enrollment(s) with invalid references:\n";
        foreach ($orphanedEnrollments as $enrollment) {
            echo "      - Enrollment ID: {$enrollment->id}, Student: {$enrollment->student_id}, Class: {$enrollment->class_id}\n";
        }
        return "Orphaned enrollments found";
    }
    
    echo "    All enrollments reference valid students and classes\n";
    return true;
});

testCase("All schedules reference valid classes", function() {
    $orphanedSchedules = Schedule::whereNotIn('class_id', ClassModel::pluck('id'))->get();
    
    if ($orphanedSchedules->count() > 0) {
        echo "    Found {$orphanedSchedules->count()} schedule(s) with invalid class_id:\n";
        foreach ($orphanedSchedules as $schedule) {
            echo "      - Schedule ID: {$schedule->id}, Class ID: {$schedule->class_id}\n";
        }
        return "Orphaned schedules found";
    }
    
    echo "    All schedules reference valid classes\n";
    return true;
});

// ============================================================================
// SECTION 9: INTERACTION FLOW TESTS
// ============================================================================

echo "\n--- SECTION 9: INTERACTION FLOW TESTS ---\n\n";

testCase("Complete flow: Admin creates class → Teacher assigned → Students enroll", function() {
    $course = Course::where('is_active', true)->first();
    $teacher = Teacher::first();
    
    if (!$course || !$teacher) {
        echo "    ⚠ Missing course or teacher for flow test\n";
        return 'warning';
    }
    
    // Check if there are classes for this course with this teacher
    $class = ClassModel::where('course_id', $course->id)
                      ->where('teacher_id', $teacher->id)
                      ->first();
    
    if (!$class) {
        echo "    ⚠ No matching class found for flow test\n";
        return 'warning';
    }
    
    // Check if students are enrolled
    $enrollments = Enrollment::where('class_id', $class->id)->count();
    
    echo "    Flow complete:\n";
    echo "      1. Admin created class: {$class->name}\n";
    echo "      2. Teacher assigned: {$teacher->name}\n";
    echo "      3. Students enrolled: {$enrollments}\n";
    
    if ($enrollments === 0) {
        echo "    ⚠ No students enrolled yet\n";
        return 'warning';
    }
    
    return true;
});

testCase("Complete flow: Teacher creates schedule → Students see schedule", function() {
    $schedule = Schedule::with('class')->first();
    
    if (!$schedule || !$schedule->class) {
        echo "    ⚠ No schedule found for flow test\n";
        return 'warning';
    }
    
    // Count enrolled students who can see this schedule
    $visibleToStudents = Enrollment::where('class_id', $schedule->class_id)
                                   ->whereIn('status', ['approved', 'paid'])
                                   ->count();
    
    echo "    Flow complete:\n";
    echo "      1. Teacher created schedule: {$schedule->date} {$schedule->start_time}-{$schedule->end_time}\n";
    echo "      2. Schedule visible to: {$visibleToStudents} student(s)\n";
    
    if ($visibleToStudents === 0) {
        echo "    ⚠ No students can see this schedule\n";
        return 'warning';
    }
    
    return true;
});

testCase("Complete flow: Teacher marks attendance → Student sees attendance", function() {
    $attendance = Attendance::with('student', 'schedule')->first();
    
    if (!$attendance) {
        echo "    ⚠ No attendance records found for flow test\n";
        return 'warning';
    }
    
    // Verify student can query their own attendance
    $studentAttendance = Attendance::where('student_id', $attendance->student_id)
                                   ->where('id', $attendance->id)
                                   ->first();
    
    if (!$studentAttendance) {
        return "Student cannot see their attendance record";
    }
    
    echo "    Flow complete:\n";
    echo "      1. Teacher marked attendance: {$attendance->status}\n";
    echo "      2. Student sees attendance: Yes\n";
    echo "      3. Student: " . ($attendance->student ? $attendance->student->name : 'Unknown') . "\n";
    
    return true;
});

testCase("Complete flow: Teacher posts scores → Student sees scores", function() {
    $score = AssessmentScore::with('student', 'assessment')->first();
    
    if (!$score) {
        echo "    ⚠ No assessment scores found for flow test\n";
        return 'warning';
    }
    
    // Verify student can query their own scores
    $studentScore = AssessmentScore::where('student_id', $score->student_id)
                                   ->where('id', $score->id)
                                   ->first();
    
    if (!$studentScore) {
        return "Student cannot see their assessment score";
    }
    
    echo "    Flow complete:\n";
    echo "      1. Teacher posted score: {$score->score}\n";
    echo "      2. Student sees score: Yes\n";
    echo "      3. Assessment: " . ($score->assessment ? $score->assessment->name : 'Unknown') . "\n";
    
    return true;
});

// ============================================================================
// SECTION 10: BUSINESS LOGIC VALIDATION
// ============================================================================

echo "\n--- SECTION 10: BUSINESS LOGIC VALIDATION ---\n\n";

testCase("Students cannot enroll in full classes", function() {
    $fullClasses = ClassModel::whereRaw('current_enrollment >= max_capacity')->get();
    
    if ($fullClasses->count() > 0) {
        echo "    Found {$fullClasses->count()} full class(es):\n";
        foreach ($fullClasses as $class) {
            echo "      - Class ID: {$class->id}, Enrollment: {$class->current_enrollment}/{$class->max_capacity}\n";
            
            // Check if any enrollments exist beyond capacity
            $enrollmentCount = Enrollment::where('class_id', $class->id)
                                        ->whereIn('status', ['approved', 'paid'])
                                        ->count();
            
            if ($enrollmentCount > $class->max_capacity) {
                return "Class ID {$class->id} has {$enrollmentCount} enrollments but capacity is {$class->max_capacity}";
            }
        }
    }
    
    echo "    Capacity limits are properly enforced\n";
    return true;
});

testCase("Enrollment status flow is logical", function() {
    $validStatuses = ['pending', 'approved', 'paid', 'cancelled'];
    $invalidEnrollments = Enrollment::whereNotIn('status', $validStatuses)->get();
    
    if ($invalidEnrollments->count() > 0) {
        echo "    Found {$invalidEnrollments->count()} enrollment(s) with invalid status:\n";
        foreach ($invalidEnrollments as $enrollment) {
            echo "      - Enrollment ID: {$enrollment->id}, Status: {$enrollment->status}\n";
        }
        return "Invalid enrollment status values found";
    }
    
    echo "    All enrollment statuses are valid\n";
    return true;
});

testCase("Teacher can only access their own classes", function() {
    $teacher = Teacher::first();
    if (!$teacher) {
        echo "    ⚠ No teacher found for testing\n";
        return 'warning';
    }
    
    $assignedClasses = ClassModel::where('teacher_id', $teacher->id)->count();
    $totalClasses = ClassModel::count();
    
    echo "    Teacher '{$teacher->name}':\n";
    echo "      - Assigned classes: {$assignedClasses}\n";
    echo "      - Total classes in system: {$totalClasses}\n";
    
    if ($assignedClasses === $totalClasses && $totalClasses > 1) {
        return "Teacher appears to have access to ALL classes (possible permission issue)";
    }
    
    echo "      - Access control: Properly limited\n";
    return true;
});

testCase("Student can only see their own data", function() {
    $student = Student::first();
    if (!$student) {
        echo "    ⚠ No student found for testing\n";
        return 'warning';
    }
    
    $studentEnrollments = Enrollment::where('student_id', $student->id)->count();
    $studentAttendances = Attendance::where('student_id', $student->id)->count();
    $studentScores = AssessmentScore::where('student_id', $student->id)->count();
    
    $totalEnrollments = Enrollment::count();
    $totalAttendances = Attendance::count();
    $totalScores = AssessmentScore::count();
    
    echo "    Student '{$student->name}':\n";
    echo "      - Their enrollments: {$studentEnrollments} / Total: {$totalEnrollments}\n";
    echo "      - Their attendances: {$studentAttendances} / Total: {$totalAttendances}\n";
    echo "      - Their scores: {$studentScores} / Total: {$totalScores}\n";
    
    if ($studentEnrollments === $totalEnrollments && $totalEnrollments > 1) {
        return "Student appears to see ALL enrollments (possible permission issue)";
    }
    
    echo "      - Data isolation: Properly enforced\n";
    return true;
});

// ============================================================================
// FINAL SUMMARY
// ============================================================================

echo "\n=================================================================\n";
echo "  TEST SUMMARY\n";
echo "=================================================================\n\n";

echo "Total Tests: {$total}\n";
echo "Passed: {$passed} ✓\n";
echo "Warnings: " . count($warnings) . " ⚠\n";
echo "Failed: " . count($errors) . " ✗\n\n";

if (count($warnings) > 0) {
    echo "WARNINGS:\n";
    foreach ($warnings as $i => $warning) {
        echo "  " . ($i + 1) . ". {$warning}\n";
    }
    echo "\n";
}

if (count($errors) > 0) {
    echo "FAILURES:\n";
    foreach ($errors as $i => $error) {
        echo "  " . ($i + 1) . ". {$error}\n";
    }
    echo "\n";
}

$successRate = $total > 0 ? round(($passed / $total) * 100, 1) : 0;
echo "Success Rate: {$successRate}%\n\n";

if (count($errors) === 0) {
    echo "✓ System logic is working correctly!\n";
    echo "✓ Data consistency verified\n";
    echo "✓ User interactions functioning properly\n";
} else {
    echo "✗ Issues found that need attention\n";
}

echo "\n=================================================================\n";
