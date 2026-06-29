<?php
/**
 * Debug Gemini API Response - Check finish_reason và safety ratings
 */

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
$model = $_ENV['GEMINI_MODEL'] ?? 'gemini-2.0-flash-exp';
$endpoint = $_ENV['GEMINI_API_ENDPOINT'] ?? 'https://generativelanguage.googleapis.com/v1beta';

echo "═══════════════════════════════════════════════════\n";
echo "🐛 DEBUG GEMINI API - FULL RESPONSE ANALYSIS\n";
echo "═══════════════════════════════════════════════════\n\n";

// Prompt đơn giản nhất có thể
$prompt = "Bạn là trợ lý AI. Trả lời ngắn gọn bằng tiếng Việt.

CÂU HỎI: Hôm nay tôi học gì?
LỊCH HỌC: Tiếng Anh, 18:00-20:00, Phòng 106

TRẢ LỜI:";

$payload = [
    'contents' => [['parts' => [['text' => $prompt]]]],
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 1000,
        'topP' => 0.95,
        'topK' => 40
    ],
    'safetySettings' => [
        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE']
    ]
];

echo "📤 REQUEST:\n";
echo "Model: {$model}\n";
echo "Prompt length: " . strlen($prompt) . " chars\n";
echo "maxOutputTokens: 1000\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "{$endpoint}/models/{$model}:generateContent");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Goog-Api-Key: ' . $apiKey
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$startTime = microtime(true);
$response = curl_exec($ch);
$duration = round((microtime(true) - $startTime) * 1000);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "📥 RESPONSE:\n";
echo "HTTP Status: {$httpCode}\n";
echo "Duration: {$duration}ms\n\n";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    
    // Show FULL response structure
    echo "📋 FULL JSON RESPONSE:\n";
    echo "════════════════════════════════════════════\n";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    echo "════════════════════════════════════════════\n\n";
    
    // Extract specific fields
    if (isset($data['candidates'][0])) {
        $candidate = $data['candidates'][0];
        
        echo "🔍 ANALYSIS:\n";
        echo "─────────────────────────────────────────────\n";
        
        // Finish reason
        $finishReason = $candidate['finishReason'] ?? 'NOT_SET';
        echo "finish_reason: {$finishReason}\n";
        
        if ($finishReason === 'STOP') {
            echo "  ✅ Normal completion\n";
        } elseif ($finishReason === 'MAX_TOKENS') {
            echo "  ⚠️ Hit max token limit\n";
        } elseif ($finishReason === 'SAFETY') {
            echo "  ❌ Blocked by safety filter\n";
        } elseif ($finishReason === 'RECITATION') {
            echo "  ❌ Blocked due to recitation\n";
        } else {
            echo "  ⚠️ Unknown reason: {$finishReason}\n";
        }
        
        echo "\n";
        
        // Safety ratings
        if (isset($candidate['safetyRatings'])) {
            echo "🛡️ SAFETY RATINGS:\n";
            foreach ($candidate['safetyRatings'] as $rating) {
                $category = $rating['category'] ?? 'UNKNOWN';
                $probability = $rating['probability'] ?? 'UNKNOWN';
                $blocked = isset($rating['blocked']) && $rating['blocked'] ? '🚫 BLOCKED' : '✅ OK';
                echo "  {$category}: {$probability} {$blocked}\n";
            }
            echo "\n";
        }
        
        // Generated text
        if (isset($candidate['content']['parts'][0]['text'])) {
            $text = $candidate['content']['parts'][0]['text'];
            echo "💬 GENERATED TEXT:\n";
            echo "┌─────────────────────────────────────────────┐\n";
            echo $text . "\n";
            echo "└─────────────────────────────────────────────┘\n";
            echo "Length: " . strlen($text) . " chars\n";
            echo "Words: " . str_word_count($text) . " words\n\n";
        } else {
            echo "❌ NO TEXT GENERATED\n\n";
        }
        
        // Token usage
        if (isset($data['usageMetadata'])) {
            echo "📊 TOKEN USAGE:\n";
            $usage = $data['usageMetadata'];
            echo "  Prompt tokens: " . ($usage['promptTokenCount'] ?? 'N/A') . "\n";
            echo "  Completion tokens: " . ($usage['candidatesTokenCount'] ?? 'N/A') . "\n";
            echo "  Total tokens: " . ($usage['totalTokenCount'] ?? 'N/A') . "\n";
        }
    }
    
} else {
    echo "❌ ERROR:\n";
    echo $response . "\n";
}

echo "\n═══════════════════════════════════════════════════\n";
