<?php
/**
 * Test Gemini API trực tiếp với prompt ngắn gọn
 * Mục đích: So sánh response giữa prompt dài vs ngắn
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
$model = $_ENV['GEMINI_MODEL'] ?? 'gemini-2.0-flash-exp';
$endpoint = $_ENV['GEMINI_API_ENDPOINT'] ?? 'https://generativelanguage.googleapis.com/v1beta';

if (empty($apiKey)) {
    die("❌ GEMINI_API_KEY not found in .env\n");
}

echo "═══════════════════════════════════════════════════\n";
echo "🧪 TEST GEMINI - SO SÁNH PROMPT DÀI vs NGẮN\n";
echo "═══════════════════════════════════════════════════\n\n";

// ============================================
// TEST 1: PROMPT DÀI (như trong code hiện tại)
// ============================================
echo "📝 TEST 1: PROMPT DÀI (có nhiều rules, examples)\n";
echo "─────────────────────────────────────────────────\n";

$promptLong = "# ROLE & IDENTITY
Bạn là trợ lý AI thông minh của Trung tâm Ngoại ngữ, chuyên hỗ trợ học viên 24/7.
Tên: EduBot | Nhiệm vụ: Trả lời mọi thắc mắc về học tập, lịch học, điểm số, thanh toán

# RESPONSE RULES (CRITICAL - MUST FOLLOW STRICTLY)

**RULE 1: FORMAT**
  - Use emoji (📚📅✅❌🎯) + bullet points
  - Keep answers concise (max 150 words unless detailed info needed)

**RULE 2: TONE**
  - Friendly, professional
  - Address student as 'bạn', refer to yourself as 'tôi' or 'mình'
  - Always end with a follow-up question or suggestion

**RULE 3: LANGUAGE**
  - Always respond in Vietnamese

# EXAMPLES
Q: 'Hôm nay tôi học gì?'
A: '📅 Lịch học hôm nay của bạn:
✅ Tiếng Anh sáng thứ 2
⏰ 18:00 - 20:00
📍 Phòng 106
Bạn muốn xem lịch cả tuần không?'

════════════════════════════════
📋 [STUDENT CONTEXT]
════════════════════════════════
📚 KHÓA HỌC HIỆN TẠI:
1. Tiếng Anh A2 (English - Beginner)
   Lớp: Morning Class A2
   Trạng thái: active
   Giáo viên: Nguyễn Văn Giáo
   Tiến độ: 65%

📅 LỊCH HỌC SẮP TỚI:
1. Tiếng Anh sáng thứ 2
   Ngày: 17/06/2026 (Thứ Tư)
   Giờ: 18:00 - 20:00
   Phòng: Phòng 106
   Chủ đề: Buổi 6

════════════════════════════════
❓ [STUDENT QUESTION]
════════════════════════════════
Hôm nay tôi học gì?";

$payloadLong = [
    'contents' => [['parts' => [['text' => $promptLong]]]],
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 500,
        'topP' => 0.95,
        'topK' => 40
    ]
];

$startTime = microtime(true);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "{$endpoint}/models/{$model}:generateContent");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloadLong));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Goog-Api-Key: ' . $apiKey
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$duration = round((microtime(true) - $startTime) * 1000);
curl_close($ch);

if ($httpCode == 200) {
    $data = json_decode($response, true);
    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'NO TEXT';
    echo "✅ Response (Thời gian: {$duration}ms):\n";
    echo "┌─────────────────────────────────────────────────┐\n";
    echo $text . "\n";
    echo "└─────────────────────────────────────────────────┘\n";
    echo "📊 Độ dài: " . strlen($text) . " ký tự\n";
} else {
    echo "❌ HTTP {$httpCode}: {$response}\n";
}

echo "\n\n";

// ============================================
// TEST 2: PROMPT NGẮN GỌN (đi thẳng vào vấn đề)
// ============================================
echo "📝 TEST 2: PROMPT NGẮN GỌN (trực tiếp, ít rules)\n";
echo "─────────────────────────────────────────────────\n";

$promptShort = "Bạn là trợ lý AI của trung tâm ngoại ngữ. Trả lời bằng tiếng Việt, ngắn gọn, dùng emoji.

THÔNG TIN HỌC VIÊN:
- Khóa: Tiếng Anh A2 (65% hoàn thành)
- Giáo viên: Nguyễn Văn Giáo
- Lịch học hôm nay (17/06/2026):
  * Tiếng Anh sáng thứ 2
  * 18:00-20:00, Phòng 106, Buổi 6

CÂU HỎI: Hôm nay tôi học gì?

TRẢ LỜI (ngắn gọn, đi thẳng vào lịch học):";

$payloadShort = [
    'contents' => [['parts' => [['text' => $promptShort]]]],
    'generationConfig' => [
        'temperature' => 0.5,  // Giảm temperature để câu trả lời tập trung hơn
        'maxOutputTokens' => 200,  // Giảm max tokens để bắt buộc ngắn gọn
        'topP' => 0.9,
        'topK' => 30
    ]
];

$startTime = microtime(true);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "{$endpoint}/models/{$model}:generateContent");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloadShort));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Goog-Api-Key: ' . $apiKey
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$duration = round((microtime(true) - $startTime) * 1000);
curl_close($ch);

if ($httpCode == 200) {
    $data = json_decode($response, true);
    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'NO TEXT';
    echo "✅ Response (Thời gian: {$duration}ms):\n";
    echo "┌─────────────────────────────────────────────────┐\n";
    echo $text . "\n";
    echo "└─────────────────────────────────────────────────┘\n";
    echo "📊 Độ dài: " . strlen($text) . " ký tự\n";
} else {
    echo "❌ HTTP {$httpCode}: {$response}\n";
}

echo "\n\n";
echo "═══════════════════════════════════════════════════\n";
echo "💡 KẾT LUẬN & KHUYẾN NGHỊ\n";
echo "═══════════════════════════════════════════════════\n";
echo "So sánh:\n";
echo "  - Prompt DÀI: Nhiều rules/examples → AI bị \"rối\" → trả lời lan man\n";
echo "  - Prompt NGẮN: Đi thẳng vào vấn đề → AI tập trung → trả lời súc tích\n\n";
echo "Khuyến nghị:\n";
echo "  ✅ Giảm system instructions xuống 50-100 dòng\n";
echo "  ✅ Giảm temperature: 0.7 → 0.5 (tập trung hơn)\n";
echo "  ✅ Giảm maxOutputTokens: 500 → 200 (bắt buộc ngắn gọn)\n";
echo "  ✅ Đặt câu lệnh cuối prompt: 'TRẢ LỜI (ngắn gọn, đi thẳng):'\n";
echo "═══════════════════════════════════════════════════\n";
