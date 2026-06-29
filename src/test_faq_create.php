<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST FAQ CREATE ===\n\n";

try {
    // Test 1: Create new FAQ
    echo "1. Creating new FAQ...\n";
    $faq = \App\Models\ChatbotKnowledge::create([
        'category' => 'Test',
        'question' => 'Đây là câu hỏi test từ PHP script',
        'answer' => 'Đây là câu trả lời test dài hơn 20 ký tự để pass validation',
        'keywords' => 'test,php,script',
        'priority' => 55,
        'is_active' => true
    ]);
    echo "   ✅ SUCCESS: FAQ created with ID {$faq->id}\n\n";
    
    // Test 2: Update FAQ
    echo "2. Updating FAQ...\n";
    $faq->update([
        'question' => 'Câu hỏi đã được cập nhật',
        'answer' => 'Câu trả lời cũng đã được cập nhật với nội dung dài hơn 20 ký tự'
    ]);
    echo "   ✅ SUCCESS: FAQ updated\n\n";
    
    // Test 3: Read FAQ
    echo "3. Reading FAQ...\n";
    $readFaq = \App\Models\ChatbotKnowledge::find($faq->id);
    echo "   Question: {$readFaq->question}\n";
    echo "   Answer: {$readFaq->answer}\n";
    echo "   ✅ SUCCESS: FAQ read\n\n";
    
    // Test 4: Delete FAQ
    echo "4. Deleting FAQ...\n";
    $faq->delete();
    echo "   ✅ SUCCESS: FAQ deleted\n\n";
    
    echo "=== ALL TESTS PASSED ===\n";
    echo "FAQ CRUD operations work correctly from PHP!\n";
    
} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
