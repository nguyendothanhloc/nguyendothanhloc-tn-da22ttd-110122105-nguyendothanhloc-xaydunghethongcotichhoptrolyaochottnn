<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ChatbotKnowledge;

echo "=== FAQ CRUD TEST ===\n\n";

// Test 1: Create FAQ
echo "TEST 1: Create new FAQ\n";
echo "------------------------\n";

$faqData = [
    'category' => 'Khóa học',
    'question' => 'Khóa học Tiếng Anh có những cấp độ nào?',
    'answer' => 'Trung tâm có các cấp độ: Beginner (A1), Elementary (A2), Intermediate (B1), Upper-Intermediate (B2), Advanced (C1), Proficiency (C2)',
    'keywords' => 'cấp độ, level, trình độ, beginner, advanced',
    'priority' => 80,
    'is_active' => true
];

try {
    $faq = ChatbotKnowledge::create($faqData);
    echo "✓ FAQ created successfully\n";
    echo "  ID: {$faq->id}\n";
    echo "  Category: {$faq->category}\n";
    echo "  Question: {$faq->question}\n";
    echo "  Is Active: " . ($faq->is_active ? 'Yes' : 'No') . "\n";
    echo "  Priority: {$faq->priority}\n\n";
} catch (\Exception $e) {
    echo "✗ Failed to create FAQ: {$e->getMessage()}\n\n";
    exit(1);
}

// Test 2: Update FAQ
echo "TEST 2: Update FAQ\n";
echo "------------------------\n";

try {
    $faq->update([
        'question' => 'Khóa học Tiếng Anh có những cấp độ nào? (UPDATED)',
        'priority' => 90,
        'is_active' => true
    ]);
    
    echo "✓ FAQ updated successfully\n";
    echo "  Question: {$faq->question}\n";
    echo "  Priority: {$faq->priority}\n";
    echo "  Is Active: " . ($faq->is_active ? 'Yes' : 'No') . "\n\n";
} catch (\Exception $e) {
    echo "✗ Failed to update FAQ: {$e->getMessage()}\n\n";
}

// Test 3: Test is_active toggle
echo "TEST 3: Toggle is_active\n";
echo "------------------------\n";

echo "Original status: " . ($faq->is_active ? 'Active' : 'Inactive') . "\n";

$faq->is_active = false;
$faq->save();
echo "After toggle: " . ($faq->is_active ? 'Active' : 'Inactive') . "\n";

$faq->is_active = true;
$faq->save();
echo "After toggle back: " . ($faq->is_active ? 'Active' : 'Inactive') . "\n\n";

// Test 4: Test searchKnowledgeBase
echo "TEST 4: Search Knowledge Base\n";
echo "------------------------\n";

$searchResults = ChatbotKnowledge::where('is_active', true)
    ->where(function($query) {
        $query->where('question', 'LIKE', '%cấp độ%')
              ->orWhere('keywords', 'LIKE', '%level%');
    })
    ->get();

echo "Found {$searchResults->count()} active FAQ(s) matching 'cấp độ' or 'level'\n";
foreach ($searchResults as $result) {
    echo "  - ID {$result->id}: {$result->question}\n";
}
echo "\n";

// Test 5: Check all FAQs
echo "TEST 5: List All FAQs\n";
echo "------------------------\n";

$allFAQs = ChatbotKnowledge::orderBy('priority', 'desc')->get();
echo "Total FAQs in database: {$allFAQs->count()}\n";
foreach ($allFAQs as $f) {
    $status = $f->is_active ? '✓ Active' : '✗ Inactive';
    echo "  [{$status}] ID {$f->id}: {$f->question} (Priority: {$f->priority})\n";
}
echo "\n";

// Test 6: Delete test FAQ
echo "TEST 6: Delete Test FAQ\n";
echo "------------------------\n";

try {
    $faq->delete();
    echo "✓ FAQ deleted successfully (ID: {$faq->id})\n\n";
} catch (\Exception $e) {
    echo "✗ Failed to delete FAQ: {$e->getMessage()}\n\n";
}

echo "=== ALL TESTS COMPLETED ===\n";
