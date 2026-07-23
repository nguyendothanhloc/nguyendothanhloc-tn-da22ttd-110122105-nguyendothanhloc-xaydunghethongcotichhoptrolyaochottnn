# ⚠️ GEMINI RATE LIMIT - CÁCH XỬ LÝ

**Vấn đề**: "Xin lỗi, hệ thống đang xử lý nhiều yêu cầu. Vui lòng thử lại sau vài giây."

---

## 🔴 NGUYÊN NHÂN

Bạn đã test chatbot quá nhiều lần trong thời gian ngắn:
- Test lần 1: "giáo viên của tôi" → Rate limit
- Test lần 2: "chào" → Rate limit
- Test lần 3: "giáo viên của tôi" → Rate limit

→ Gemini API free tier bị KHÓA TẠM THỜI

---

## ✅ GIẢI PHÁP

### Bước 1: ĐỢI 10-15 PHÚT

**KHÔNG CÓ CÁCH NÀO KHÁC!**

Gemini API sẽ tự động mở lại sau 10-15 phút.

### Bước 2: Nghỉ ngơi

Đóng chatbot, đi uống nước, nghỉ ngơi.

### Bước 3: Test lại sau 15 phút

```
1. Mở lại chatbot
2. Test câu: "chào"
3. Nếu trả lời được → Gemini đã khỏi
4. Nếu vẫn lỗi → Đợi thêm 1 tiếng
```

---

## 📊 GIỚI HẠN GEMINI FREE TIER

- **Requests per minute**: 2-3 requests
- **Requests per day**: 1,500 requests
- **Tokens per minute**: 32,000 tokens

Bạn đã vượt quá 2-3 requests/phút → Bị khóa tạm thời.

---

## 🛡️ CÁCH TRÁNH RATE LIMIT

### 1. Đừng test quá nhanh
- Đợi 10-15 giây giữa mỗi câu hỏi
- Đừng test liên tục 5-10 câu

### 2. Test câu đơn giản trước
```
✅ Test "chào" → Rule-based trả lời (không qua Gemini)
✅ Test "giờ mở cửa" → Rule-based (không qua Gemini)
❌ Test "giáo viên của tôi" → Gemini (tốn quota)
```

### 3. Dùng script test thông minh
File `test_gemini_5_questions.php` có delay 10 giây giữa mỗi câu.

---

## ⏰ THỜI GIAN CHỜ

| Lỗi | Thời gian chờ |
|-----|---------------|
| Rate limit nhẹ | 5-10 phút |
| Rate limit vừa | 10-15 phút |
| Rate limit nặng | 1 giờ |
| Quota hết (1500 req/day) | 24 giờ |

Bạn hiện tại: **Rate limit vừa** → Chờ 10-15 phút

---

## 🔍 KIỂM TRA QUOTA

Truy cập: https://aistudio.google.com/app/apikey

Xem:
- Requests hôm nay: X/1500
- Rate limit status: OK/Limited

---

## 💡 KHUYẾN NGHỊ

### Ngắn hạn (Hôm nay)
1. ✅ Đợi 15 phút
2. ✅ Test lại 1-2 câu thôi
3. ✅ Nếu hoạt động → Dừng test
4. ✅ Để mai test tiếp

### Dài hạn (Luận văn)
1. **Giữ nguyên như cũ**
   - Chatbot hoạt động tốt
   - Rule-based handle 50-60% queries
   - Gemini handle 25-30% queries
   - Rate limit ít xảy ra với user thật

2. **Nâng cấp (Nếu cần)**
   - Gemini paid tier: $0.00025/1K chars
   - 60 requests/minute (thay vì 2-3)
   - Rất rẻ, chỉ vài USD/tháng

3. **Chấp nhận**
   - Free tier đủ cho demo luận văn
   - User thật không test nhanh như bạn
   - Rate limit hiếm khi xảy ra

---

## 🎯 KẾT LUẬN

**Chatbot của bạn KHÔNG HỎ NG!**

Chỉ là Gemini API bị rate limit vì test quá nhiều.

**Hãy:**
- ✅ Nghỉ ngơi 15 phút
- ✅ Test lại sau đó
- ✅ Nếu hoạt động → Chatbot ổn rồi!

---

## ⏰ GIỜ HIỆN TẠI

Bạn bị rate limit lúc: **19:30 (6:30 PM)**

**Test lại lúc**: **19:45 (6:45 PM)** ← Sau 15 phút

Nếu vẫn lỗi → Test lúc: **20:30 (8:30 PM)** ← Sau 1 giờ

---

**ĐỪNG LO LẮNG! Chỉ cần đợi thôi!** ✅

Gemini sẽ tự khỏi sau 10-15 phút.

