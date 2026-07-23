# 🤖 Hướng dẫn Setup Gemini API

## Bước 1: Lấy API Key

### Link chính thức:
👉 **https://aistudio.google.com/app/apikey**

### Các bước chi tiết:

1. **Truy cập Google AI Studio**
   - Mở trình duyệt và vào: https://aistudio.google.com/app/apikey
   - Đăng nhập bằng Google Account

2. **Tạo API Key**
   - Click nút **"Create API Key"** (màu xanh)
   - Chọn một trong hai options:
     - **"Create API key in new project"** (Tạo project mới - khuyến nghị)
     - **"Create API key in existing project"** (Dùng project có sẵn)

3. **Copy API Key**
   - API key sẽ hiển thị dạng: `AIzaSy...` (dài khoảng 39 ký tự)
   - Click **Copy** để copy API key
   - **LƯU Ý:** Giữ API key bí mật, không chia sẻ công khai

---

## Bước 2: Cấu hình trong Laravel

### File: `.env`

Mở file `.env` và cập nhật các dòng sau:

```env
# Google Gemini API Configuration
GEMINI_API_KEY=YOUR_ACTUAL_GEMINI_API_KEY  # ← Thay bằng API key thật từ https://aistudio.google.com/app/apikey
GEMINI_MODEL=gemini-1.5-flash
GEMINI_API_ENDPOINT=https://generativelanguage.googleapis.com/v1beta
GEMINI_TIMEOUT=30
GEMINI_TEMPERATURE=0.3
GEMINI_MAX_TOKENS=1000
GEMINI_TOP_P=0.95
GEMINI_TOP_K=40
```

### Giải thích các tham số:

| Tham số | Giá trị | Mô tả |
|---------|---------|-------|
| `GEMINI_API_KEY` | `AIzaSy...` | API key từ Google AI Studio |
| `GEMINI_MODEL` | `gemini-1.5-flash` | Model mới nhất (khuyến nghị) |
| `GEMINI_API_ENDPOINT` | URL | Endpoint của Gemini API v1beta |
| `GEMINI_TIMEOUT` | `30` | Thời gian chờ tối đa (giây) |
| `GEMINI_TEMPERATURE` | `0.3` | Độ sáng tạo (0-1, thấp = chính xác hơn) |
| `GEMINI_MAX_TOKENS` | `1000` | Độ dài response tối đa |
| `GEMINI_TOP_P` | `0.95` | Tham số sampling |
| `GEMINI_TOP_K` | `40` | Tham số sampling |

---

## Bước 3: Test API hoạt động

### Chạy test script:

```bash
php test_gemini_force.php
```

### Kết quả mong đợi:

```
✅ SUCCESS: Gemini AI responded!
Response: [Câu trả lời từ AI]
```

### Nếu gặp lỗi:

#### ❌ Lỗi 1: "API key not valid"
**Nguyên nhân:** API key sai hoặc không hợp lệ

**Giải pháp:**
1. Kiểm tra lại API key đã copy đúng chưa
2. Đảm bảo không có khoảng trắng thừa
3. Thử tạo API key mới

#### ❌ Lỗi 2: "models/gemini-pro is not found"
**Nguyên nhân:** Đang dùng model cũ đã deprecated

**Giải pháp:**
- Đổi `GEMINI_MODEL=gemini-pro` thành `GEMINI_MODEL=gemini-1.5-flash`

#### ❌ Lỗi 3: "Rate limit exceeded"
**Nguyên nhân:** Gọi API quá nhiều lần

**Giải pháp:**
- Đợi vài phút rồi thử lại
- Free tier có giới hạn: 15 requests/minute

---

## Bước 4: Verify trong code

### File config: `config/gemini.php`

Kiểm tra file này có tồn tại và cấu hình đúng:

```php
<?php

return [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    'api_endpoint' => env('GEMINI_API_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta'),
    'timeout' => env('GEMINI_TIMEOUT', 30),
    'temperature' => env('GEMINI_TEMPERATURE', 0.3),
    'max_tokens' => env('GEMINI_MAX_TOKENS', 1000),
    'top_p' => env('GEMINI_TOP_P', 0.95),
    'top_k' => env('GEMINI_TOP_K', 40),
];
```

---

## Models có sẵn (tháng 6/2026)

| Model | Tốc độ | Chi phí | Use Case |
|-------|--------|---------|----------|
| `gemini-1.5-flash` | ⚡ Nhanh | 💰 Rẻ | **Khuyến nghị** - Chatbot |
| `gemini-1.5-pro` | 🐌 Chậm hơn | 💰💰 Đắt hơn | Complex tasks |
| `gemini-pro` | ❌ Deprecated | - | Không dùng nữa |

---

## API Limits (Free Tier)

- ✅ **15 requests per minute** (RPM)
- ✅ **1 million tokens per day**
- ✅ **1,500 requests per day** (RPD)

Nếu cần nhiều hơn, upgrade lên paid plan tại: https://ai.google.dev/pricing

---

## Troubleshooting

### 1. Clear config cache

```bash
php artisan config:clear
php artisan cache:clear
```

### 2. Kiểm tra API key đã load

```bash
php artisan tinker
>>> config('gemini.api_key')
```

### 3. Test trực tiếp API

```bash
curl "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=YOUR_API_KEY" \
-H 'Content-Type: application/json' \
-d '{"contents":[{"parts":[{"text":"Hello"}]}]}'
```

---

## ✅ Checklist hoàn thành

- [ ] Đã lấy được API key từ Google AI Studio
- [ ] Đã cập nhật `GEMINI_API_KEY` trong `.env`
- [ ] Đã đổi `GEMINI_MODEL=gemini-1.5-flash`
- [ ] Đã chạy `php artisan config:clear`
- [ ] Đã test với `php test_gemini_force.php`
- [ ] Chatbot đã trả lời đúng câu hỏi phức tạp

---

## 📚 Tài liệu tham khảo

- **Google AI Studio:** https://aistudio.google.com
- **API Documentation:** https://ai.google.dev/docs
- **Pricing:** https://ai.google.dev/pricing
- **Models List:** https://ai.google.dev/models/gemini

---

**Lưu ý quan trọng:**
- API key là bí mật - không commit vào Git
- Đã thêm `.env` vào `.gitignore`
- Nếu key bị leak, revoke ngay tại Google AI Studio
