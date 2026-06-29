<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Updating student info...\n";

$user = User::where('email', 'hocvien1@gmail.com')->first();

if ($user) {
    $user->phone = '0901234567';
    $user->save();
    
    echo "✅ Updated user info: " . $user->name . "\n";
    echo "  📱 Phone: " . $user->phone . "\n";
    
    if ($user->student) {
        $student = $user->student;
        $student->level = 'Beginner';
        $student->interests = 'Tieng Anh, Du lich';
        $student->save();
        
        echo "  📚 Level: " . $student->level . "\n";
        echo "  🎯 Interests: " . $student->interests . "\n";
    }
} else {
    echo "❌ User not found\n";
}
