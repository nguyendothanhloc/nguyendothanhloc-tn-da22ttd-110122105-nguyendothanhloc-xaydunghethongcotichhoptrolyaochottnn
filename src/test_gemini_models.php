<?php

/**
 * Test script to list available Gemini models with new API key format
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "===========================================\n";
echo "GEMINI API - LIST AVAILABLE MODELS\n";
echo "===========================================\n\n";

$apiKey = config('gemini.api_key');
$endpoint = config('gemini.api_endpoint');

if (empty($apiKey)) {
    echo "❌ ERROR: GEMINI_API_KEY not configured\n";
    exit(1);
}

echo "API Key format: " . substr($apiKey, 0, 10) . "...\n";
echo "Endpoint: {$endpoint}\n\n";

echo "Testing API connection...\n";
echo "-------------------------------------------\n\n";

// Try to list models
$url = "{$endpoint}/models";

try {
    echo "Request URL: {$url}\n\n";
    
    // Test with header authentication (new format)
    $response = \Illuminate\Support\Facades\Http::timeout(30)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $apiKey,
        ])
        ->get($url);
    
    if ($response->successful()) {
        $data = $response->json();
        
        echo "✅ SUCCESS! API Key is valid.\n\n";
        echo "Available Models:\n";
        echo "===========================================\n";
        
        if (isset($data['models']) && is_array($data['models'])) {
            foreach ($data['models'] as $model) {
                $name = $model['name'] ?? 'N/A';
                $displayName = $model['displayName'] ?? 'N/A';
                $description = $model['description'] ?? 'No description';
                
                // Check if model supports generateContent
                $supportsGenerate = false;
                if (isset($model['supportedGenerationMethods']) && is_array($model['supportedGenerationMethods'])) {
                    $supportsGenerate = in_array('generateContent', $model['supportedGenerationMethods']);
                }
                
                if ($supportsGenerate) {
                    echo "\n✅ {$name}\n";
                    echo "   Display Name: {$displayName}\n";
                    echo "   Description: {$description}\n";
                    echo "   Supports: generateContent\n";
                }
            }
        } else {
            echo "No models found in response.\n";
            echo "\nFull response:\n";
            print_r($data);
        }
        
        echo "\n===========================================\n";
        
    } else {
        $statusCode = $response->status();
        $body = $response->body();
        
        echo "❌ API Request Failed\n";
        echo "Status Code: {$statusCode}\n";
        echo "Response:\n{$body}\n\n";
        
        // Try with query parameter (old format)
        echo "Trying with query parameter authentication...\n\n";
        
        $response2 = \Illuminate\Support\Facades\Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->get($url . '?key=' . $apiKey);
        
        if ($response2->successful()) {
            $data2 = $response2->json();
            echo "✅ SUCCESS with query parameter!\n\n";
            
            if (isset($data2['models'])) {
                foreach ($data2['models'] as $model) {
                    $name = $model['name'] ?? 'N/A';
                    echo "- {$name}\n";
                }
            }
        } else {
            echo "❌ Also failed with query parameter\n";
            echo "Status: " . $response2->status() . "\n";
            echo "Response: " . $response2->body() . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n===========================================\n";
