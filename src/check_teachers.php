<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Teacher;
use App\Models\User;

echo "=== KIỂM TRA GIÁO VIÊN TRONG HỆ THỐNG ===\n\n";

// Get all teachers
$teachers = Teacher::with('user')->orderBy('id', 'desc')->get();

if ($teachers->isEmpty()) {
    echo "❌ KHÔNG CÓ GIÁO VIÊN NÀO TRONG HỆ THỐNG!\n\n";
} else {
    echo "✅ Tìm thấy {$teachers->count()} giáo viên:\n\n";
    
    foreach ($teachers as $teacher) {
        echo "ID: {$teacher->id}\n";
        echo "User ID: {$teacher->user_id}\n";
        echo "Tên: {$teacher->user->name}\n";
        echo "Email: {$teacher->user->email}\n";
        echo "Role: {$teacher->user->role}\n";
        echo "Active: " . ($teacher->user->is_active ? 'Yes' : 'No') . "\n";
        echo "Specialization: " . ($teacher->specialization ?? 'Chưa có') . "\n";
        echo "---\n\n";
    }
}

// Also check users with role 'teacher'
echo "\n=== USERS CÓ ROLE = TEACHER ===\n\n";
$teacherUsers = User::where('role', 'teacher')->get();

if ($teacherUsers->isEmpty()) {
    echo "❌ KHÔNG CÓ USER NÀO CÓ ROLE TEACHER!\n";
} else {
    echo "✅ Tìm thấy {$teacherUsers->count()} users với role teacher:\n\n";
    foreach ($teacherUsers as $user) {
        $hasProfile = Teacher::where('user_id', $user->id)->exists();
        echo "User ID: {$user->id}\n";
        echo "Tên: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Active: " . ($user->is_active ? 'Yes' : 'No') . "\n";
        echo "Có profile Teacher: " . ($hasProfile ? 'YES ✅' : 'NO ❌') . "\n";
        echo "---\n\n";
    }
}
