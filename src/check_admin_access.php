<?php
/**
 * Check Admin Access and FAQ Creation Issue
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\ChatbotKnowledge;
use Illuminate\Support\Facades\DB;

echo "=== ADMIN ACCESS CHECK ===\n\n";

// Check admin users
echo "1. ADMIN USERS IN DATABASE:\n";
$admins = User::where('role', 'admin')->get();
if ($admins->isEmpty()) {
    echo "❌ NO ADMIN USERS FOUND!\n";
    echo "   Creating default admin user...\n";
    
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@admin.com',
        'password' => bcrypt('admin123'),
        'role' => 'admin',
        'phone' => '0123456789',
        'is_active' => true
    ]);
    
    echo "✅ Admin created:\n";
    echo "   Email: admin@admin.com\n";
    echo "   Password: admin123\n\n";
} else {
    echo "✅ Found " . $admins->count() . " admin user(s):\n";
    foreach ($admins as $admin) {
        echo "   - {$admin->email} (ID: {$admin->id}, Active: " . ($admin->is_active ? 'Yes' : 'No') . ")\n";
    }
    echo "\n";
}

// Check FAQ count
echo "2. FAQ ENTRIES IN DATABASE:\n";
$faqCount = ChatbotKnowledge::count();
echo "Total FAQs: $faqCount\n";

if ($faqCount > 0) {
    echo "\nRecent 5 FAQs:\n";
    $recentFaqs = ChatbotKnowledge::orderBy('created_at', 'desc')->take(5)->get();
    foreach ($recentFaqs as $faq) {
        echo "  - ID {$faq->id}: " . substr($faq->question, 0, 50) . "...\n";
        echo "    Category: {$faq->category}, Priority: {$faq->priority}, Active: " . ($faq->is_active ? 'Yes' : 'No') . "\n";
        echo "    Created: {$faq->created_at}\n";
    }
}
echo "\n";

// Test creating FAQ directly
echo "3. TEST CREATING FAQ DIRECTLY:\n";
try {
    $testFaq = ChatbotKnowledge::create([
        'category' => 'Khác',
        'question' => 'Test FAQ - ' . date('Y-m-d H:i:s'),
        'answer' => 'Đây là câu trả lời test được tạo bởi script kiểm tra.',
        'keywords' => 'test,script,check',
        'priority' => 50,
        'is_active' => true
    ]);
    
    echo "✅ FAQ created successfully!\n";
    echo "   ID: {$testFaq->id}\n";
    echo "   Question: {$testFaq->question}\n\n";
    
    // Try to retrieve it
    $retrieved = ChatbotKnowledge::find($testFaq->id);
    if ($retrieved) {
        echo "✅ FAQ can be retrieved from database\n\n";
    } else {
        echo "❌ FAQ was created but cannot be retrieved!\n\n";
    }
    
    // Clean up
    $testFaq->delete();
    echo "✅ Test FAQ cleaned up\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error creating FAQ: " . $e->getMessage() . "\n\n";
}

// Check middleware configuration
echo "4. CHECKING ROUTE MIDDLEWARE:\n";
try {
    $routes = app('router')->getRoutes();
    $faqRoutes = [];
    
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'chatbot-knowledge') !== false) {
            $faqRoutes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'middleware' => implode(', ', $route->middleware())
            ];
        }
    }
    
    if (!empty($faqRoutes)) {
        echo "✅ FAQ routes found:\n";
        foreach ($faqRoutes as $route) {
            echo "   [{$route['method']}] /{$route['uri']}\n";
            echo "      Name: {$route['name']}\n";
            echo "      Middleware: {$route['middleware']}\n\n";
        }
    } else {
        echo "❌ No FAQ routes found!\n\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error checking routes: " . $e->getMessage() . "\n\n";
}

echo "=== DIAGNOSIS ===\n\n";
echo "If you can login as admin and access http://127.0.0.1:8000/admin/chatbot-knowledge\n";
echo "but cannot create new FAQ, the issue is likely:\n\n";

echo "1. Form submission is not reaching the controller\n";
echo "   - Check browser Console (F12) for JavaScript errors\n";
echo "   - Check Network tab in F12 to see if POST request is sent\n\n";

echo "2. CSRF token mismatch\n";
echo "   - Clear browser cache and cookies\n";
echo "   - Run: php artisan cache:clear\n";
echo "   - Run: php artisan config:clear\n\n";

echo "3. Validation is failing silently\n";
echo "   - Form data doesn't meet validation rules\n";
echo "   - Check if all required fields are filled\n\n";

echo "INSTRUCTIONS:\n";
echo "1. Make sure you are logged in as admin (email from list above)\n";
echo "2. Go to http://127.0.0.1:8000/admin/chatbot-knowledge-debug\n";
echo "3. Click 'Test via AJAX' button\n";
echo "4. Look at the 'JavaScript Console Log' section on the page\n";
echo "5. Report what you see in that log\n";
