<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Course;
use App\Models\Teacher;
use App\Models\ClassModel;

echo "=== DEBUG TEACHERS ===\n\n";

// 1. Check courses
echo "1. KHOA HOC TIENG NHAT:\n";
$courses = Course::where('language', 'Japanese')->where('is_active', true)->get();
echo "So khoa hoc: " . $courses->count() . "\n";
foreach ($courses as $course) {
    echo "  - ID: {$course->id}, Name: {$course->name}, Language: {$course->language}\n";
}
echo "\n";

// 2. Check all courses language
echo "2. TAT CA KHOA HOC (de kiem tra language field):\n";
$allCourses = Course::all();
foreach ($allCourses as $course) {
    echo "  - ID: {$course->id}, Name: {$course->name}, Language: {$course->language}, Active: {$course->is_active}\n";
}
echo "\n";

// 3. Check teachers
echo "3. TAT CA GIAO VIEN:\n";
$teachers = Teacher::with('user')->get();
echo "So giao vien: " . $teachers->count() . "\n";
foreach ($teachers as $teacher) {
    echo "  - ID: {$teacher->id}, Name: {$teacher->user->name}, Email: {$teacher->user->email}\n";
}
echo "\n";

// 4. Check classes
echo "4. TAT CA LOP HOC:\n";
$classes = ClassModel::with(['course', 'teacher.user'])->get();
echo "So lop hoc: " . $classes->count() . "\n";
foreach ($classes as $class) {
    $teacherName = $class->teacher ? $class->teacher->user->name : 'Khong co';
    echo "  - ID: {$class->id}, Name: {$class->name}\n";
    echo "    Course: {$class->course->name} ({$class->course->language})\n";
    echo "    Teacher: {$teacherName}\n";
    echo "    Status: {$class->status}\n";
}
echo "\n";

// 5. Check classes by Japanese courses
if ($courses->isNotEmpty()) {
    $courseIds = $courses->pluck('id');
    echo "5. LOP HOC TIENG NHAT (Course IDs: " . $courseIds->implode(', ') . "):\n";
    $japaneseClasses = ClassModel::whereIn('course_id', $courseIds)
        ->with(['course', 'teacher.user'])
        ->get();
    echo "So lop: " . $japaneseClasses->count() . "\n";
    foreach ($japaneseClasses as $class) {
        $teacherName = $class->teacher ? $class->teacher->user->name : 'Khong co';
        echo "  - {$class->name}, Teacher: {$teacherName}, Status: {$class->status}\n";
    }
    echo "\n";
    
    // 6. Check teachers teaching Japanese
    echo "6. GIAO VIEN DAY TIENG NHAT:\n";
    $japaneseTeachers = Teacher::whereHas('classes', function ($query) use ($courseIds) {
        $query->whereIn('course_id', $courseIds);
    })->with('user')->get();
    echo "So giao vien: " . $japaneseTeachers->count() . "\n";
    foreach ($japaneseTeachers as $teacher) {
        echo "  - {$teacher->user->name} ({$teacher->user->email})\n";
    }
}
