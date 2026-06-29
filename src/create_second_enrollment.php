<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CREATE SECOND ENROLLMENT ===\n\n";

// Get student (latest user)
$student = App\Models\Student::find(2);

if (!$student) {
    echo "ERROR: Student ID 2 not found\n";
    exit(1);
}

echo "Student: {$student->user->name} (ID: {$student->id})\n";

// Get class 2
$class = App\Models\ClassModel::find(2);

if (!$class) {
    echo "ERROR: Class ID 2 not found\n";
    exit(1);
}

echo "Class: {$class->name} (ID: {$class->id})\n";
echo "Course: {$class->course->name}\n";
echo "Status: {$class->status}\n\n";

// Check if already enrolled
$existing = App\Models\Enrollment::where('student_id', $student->id)
    ->where('class_id', $class->id)
    ->first();

if ($existing) {
    echo "⚠️  Already enrolled!\n";
    echo "Enrollment ID: {$existing->id} | Status: {$existing->status}\n";
    exit(0);
}

// Create enrollment
try {
    $enrollment = App\Models\Enrollment::create([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'enrollment_date' => now()->toDateString(),
        'status' => 'paid',
        'completion_percentage' => 0
    ]);
    
    echo "✅ Created enrollment!\n";
    echo "Enrollment ID: {$enrollment->id}\n";
    echo "Status: {$enrollment->status}\n\n";
    
    // Increment class enrollment count
    $class->increment('current_enrollment');
    echo "✅ Incremented class enrollment count\n\n";
    
    // Check schedules for this class
    $scheduleCount = App\Models\Schedule::where('class_id', $class->id)->count();
    echo "This class has {$scheduleCount} schedules\n";
    
    if ($scheduleCount === 0) {
        echo "⚠️  WARNING: This class has NO schedules! Creating sample schedules...\n";
        
        // Create 5 sample schedules
        $startDate = now()->addDays(1);
        for ($i = 0; $i < 5; $i++) {
            $date = $startDate->copy()->addDays($i * 2); // Every 2 days
            App\Models\Schedule::create([
                'class_id' => $class->id,
                'date' => $date->toDateString(),
                'start_time' => '19:00:00',
                'end_time' => '21:00:00',
                'location' => 'Phòng 20' . ($i + 1),
                'topic' => 'Buổi ' . ($i + 1),
                'status' => 'scheduled'
            ]);
        }
        echo "✅ Created 5 sample schedules\n";
    }
    
    echo "\n=== VERIFICATION ===\n";
    $allEnrollments = $student->enrollments()
        ->whereIn('status', ['paid', 'approved', 'pending'])
        ->with(['class.course'])
        ->get();
    
    echo "Total active enrollments: {$allEnrollments->count()}\n";
    foreach ($allEnrollments as $e) {
        $schedules = App\Models\Schedule::where('class_id', $e->class_id)
            ->where('date', '>=', now())
            ->count();
        echo "  - {$e->class->name} ({$e->class->course->name}) - {$schedules} upcoming schedules\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: {$e->getMessage()}\n";
    exit(1);
}
