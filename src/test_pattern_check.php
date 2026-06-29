<?php

$message = "giao vien cua toi";

// Check if these patterns match
$patterns = [
    'hoc phi' => str_contains($message, 'hoc phi'),
    'phi' => str_contains($message, 'phi'),
    'gia' => str_contains($message, 'gia'),
    'giao' => str_contains($message, 'giao'),
    'vien' => str_contains($message, 'vien'),
];

echo "Message: '$message'\n\n";
echo "Pattern matches:\n";
foreach ($patterns as $pattern => $matches) {
    echo "  '$pattern': " . ($matches ? "✅ YES" : "❌ NO") . "\n";
}
