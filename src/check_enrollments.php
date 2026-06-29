<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the latest user (your new account)
$user = App\Models\User::latest()->first();

if (!$user || !$user->student) {
    echo "ERROR: No student found\n";
    exit(1);
}

$student = $user->student;

echo "=== STUDENT INFO ===\n";
echo "Name: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Student ID: {$student->id}\n\n";

echo "=== ENROLLMENTS ===\n";
$enrollments = $student->enrollments()
    ->with(['class.course'])
    ->get();

echo "Total enrollments: " . $enrollments->count() . "\n\n";

foreach ($enrollments as $e) {
    echo "Enrollment ID: {$e->id}\n";
    echo "  Class: {$e->class->name} (ID: {$e->class_id})\n";
    echo "  Course: {$e->class->course->name}\n";
    echo "  Status: {$e->status}\n";
    echo "  Class Status: {$e->class->status}\n";
    
    // Check schedules for this class
    $scheduleCount = App\Models\Schedule::where('class_id', $e->class_id)->count();
    echo "  Schedules: {$scheduleCount}\n";
    
    if ($scheduleCount > 0) {
        $upcomingSchedules = App\Models\Schedule::where('class_id', $e->class_id)
            ->where('date', '>=', now())
            ->orderBy('date')
            ->limit(3)
            ->get();
        echo "  Upcoming schedules:\n";
        foreach ($upcomingSchedules as $s) {
            echo "    - {$s->date->format('d/m/Y')} {$s->start_time}-{$s->end_time}\n";
        }
    } else {
        echo "  ⚠️  NO SCHEDULES for this class!\n";
    }
    
    echo "\n";
}

echo "=== DASHBOARD QUERY ===\n";
$dashboardEnrollments = $student->enrollments()
    ->whereIn('status', ['paid', 'approved', 'pending'])
    ->whereHas('class', function($query) {
        $query->where('status', '!=', 'cancelled');
    })
    ->with(['class.course'])
    ->get();

echo "Dashboard would show: " . $dashboardEnrollments->count() . " enrollments\n";
foreach ($dashboardEnrollments as $e) {
    echo "  - {$e->class->name} (Status: {$e->status})\n";
}
