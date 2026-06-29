<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test 1: Create new enrollment with 'paid' status
echo "=== TEST 1: Create new enrollment ===\n";
$student = App\Models\Student::latest()->first();
$class = App\Models\ClassModel::first(); // Remove status filter

if ($student && $class) {
    echo "Found: Student ID {$student->id}, Class ID {$class->id} (Status: {$class->status})\n";
    
    // Check if already enrolled
    $existing = App\Models\Enrollment::where('student_id', $student->id)
        ->where('class_id', $class->id)
        ->first();
    
    if ($existing) {
        echo "Already enrolled: Student {$student->id} in Class {$class->id} (Status: {$existing->status})\n";
    } else {
        $enrollment = App\Models\Enrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'paid',
            'completion_percentage' => 0
        ]);
        echo "Created: Enrollment ID {$enrollment->id} | Status: {$enrollment->status}\n";
    }
} else {
    echo "ERROR: No student or class found\n";
    echo "Student count: " . App\Models\Student::count() . "\n";
    echo "Class count: " . App\Models\ClassModel::count() . "\n";
}

// Test 2: Check what dashboard would show
echo "\n=== TEST 2: Dashboard enrollments ===\n";
if ($student) {
    $enrollments = $student->enrollments()
        ->whereIn('status', ['paid', 'approved', 'pending'])
        ->whereHas('class', function($query) {
            $query->where('status', '!=', 'cancelled');
        })
        ->with(['class.course'])
        ->get();
    
    echo "Total enrollments: " . $enrollments->count() . "\n";
    foreach ($enrollments as $e) {
        echo "  - ID: {$e->id} | Class: {$e->class->name} | Status: {$e->status}\n";
    }
}

echo "\n✅ Test completed!\n";
