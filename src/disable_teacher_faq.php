<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ChatbotKnowledge;

echo "═══════════════════════════════════════════════════════════════\n";
echo "DISABLE TEACHER-RELATED FAQ ENTRIES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Get FAQ entries with teacher keywords
$teacherEntries = ChatbotKnowledge::where(function($query) {
    $query->where('question', 'LIKE', '%giáo viên%')
          ->orWhere('question', 'LIKE', '%giao vien%')
          ->orWhere('keywords', 'LIKE', '%giáo viên%')
          ->orWhere('keywords', 'LIKE', '%giao vien%')
          ->orWhere('category', 'Giáo viên');
})->where('is_active', 1)->get();

if ($teacherEntries->isEmpty()) {
    echo "✅ No active teacher FAQ entries found.\n";
    exit(0);
}

echo "Found " . $teacherEntries->count() . " active teacher FAQ entries:\n\n";

foreach ($teacherEntries as $entry) {
    echo "Entry #{$entry->id}: {$entry->question}\n";
}

echo "\n❓ Do you want to disable these entries? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim($line) !== 'y') {
    echo "\n❌ Aborted. No changes made.\n";
    exit(0);
}

echo "\n🔄 Disabling entries...\n\n";

foreach ($teacherEntries as $entry) {
    $entry->is_active = 0;
    $entry->save();
    echo "✅ Disabled Entry #{$entry->id}: {$entry->question}\n";
}

echo "\n✅ Done! All teacher FAQ entries have been disabled.\n";
echo "Now these questions will fallback to Gemini AI.\n";
