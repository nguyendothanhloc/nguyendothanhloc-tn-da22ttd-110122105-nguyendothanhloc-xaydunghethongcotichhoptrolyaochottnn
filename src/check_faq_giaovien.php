<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ChatbotKnowledge;

echo "═══════════════════════════════════════════════════════════════\n";
echo "CHECK FAQ ENTRIES ABOUT 'GIÁO VIÊN'\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Get all FAQ entries (including inactive)
$allEntries = ChatbotKnowledge::where(function($query) {
    $query->where('question', 'LIKE', '%giáo viên%')
          ->orWhere('question', 'LIKE', '%giao vien%')
          ->orWhere('keywords', 'LIKE', '%giáo viên%')
          ->orWhere('keywords', 'LIKE', '%giao vien%');
})->get();

if ($allEntries->isEmpty()) {
    echo "✅ NO FAQ entries found with 'giáo viên' keywords\n";
} else {
    echo "❌ Found " . $allEntries->count() . " FAQ entries:\n\n";
    
    foreach ($allEntries as $entry) {
        $status = $entry->is_active ? '🟢 ACTIVE' : '🔴 INACTIVE';
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Entry #{$entry->id}: {$status}\n";
        echo "Question: {$entry->question}\n";
        echo "Keywords: " . ($entry->keywords ?? 'N/A') . "\n";
        echo "Priority: {$entry->priority}\n";
        echo "Category: {$entry->category}\n";
        echo "is_active: " . ($entry->is_active ? 'TRUE (1)' : 'FALSE (0)') . "\n";
        echo "\nAnswer preview: " . substr($entry->answer, 0, 100) . "...\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "CHECK ACTIVE FAQ ENTRIES (using ::active() scope)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$activeEntries = ChatbotKnowledge::active()
    ->where(function($query) {
        $query->where('question', 'LIKE', '%giáo viên%')
              ->orWhere('question', 'LIKE', '%giao vien%')
              ->orWhere('keywords', 'LIKE', '%giáo viên%')
              ->orWhere('keywords', 'LIKE', '%giao vien%');
    })->get();

if ($activeEntries->isEmpty()) {
    echo "✅ NO ACTIVE FAQ entries found\n";
} else {
    echo "❌ Found " . $activeEntries->count() . " ACTIVE entries (THESE WILL SHOW UP!):\n\n";
    
    foreach ($activeEntries as $entry) {
        echo "Entry #{$entry->id}: {$entry->question}\n";
    }
}
