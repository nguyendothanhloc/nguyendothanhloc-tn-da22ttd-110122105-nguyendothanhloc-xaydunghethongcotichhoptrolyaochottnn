<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Services\GeminiChatbotService;
use Illuminate\Support\Facades\Auth;

// Login as student
$user = User::where('email', 'hocvien1@gmail.com')->first();
Auth::login($user);

$student = Student::where('user_id', $user->id)->first();

if (!$student) {
    echo "❌ Student not found!\n";
    exit(1);
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "TEST GEMINI CONTEXT FOR STUDENT: " . $user->name . "\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Use reflection to access private buildStudentContext method
$geminiService = new GeminiChatbotService();
$reflection = new ReflectionClass($geminiService);
$method = $reflection->getMethod('buildStudentContext');
$method->setAccessible(true);

$context = $method->invoke($geminiService, $student->id);

echo "📋 STUDENT CONTEXT DATA:\n\n";

// Show student info
if (!empty($context['student'])) {
    echo "👤 STUDENT INFO:\n";
    print_r($context['student']);
    echo "\n";
}

// Show enrollments
if (!empty($context['enrollments'])) {
    echo "📚 ENROLLMENTS (" . count($context['enrollments']) . "):\n";
    foreach ($context['enrollments'] as $idx => $enrollment) {
        echo "  " . ($idx + 1) . ". {$enrollment['class_name']} - {$enrollment['course_name']}\n";
        echo "     Language: {$enrollment['language']}, Level: {$enrollment['level']}\n";
        echo "     Teacher: {$enrollment['teacher_name']}\n";
        echo "     Status: {$enrollment['status']}\n\n";
    }
} else {
    echo "📚 ENROLLMENTS: ❌ EMPTY (THIS IS THE PROBLEM!)\n\n";
}

// Show schedules
if (!empty($context['schedules'])) {
    echo "📅 SCHEDULES (" . count($context['schedules']) . "):\n";
    foreach ($context['schedules'] as $idx => $schedule) {
        echo "  " . ($idx + 1) . ". {$schedule['class_name']}\n";
        echo "     Date: {$schedule['date']} ({$schedule['day_of_week']})\n";
        echo "     Time: {$schedule['start_time']} - {$schedule['end_time']}\n";
        echo "     Location: {$schedule['location']}\n\n";
    }
} else {
    echo "📅 SCHEDULES: ❌ EMPTY\n\n";
}

// Show attendance
if (!empty($context['attendance'])) {
    echo "✅ ATTENDANCE:\n";
    print_r($context['attendance']);
    echo "\n";
}

// Show assessments
if (!empty($context['assessments'])) {
    echo "📊 ASSESSMENTS (" . count($context['assessments']) . "):\n";
    foreach ($context['assessments'] as $idx => $assessment) {
        echo "  " . ($idx + 1) . ". {$assessment['title']}: {$assessment['score']}/{$assessment['max_score']}\n";
    }
    echo "\n";
} else {
    echo "📊 ASSESSMENTS: Empty\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";

if (empty($context['enrollments'])) {
    echo "❌ PROBLEM: Enrollments empty → Gemini says 'Chưa có khóa học'\n";
    echo "✅ SOLUTION: Added 'approved' to enrollment status filter\n";
} else {
    echo "✅ SUCCESS: Context has enrollments data!\n";
    echo "Gemini should now see teacher info correctly.\n";
}
