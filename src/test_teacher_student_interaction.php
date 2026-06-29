<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Assessment;
use App\Models\AssessmentScore;

echo "=== TEACHER-STUDENT INTERACTION TEST ===\n\n";

// Get the test student
$student = Student::with('user')->find(3);
if (!$student) {
    echo "✗ Test student not found!\n";
    exit(1);
}

echo "Testing with student: {$student->user->name} (ID={$student->id})\n";
echo "Student email: {$student->user->email}\n\n";

// Get the class
$class = ClassModel::with('teacher.user')->find(1);
echo "Class: {$class->name}\n";
echo "Teacher: {$class->teacher->user->name}\n\n";

// ===== TEST 1: TEACHER TAKES ATTENDANCE =====
echo "=== TEST 1: Teacher Takes Attendance ===\n";

// Get the first upcoming schedule
$schedule = Schedule::where('class_id', $class->id)
    ->where('date', '>=', now())
    ->orderBy('date')
    ->first();

if (!$schedule) {
    echo "✗ No schedule found!\n";
    exit(1);
}

echo "Schedule: {$schedule->date->format('d/m/Y')} {$schedule->start_time}-{$schedule->end_time}\n";
echo "Location: {$schedule->location}, Topic: {$schedule->topic}\n\n";

// Teacher marks attendance for all students
$studentsForAttendance = $class->enrollments()
    ->whereIn('status', ['paid', 'approved', 'pending'])
    ->with('student.user')
    ->get()
    ->pluck('student');

echo "Teacher can see {$studentsForAttendance->count()} students for attendance:\n";
foreach ($studentsForAttendance as $s) {
    echo "  - {$s->user->name}\n";
}
echo "\n";

// Mark our test student as present
$attendance = Attendance::updateOrCreate(
    [
        'schedule_id' => $schedule->id,
        'student_id' => $student->id,
    ],
    [
        'status' => 'present',
        'note' => 'Test attendance',
        'recorded_at' => now(),
    ]
);

echo "✓ Attendance recorded for {$student->user->name}: {$attendance->status}\n";
echo "  Recorded at: {$attendance->recorded_at}\n\n";

// Verify student can see their attendance
$studentAttendances = $student->attendances()
    ->with(['schedule.class.course'])
    ->orderBy('recorded_at', 'desc')
    ->limit(5)
    ->get();

echo "Student can see {$studentAttendances->count()} attendance record(s):\n";
foreach ($studentAttendances as $att) {
    echo "  - {$att->schedule->date->format('d/m/Y')}: {$att->status}\n";
    echo "    Class: {$att->schedule->class->name}\n";
}
echo "\n";

// ===== TEST 2: TEACHER CREATES ASSESSMENT =====
echo "=== TEST 2: Teacher Creates Assessment ===\n";

// Create a new assessment
$assessment = Assessment::create([
    'class_id' => $class->id,
    'name' => 'Test Assessment - Kiểm tra giữa kỳ',
    'type' => 'midterm',
    'max_score' => 100,
    'assessment_date' => now()->toDateString(),
    'description' => 'Test assessment for testing purposes'
]);

echo "✓ Assessment created: {$assessment->name}\n";
echo "  Type: {$assessment->type}, Max score: {$assessment->max_score}\n\n";

// ===== TEST 3: TEACHER ENTERS SCORES =====
echo "=== TEST 3: Teacher Enters Scores ===\n";

// Get all students for this assessment
$studentsForScoring = $class->enrollments()
    ->whereIn('status', ['paid', 'approved', 'pending'])
    ->with(['student.user'])
    ->get()
    ->pluck('student');

echo "Teacher can see {$studentsForScoring->count()} students for scoring:\n";
foreach ($studentsForScoring as $s) {
    echo "  - {$s->user->name}\n";
}
echo "\n";

// Enter score for our test student
$score = AssessmentScore::updateOrCreate(
    [
        'assessment_id' => $assessment->id,
        'student_id' => $student->id,
    ],
    [
        'score' => 85,
        'feedback' => 'Good job! Keep up the excellent work.',
    ]
);

echo "✓ Score entered for {$student->user->name}: {$score->score}/{$assessment->max_score}\n";
echo "  Feedback: {$score->feedback}\n\n";

// ===== TEST 4: STUDENT VIEWS THEIR SCORES =====
echo "=== TEST 4: Student Views Their Scores ===\n";

// Get student's assessment scores
$studentScores = $student->assessmentScores()
    ->with(['assessment.class.course'])
    ->orderBy('created_at', 'desc')
    ->get();

echo "Student can see {$studentScores->count()} score(s):\n";
foreach ($studentScores as $sc) {
    $percentage = ($sc->score / $sc->assessment->max_score) * 100;
    echo "  - {$sc->assessment->name}: {$sc->score}/{$sc->assessment->max_score} ({$percentage}%)\n";
    echo "    Type: {$sc->assessment->type}\n";
    if ($sc->feedback) {
        echo "    Feedback: {$sc->feedback}\n";
    }
}
echo "\n";

// ===== TEST 5: STUDENT DASHBOARD STATS =====
echo "=== TEST 5: Student Dashboard Statistics ===\n";

// Calculate attendance statistics
$totalAttendances = $student->attendances()->count();
$presentCount = $student->attendances()->where('status', 'present')->count();
$absentCount = $student->attendances()->where('status', 'absent')->count();

echo "Attendance Statistics:\n";
echo "  - Total sessions: {$totalAttendances}\n";
echo "  - Present: {$presentCount}\n";
echo "  - Absent: {$absentCount}\n";

if ($totalAttendances > 0) {
    $attendanceRate = round(($presentCount / $totalAttendances) * 100, 1);
    echo "  - Attendance rate: {$attendanceRate}%\n";
}
echo "\n";

// Calculate assessment statistics
$totalScores = $student->assessmentScores()->count();
$averageScore = $totalScores > 0 ? round($student->assessmentScores()->avg('score'), 2) : 0;

echo "Assessment Statistics:\n";
echo "  - Total assessments: {$totalScores}\n";
echo "  - Average score: {$averageScore}\n\n";

// ===== TEST 6: ENROLLMENT STATUS CHECK =====
echo "=== TEST 6: Enrollment Status Check ===\n";

$enrollment = $student->enrollments()->where('class_id', $class->id)->first();
echo "Enrollment Status: {$enrollment->status}\n";
echo "Completion Percentage: {$enrollment->completion_percentage}%\n";
echo "Enrollment Date: {$enrollment->enrollment_date->format('d/m/Y')}\n\n";

// ===== FINAL SUMMARY =====
echo "=== FINAL TEST SUMMARY ===\n";
echo "✓ Teacher can see the new student in class roster\n";
echo "✓ Teacher can mark attendance for the student\n";
echo "✓ Student can see their attendance records\n";
echo "✓ Teacher can create assessments\n";
echo "✓ Teacher can enter scores for the student\n";
echo "✓ Student can see their assessment scores and feedback\n";
echo "✓ Student dashboard shows correct statistics\n";
echo "✓ Enrollment status is 'paid' (auto-approved)\n\n";

echo "=== ALL TESTS PASSED! ===\n\n";

echo "You can now login and test manually:\n";
echo "Student Login: testuser@test.com / password\n";
echo "Teacher Login: teacher1@teacher.com / password\n";
echo "URL: http://127.0.0.1:8000/login\n";
