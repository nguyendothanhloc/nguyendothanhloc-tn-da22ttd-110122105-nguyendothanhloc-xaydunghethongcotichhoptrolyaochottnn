<?php

// Test chatbot API endpoint directly

$baseUrl = 'http://127.0.0.1:8000';
$loginUrl = $baseUrl . '/login';
$chatUrl = $baseUrl . '/api/chat';

// Test credentials
$email = 'hocvien1@gmail.com';
$password = 'password';

// Test questions
$testQuestions = [
    "tôi muốn học tiếng trung",
    "học phí tiếng trung"
];

echo "===== API TEST: TIẾNG TRUNG =====\n\n";

// Step 1: Login to get session cookie
echo "Step 1: Login...\n";
$ch = curl_init($loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
$response = curl_exec($ch);

// Extract CSRF token
preg_match('/<input type="hidden" name="_token" value="([^"]+)"/', $response, $matches);
$csrfToken = $matches[1] ?? null;

if (!$csrfToken) {
    echo "❌ Failed to get CSRF token\n";
    exit(1);
}

echo "✅ CSRF token: $csrfToken\n";

// Login with credentials
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    '_token' => $csrfToken,
    'email' => $email,
    'password' => $password
]));
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 400) {
    echo "❌ Login failed (HTTP $httpCode)\n";
    exit(1);
}

echo "✅ Login successful\n\n";

// Step 2: Get fresh CSRF token for API calls
$ch = curl_init($baseUrl . '/student/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
$dashboardHtml = curl_exec($ch);
curl_close($ch);

preg_match('/<meta name="csrf-token" content="([^"]+)"/', $dashboardHtml, $matches);
$apiCsrfToken = $matches[1] ?? $csrfToken;

echo "✅ API CSRF token: $apiCsrfToken\n\n";

// Step 3: Test each question
foreach ($testQuestions as $index => $question) {
    echo "===== TEST " . ($index + 1) . " =====\n";
    echo "Question: $question\n";
    
    $ch = curl_init($chatUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-CSRF-TOKEN: ' . $apiCsrfToken,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'message' => $question
    ]));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data && isset($data['response'])) {
            echo "✅ Response received:\n";
            echo "Type: " . ($data['type'] ?? 'unknown') . "\n";
            echo "Response: " . substr($data['response'], 0, 300) . "...\n";
        } else {
            echo "❌ Invalid response format\n";
            echo "Raw: $response\n";
        }
    } else {
        echo "❌ Request failed\n";
        echo "Response: $response\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Cleanup
@unlink('cookie.txt');

echo "===== TEST COMPLETE =====\n";
