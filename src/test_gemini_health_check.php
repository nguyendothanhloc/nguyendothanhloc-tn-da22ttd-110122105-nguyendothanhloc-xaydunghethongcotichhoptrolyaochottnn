<?php
/**
 * Script kiểm tra sức khỏe của Gemini API
 * Gửi câu hỏi test và kiểm tra response có đầy đủ và chất lượng không
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['GEMINI_API_KEY'];
$model = $_ENV['GEMINI_MODEL'];
$endpoint = $_ENV['GEMINI_API_ENDPOINT'];

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║     KIỂM TRA GEMINI API - HEALTH CHECK                      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "📝 Cấu hình:\n";
echo "   • API Key: " . substr($apiKey, 0, 15) . "...\n";
echo "   • Model: {$model}\n";
echo "   • Endpoint: {$endpoint}\n\n";

// Các câu hỏi test (ngoài FAQ để test Gemini)
$testQuestions = [
    "Bạn có thể giới thiệu về mình không?",
    "Tôi nên học tiếng Anh như thế nào cho hiệu quả?",
    "Làm thế nào để cải thiện kỹ năng nói tiếng Anh?",
];

foreach ($testQuestions as $index => $question) {
    $testNumber = $index + 1;
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TEST {$testNumber}/3: {$question}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // System instruction
    $systemInstruction = "Bạn là trợ lý AI thông minh của trung tâm ngoại ngữ. 
Hãy trả lời câu hỏi một cách thân thiện, chi tiết và hữu ích.
TRẢ LỜI ĐẦY ĐỦ (50-150 từ), có cấu trúc rõ ràng với emoji và gợi ý.";

    $payload = [
        'system_instruction' => [
            'parts' => [
                ['text' => $systemInstruction]
            ]
        ],
        'contents' => [
            [
                'parts' => [
                    ['text' => $question]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 1000,
            'topP' => 0.95,
            'topK' => 40,
        ]
    ];

    $url = "{$endpoint}/models/{$model}:generateContent";
    
    echo "⏳ Đang gửi request...\n";
    $startTime = microtime(true);
    
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Goog-Api-Key: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000);
        
        if ($error) {
            echo "❌ LỖI CURL: {$error}\n\n";
            continue;
        }
        
        if ($httpCode !== 200) {
            echo "❌ HTTP Error {$httpCode}\n";
            echo "Response: {$response}\n\n";
            continue;
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            echo "❌ Response không có text\n";
            echo "Raw response: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
            continue;
        }
        
        $responseText = $data['candidates'][0]['content']['parts'][0]['text'];
        $finishReason = $data['candidates'][0]['finishReason'] ?? 'UNKNOWN';
        $wordCount = str_word_count($responseText);
        $charCount = mb_strlen($responseText);
        
        // Phân tích chất lượng
        $hasEmoji = preg_match('/[\x{1F300}-\x{1F9FF}]/u', $responseText);
        $hasStructure = (substr_count($responseText, "\n") > 2);
        $isDetailed = ($wordCount >= 30); // Ít nhất 30 từ
        
        echo "✅ Response nhận được!\n\n";
        echo "📊 Thống kê:\n";
        echo "   • Thời gian: {$duration}ms\n";
        echo "   • Finish Reason: {$finishReason}\n";
        echo "   • Số từ: {$wordCount} từ\n";
        echo "   • Số ký tự: {$charCount} ký tự\n";
        echo "   • Có emoji: " . ($hasEmoji ? "✅ Có" : "⚠️ Không") . "\n";
        echo "   • Có cấu trúc: " . ($hasStructure ? "✅ Có" : "⚠️ Không") . "\n";
        echo "   • Chi tiết đủ: " . ($isDetailed ? "✅ Có" : "⚠️ Không (< 30 từ)") . "\n\n";
        
        echo "💬 Câu trả lời:\n";
        echo "┌─────────────────────────────────────────────────────────────┐\n";
        echo wordwrap($responseText, 60, "\n");
        echo "\n└─────────────────────────────────────────────────────────────┘\n\n";
        
        // Đánh giá tổng thể
        $score = 0;
        if ($finishReason === 'STOP') $score++;
        if ($hasEmoji) $score++;
        if ($hasStructure) $score++;
        if ($isDetailed) $score++;
        if ($wordCount >= 50) $score++;
        
        echo "🎯 Đánh giá chất lượng: {$score}/5 ";
        if ($score >= 4) {
            echo "⭐⭐⭐ Tốt\n";
        } elseif ($score >= 2) {
            echo "⭐⭐ Trung bình\n";
        } else {
            echo "⭐ Yếu\n";
        }
        echo "\n";
        
    } catch (Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n\n";
    }
    
    // Nghỉ giữa các test
    if ($testNumber < count($testQuestions)) {
        sleep(2);
    }
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "🏁 HOÀN THÀNH KIỂM TRA!\n";
echo "═══════════════════════════════════════════════════════════════\n";
