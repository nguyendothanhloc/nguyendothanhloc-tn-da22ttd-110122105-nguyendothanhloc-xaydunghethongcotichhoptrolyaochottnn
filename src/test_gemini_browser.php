<?php
/**
 * Simple browser-based test for Gemini API integration
 * Access via: http://127.0.0.1:8000/test_gemini_browser.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Gemini API Integration</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .config-box {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 15px 0;
            font-family: monospace;
        }
        .test-result {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .response-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        button {
            background: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        button:hover {
            background: #0056b3;
        }
        .spinner {
            display: none;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Test Gemini API Integration</h1>
        
        <?php
        $apiKey = config('gemini.api_key');
        $model = config('gemini.model');
        $endpoint = config('gemini.api_endpoint');
        
        if (empty($apiKey)) {
            echo '<div class="status error">❌ GEMINI_API_KEY chưa được cấu hình trong .env</div>';
            exit;
        }
        
        echo '<div class="status success">✅ API Key đã được cấu hình</div>';
        
        echo '<div class="config-box">';
        echo '<strong>Configuration:</strong><br>';
        echo 'API Key: ' . substr($apiKey, 0, 15) . '...<br>';
        echo 'Model: ' . $model . '<br>';
        echo 'Endpoint: ' . $endpoint . '<br>';
        echo '</div>';
        
        // Test API connection if requested
        if (isset($_GET['test'])) {
            echo '<div class="test-result">';
            echo '<h3>📊 Kết quả Test:</h3>';
            
            try {
                $service = new \App\Services\GeminiChatbotService();
                $question = "Xin chào! Bạn có thể giới thiệu về trung tâm không?";
                
                echo '<p><strong>Câu hỏi test:</strong> ' . htmlspecialchars($question) . '</p>';
                
                $startTime = microtime(true);
                $response = $service->generateResponse($question, 1);
                $endTime = microtime(true);
                
                $responseTime = round(($endTime - $startTime) * 1000, 2);
                
                echo '<div class="status success">';
                echo '✅ <strong>THÀNH CÔNG!</strong> Gemini API hoạt động hoàn hảo<br>';
                echo 'Response time: ' . $responseTime . 'ms';
                echo '</div>';
                
                echo '<div class="response-box">';
                echo '<strong>Response từ Gemini AI:</strong><br><br>';
                echo nl2br(htmlspecialchars($response));
                echo '</div>';
                
            } catch (\Exception $e) {
                echo '<div class="status error">';
                echo '❌ <strong>LỖI:</strong><br>';
                echo htmlspecialchars($e->getMessage());
                echo '</div>';
            }
            
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 30px;">
            <h3>🧪 Chạy Test:</h3>
            <p>Click nút bên dưới để test kết nối với Gemini API:</p>
            
            <button onclick="runTest()">🚀 Test Gemini API</button>
            <button onclick="location.reload()">🔄 Refresh</button>
            
            <div class="spinner" id="spinner"></div>
        </div>
        
        <div class="info" style="margin-top: 30px;">
            <strong>ℹ️ Lưu ý:</strong>
            <ul>
                <li>Test này sẽ gọi thật Gemini API (tính vào rate limit)</li>
                <li>Response time bình thường: 3-8 giây</li>
                <li>Nếu lỗi 429: đợi 1 phút rồi thử lại</li>
            </ul>
        </div>
    </div>
    
    <script>
        function runTest() {
            document.getElementById('spinner').style.display = 'block';
            window.location.href = '?test=1';
        }
    </script>
</body>
</html>
