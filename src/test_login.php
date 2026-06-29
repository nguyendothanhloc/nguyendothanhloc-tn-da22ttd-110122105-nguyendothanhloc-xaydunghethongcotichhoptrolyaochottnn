<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║               KIỂM TRA TÀI KHOẢN ĐĂNG NHẬP                   ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Test accounts
$testAccounts = [
    ['email' => 'admin1@admin.com', 'expected_role' => 'admin'],
    ['email' => 'admin2@admin.com', 'expected_role' => 'admin'],
    ['email' => 'teacher1@teacher.com', 'expected_role' => 'teacher'],
    ['email' => 'teacher2@teacher.com', 'expected_role' => 'teacher'],
];

// Get all students
$students = User::where('role', 'student')->get();
foreach ($students as $student) {
    $testAccounts[] = ['email' => $student->email, 'expected_role' => 'student'];
}

echo "📋 KIỂM TRA " . count($testAccounts) . " TÀI KHOẢN\n";
echo "─────────────────────────────────────────────────────────────\n\n";

$workingCount = 0;
$brokenCount = 0;

foreach ($testAccounts as $account) {
    $user = User::where('email', $account['email'])->first();
    
    if (!$user) {
        echo "❌ {$account['email']} - KHÔNG TỒN TẠI\n";
        $brokenCount++;
        continue;
    }
    
    // Check role
    if ($user->role !== $account['expected_role']) {
        echo "⚠️  {$account['email']} - SAI ROLE (expected: {$account['expected_role']}, got: {$user->role})\n";
        $brokenCount++;
        continue;
    }
    
    // Check active status
    if (!$user->is_active) {
        echo "⚠️  {$account['email']} - TÀI KHOẢN BỊ VÔ HIỆU HÓA\n";
        $brokenCount++;
        continue;
    }
    
    // Check password (test with 'password')
    $passwordWorks = Hash::check('password', $user->password);
    
    if (!$passwordWorks) {
        echo "⚠️  {$account['email']} - MẬT KHẨU KHÔNG PHẢI 'password'\n";
    }
    
    echo "✅ {$account['email']} ({$user->role}) - OK\n";
    $workingCount++;
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                          KẾT QUẢ                             ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "✅ Tài khoản hoạt động tốt: {$workingCount}\n";
echo "❌ Tài khoản có vấn đề: {$brokenCount}\n\n";

if ($workingCount > 0) {
    echo "📍 CÁCH ĐĂNG NHẬP:\n";
    echo "   URL: http://127.0.0.1:8000/login\n\n";
    
    echo "   👑 Admin:\n";
    echo "      Email: admin1@admin.com | Password: password\n\n";
    
    echo "   👨‍🏫 Giáo viên:\n";
    echo "      Email: teacher1@teacher.com | Password: password\n\n";
    
    if (count($students) > 0) {
        $firstStudent = $students->first();
        echo "   🎓 Học viên:\n";
        echo "      Email: {$firstStudent->email} | Password: password\n\n";
    }
}

// Check if laravel session is configured
echo "🔧 KIỂM TRA CẤU HÌNH:\n";
echo "─────────────────────────────────────────────────────────────\n";

$envPath = base_path('.env');
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Check SESSION_DRIVER
    if (preg_match('/SESSION_DRIVER=(.+)/', $envContent, $matches)) {
        echo "   SESSION_DRIVER: {$matches[1]}\n";
    }
    
    // Check APP_KEY
    if (preg_match('/APP_KEY=(.+)/', $envContent, $matches)) {
        $key = trim($matches[1]);
        if (empty($key) || $key === '') {
            echo "   ❌ APP_KEY: CHƯA SET!\n";
            echo "      → Chạy: php artisan key:generate\n";
        } else {
            echo "   ✅ APP_KEY: Đã set\n";
        }
    }
}

echo "\n";
