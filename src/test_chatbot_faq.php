<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== KIỂM TRA FAQ DATABASE ===\n\n";

// 1. Kiểm tra tổng số FAQ
$totalFAQ = \App\Models\ChatbotKnowledge::where('is_active', 1)->count();
echo "1. Tổng số FAQ active: {$totalFAQ}\n\n";

// 2. Tìm FAQ về tiếng Nhật
echo "2. Tìm FAQ về tiếng Nhật:\n";
$japFAQ = \App\Models\ChatbotKnowledge::where(function($query) {
    $query->where('keywords', 'LIKE', '%tieng nhat%')
          ->orWhere('keywords', 'LIKE', '%japanese%')
          ->orWhere('question', 'LIKE', '%tiếng Nhật%');
})->where('is_active', 1)->first();

if ($japFAQ) {
    echo "   ✅ FOUND!\n";
    echo "   Question: {$japFAQ->question}\n";
    echo "   Keywords: {$japFAQ->keywords}\n";
    echo "   Answer: " . substr($japFAQ->answer, 0, 100) . "...\n\n";
} else {
    echo "   ❌ KHÔNG TÌM THẤY FAQ về tiếng Nhật\n\n";
}

// 3. Test searchKnowledgeBase function
echo "3. Test searchKnowledgeBase với message: 'Có dạy tiếng Nhật không?'\n";

// Simulate the search logic
$message = 'Có dạy tiếng Nhật không?';
$normalizedMessage = removeVietnameseAccents(trim($message));
echo "   Normalized: {$normalizedMessage}\n";

$activeEntries = \App\Models\ChatbotKnowledge::where('is_active', 1)
    ->orderBy('priority', 'desc')
    ->get();

echo "   Total active entries: {$activeEntries->count()}\n";

$matchingEntries = $activeEntries->filter(function ($entry) use ($normalizedMessage) {
    $normalizedQuestion = removeVietnameseAccents($entry->question);
    
    if (str_contains($normalizedQuestion, $normalizedMessage) || 
        str_contains($normalizedMessage, $normalizedQuestion)) {
        return true;
    }
    
    if (!empty($entry->keywords)) {
        $keywords = array_map('trim', explode(',', $entry->keywords));
        
        foreach ($keywords as $keyword) {
            $normalizedKeyword = removeVietnameseAccents($keyword);
            
            if (str_contains($normalizedMessage, $normalizedKeyword) || 
                str_contains($normalizedKeyword, $normalizedMessage)) {
                return true;
            }
        }
    }
    
    return false;
});

echo "   Matching entries: {$matchingEntries->count()}\n";

if ($matchingEntries->count() > 0) {
    $firstMatch = $matchingEntries->first();
    echo "   ✅ MATCH FOUND!\n";
    echo "   Question: {$firstMatch->question}\n";
    echo "   Answer: " . substr($firstMatch->answer, 0, 100) . "...\n";
} else {
    echo "   ❌ NO MATCH - Sẽ rơi xuống Gemini AI\n";
}

// Helper function
function removeVietnameseAccents(string $str): string
{
    $str = mb_strtolower($str, 'UTF-8');
    
    $vietnameseMap = [
        'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
        'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
        'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
        'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
        'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
        'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
        'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
        'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
        'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
        'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
        'đ' => 'd'
    ];
    
    return strtr($str, $vietnameseMap);
}

echo "\n=== KẾT THÚC ===\n";
