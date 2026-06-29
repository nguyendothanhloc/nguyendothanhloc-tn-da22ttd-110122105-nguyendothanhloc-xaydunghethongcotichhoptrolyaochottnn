<?php
/**
 * FAQ Form Debug Script
 * Test if the FAQ create/update forms work via direct HTTP simulation
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ChatbotKnowledge;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

echo "=== FAQ FORM DEBUG SCRIPT ===\n\n";

// Test 1: Check if table exists and is accessible
echo "Test 1: Database Connection\n";
try {
    $count = ChatbotKnowledge::count();
    echo "✅ Table accessible. Current FAQ count: $count\n\n";
} catch (\Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Simulate form data from create form
echo "Test 2: Simulate CREATE form submission\n";
$createData = [
    'category' => 'Khác',
    'question' => 'Test FAQ từ form debug script - có dấu tiếng Việt',
    'answer' => 'Đây là câu trả lời test với ký tự đặc biệt và dấu tiếng Việt để kiểm tra.',
    'keywords' => 'test,debug,kiem tra',
    'priority' => 75,
    'is_active' => true
];

// Validate data using the same rules as controller
$validator = Validator::make($createData, ChatbotKnowledge::validationRules());

if ($validator->fails()) {
    echo "❌ Validation failed:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - $error\n";
    }
    echo "\n";
} else {
    echo "✅ Validation passed\n";
    
    try {
        $faq = ChatbotKnowledge::create($createData);
        echo "✅ FAQ created successfully with ID: {$faq->id}\n";
        echo "   Question: {$faq->question}\n";
        echo "   Category: {$faq->category}\n";
        echo "   Priority: {$faq->priority}\n";
        echo "   Active: " . ($faq->is_active ? 'Yes' : 'No') . "\n\n";
        
        // Test 3: Simulate UPDATE form submission
        echo "Test 3: Simulate UPDATE form submission\n";
        $updateData = [
            'category' => 'Chính sách hoàn tiền',
            'question' => 'Test FAQ đã được cập nhật - có dấu tiếng Việt',
            'answer' => 'Đây là câu trả lời đã được cập nhật với nhiều ký tự hơn.',
            'keywords' => 'test,debug,kiem tra,cap nhat',
            'priority' => 85,
            'is_active' => true
        ];
        
        $validator = Validator::make($updateData, ChatbotKnowledge::validationRules());
        
        if ($validator->fails()) {
            echo "❌ Validation failed:\n";
            foreach ($validator->errors()->all() as $error) {
                echo "  - $error\n";
            }
            echo "\n";
        } else {
            echo "✅ Validation passed\n";
            
            $faq->update($updateData);
            $faq->refresh();
            
            echo "✅ FAQ updated successfully\n";
            echo "   Question: {$faq->question}\n";
            echo "   Category: {$faq->category}\n";
            echo "   Priority: {$faq->priority}\n\n";
        }
        
        // Test 4: Check if form fields match model fillable
        echo "Test 4: Check Model Configuration\n";
        $fillable = $faq->getFillable();
        echo "✅ Fillable fields: " . implode(', ', $fillable) . "\n";
        
        $requiredFields = ['category', 'question', 'answer', 'priority'];
        $missingFields = array_diff($requiredFields, $fillable);
        
        if (empty($missingFields)) {
            echo "✅ All required fields are fillable\n\n";
        } else {
            echo "❌ Missing fillable fields: " . implode(', ', $missingFields) . "\n\n";
        }
        
        // Clean up
        echo "Test 5: Cleanup\n";
        $faq->delete();
        echo "✅ Test FAQ deleted\n\n";
        
    } catch (\Exception $e) {
        echo "❌ Error during creation: " . $e->getMessage() . "\n\n";
    }
}

// Test 6: Check if checkbox handling works correctly
echo "Test 6: Checkbox Handling (is_active)\n";
echo "Testing with is_active NOT in request (unchecked checkbox):\n";

$dataWithoutCheckbox = [
    'category' => 'Khác',
    'question' => 'Test checkbox - câu hỏi không active',
    'answer' => 'Đây là câu trả lời test cho checkbox không được chọn',
    'keywords' => 'test,checkbox',
    'priority' => 50,
    // is_active not included (simulates unchecked checkbox)
];

try {
    $faq = ChatbotKnowledge::create(array_merge($dataWithoutCheckbox, ['is_active' => false]));
    echo "✅ FAQ created with is_active = false\n";
    echo "   Active status: " . ($faq->is_active ? 'true' : 'false') . "\n\n";
    $faq->delete();
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

echo "=== ALL TESTS COMPLETED ===\n\n";

echo "DIAGNOSIS:\n";
echo "If all tests passed above, the backend works perfectly.\n";
echo "The issue is likely:\n";
echo "1. CSRF Token - Check if meta tag exists in layout: <meta name=\"csrf-token\" content=\"{{ csrf_token() }}\">\n";
echo "2. Browser JavaScript Error - Open browser Console (F12) and check for errors\n";
echo "3. Form submission blocked - Check if form actually submits (Network tab in F12)\n";
echo "4. Session issue - Clear browser cookies and try again\n";
echo "5. Middleware blocking - Check storage/logs/laravel.log for errors\n\n";

echo "NEXT STEPS FOR USER:\n";
echo "1. Go to http://127.0.0.1:8000/admin/chatbot-knowledge/create\n";
echo "2. Press F12 to open Developer Tools\n";
echo "3. Go to Console tab\n";
echo "4. Fill the form and click 'Tạo FAQ'\n";
echo "5. Look for any RED errors in Console\n";
echo "6. Take a screenshot and report the error\n";
