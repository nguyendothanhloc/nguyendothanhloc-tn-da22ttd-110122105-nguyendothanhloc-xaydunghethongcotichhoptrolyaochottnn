<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Services\GeminiChatbotService;
use Illuminate\Support\Facades\Auth;

echo "=================================================================\n";
echo "              KIỂM TRA GEMINI API HOẠT ĐỘNG                     \n";
echo "=================================================================\n\n";

// Login as test student
$user = User::where('email', 'hocvien1@gmail.com')->first();

if (!$user) {
    echo "❌ Không tìm thấy user test!\n";
    exit(1);
}

Auth::login($user);
$student = Student::where('user_id', $user->id)->first();

if (!$student) {
    echo "❌ Không tìm thấy student!\n";
    exit(1);
}

echo "✅ Logged in as: {$user->name}\n";
echo "📝 Student ID: {$student->id}\n\n";

// Check .env
echo "🔍 Kiểm tra cấu hình:\n";
$apiKey = env('GEMINI_API_KEY');

if (empty($apiKey)) {
    echo "   ❌ GEMINI_API_KEY không tồn tại trong .env!\n";
    exit(1);
} else {
    $keyPreview = substr($apiKey, 0, 20) . '...' . substr($apiKey, -10);
    echo "   ✅ GEMINI_API_KEY: {$keyPreview}\n";
}

$apiUrl = 'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent';
echo "   ✅ API URL: {$apiUrl}\n\n";

// Test 1: Direct API call
echo "📡 TEST 1: GỌI TRỰC TIẾP GEMINI API\n";
echo "-----------------------------------\n";

try {
    $ch = curl_init();
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Xin chào! Hãy nói "Hello from Gemini!"']
                ]
            ]
        ]
    ];
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl . '?key=' . $apiKey,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    echo "   HTTP Code: {$httpCode}\n";
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $result['candidates'][0]['content']['parts'][0]['text'];
            echo "   ✅ API hoạt động!\n";
            echo "   📝 Response: {$text}\n\n";
        } else {
            echo "   ⚠️  Response không đúng format:\n";
            echo "   " . substr($response, 0, 200) . "...\n\n";
        }
    } elseif ($httpCode === 429) {
        echo "   ❌ RATE LIMIT! (429 - Too Many Requests)\n";
        echo "   ⏳ Bạn đã gọi API quá nhiều lần.\n";
        echo "   💡 Đợi 1-2 phút rồi thử lại.\n\n";
    } elseif ($httpCode === 403) {
        echo "   ❌ API KEY không hợp lệ! (403 - Forbidden)\n";
        echo "   💡 Kiểm tra lại GEMINI_API_KEY trong .env\n\n";
    } elseif ($httpCode === 400) {
        echo "   ❌ Request không hợp lệ! (400 - Bad Request)\n";
        echo "   Response: " . substr($response, 0, 300) . "\n\n";
    } else {
        echo "   ❌ Lỗi: HTTP {$httpCode}\n";
        echo "   Response: " . substr($response, 0, 300) . "\n";
        if ($error) {
            echo "   CURL Error: {$error}\n";
        }
        echo "\n";
    }
    
} catch (\Exception $e) {
    echo "   ❌ Exception: {$e->getMessage()}\n\n";
}

// Test 2: Via GeminiChatbotService
echo "📡 TEST 2: QUA GeminiChatbotService\n";
echo "-----------------------------------\n";

try {
    $geminiService = new GeminiChatbotService();
    
    echo "   Đang gọi generateResponse()...\n";
    
    $startTime = microtime(true);
    $response = $geminiService->generateResponse('Xin chào! Bạn có hoạt động không?', $student->id);
    $endTime = microtime(true);
    
    $duration = round(($endTime - $startTime) * 1000);
    
    echo "   ✅ Service hoạt động!\n";
    echo "   ⏱️  Thời gian: {$duration}ms\n";
    echo "   📝 Response preview: " . substr($response, 0, 150) . "...\n\n";
    
} catch (\Exception $e) {
    echo "   ❌ Lỗi: {$e->getMessage()}\n";
    echo "   Stack trace:\n";
    $trace = $e->getTraceAsString();
    $lines = explode("\n", $trace);
    foreach (array_slice($lines, 0, 5) as $line) {
        echo "      " . $line . "\n";
    }
    echo "\n";
}

// Test 3: Via RuleBasedChatbotService (full flow)
echo "📡 TEST 3: QUA RuleBasedChatbotService (FULL FLOW)\n";
echo "-----------------------------------\n";

try {
    $chatbotService = new \App\Services\RuleBasedChatbotService();
    
    // Test với câu hỏi không có rule/FAQ → phải gọi Gemini
    $testQuestion = 'Hôm nay trời đẹp quá, bạn nghĩ sao?';
    
    echo "   Question: '{$testQuestion}'\n";
    echo "   Đang xử lý...\n";
    
    $startTime = microtime(true);
    $response = $chatbotService->processMessage($testQuestion);
    $endTime = microtime(true);
    
    $duration = round(($endTime - $startTime) * 1000);
    
    echo "   ✅ Process hoàn thành!\n";
    echo "   ⏱️  Thời gian: {$duration}ms\n";
    echo "   📊 Response Type: {$response['type']}\n";
    
    if ($response['type'] === 'ai_powered') {
        echo "   ✅ Đã gọi Gemini AI!\n";
        echo "   📝 Response: " . substr($response['response'], 0, 200) . "...\n\n";
    } else {
        echo "   ⚠️  Không gọi Gemini (type: {$response['type']})\n";
        echo "   Response: " . substr($response['response'], 0, 200) . "\n\n";
    }
    
} catch (\Exception $e) {
    echo "   ❌ Lỗi: {$e->getMessage()}\n\n";
}

// Summary
echo "=================================================================\n";
echo "                          TÓM TẮT                                \n";
echo "=================================================================\n";

// Check Laravel log for recent errors
echo "\n📋 Kiểm tra Laravel log (5 dòng cuối):\n";
$logFile = storage_path('logs/laravel.log');

if (file_exists($logFile)) {
    $lines = file($logFile);
    $recentLines = array_slice($lines, -5);
    
    foreach ($recentLines as $line) {
        if (stripos($line, 'error') !== false || stripos($line, 'exception') !== false) {
            echo "   ⚠️  " . trim(substr($line, 0, 100)) . "\n";
        }
    }
} else {
    echo "   ℹ️  Log file không tồn tại\n";
}

echo "\n";
echo "🔍 CHẨN ĐOÁN:\n";
echo "   1. Nếu TEST 1 trả về 429 → RATE LIMIT (đợi 1-2 phút)\n";
echo "   2. Nếu TEST 1 trả về 403 → API KEY sai\n";
echo "   3. Nếu TEST 1 pass nhưng TEST 2/3 fail → Lỗi code\n";
echo "   4. Nếu tất cả pass → Gemini hoạt động bình thường!\n";
echo "\n";
