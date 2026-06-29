# Script to enable PHP zip extension
$phpIniPath = "D:\xampp\php\php.ini"

Write-Host "Đang bật PHP zip extension..." -ForegroundColor Yellow

try {
    # Read the php.ini file
    $content = Get-Content $phpIniPath -Raw
    
    # Replace ;extension=zip with extension=zip
    $newContent = $content -replace ';extension=zip', 'extension=zip'
    
    # Write back to file
    Set-Content -Path $phpIniPath -Value $newContent -Force
    
    Write-Host "✓ Đã bật zip extension thành công!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Vui lòng:" -ForegroundColor Cyan
    Write-Host "1. Đóng terminal này" -ForegroundColor White
    Write-Host "2. Mở terminal mới" -ForegroundColor White
    Write-Host "3. Chạy lệnh: composer install --no-interaction" -ForegroundColor White
    
} catch {
    Write-Host "✗ Lỗi: Không thể sửa file php.ini" -ForegroundColor Red
    Write-Host "Vui lòng chạy PowerShell as Administrator và thử lại" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Hoặc tự sửa file D:\xampp\php\php.ini:" -ForegroundColor Cyan
    Write-Host "- Tìm dòng: ;extension=zip" -ForegroundColor White
    Write-Host "- Đổi thành: extension=zip" -ForegroundColor White
}
