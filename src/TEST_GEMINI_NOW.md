# 🚀 TEST GEMINI NGAY - HƯỚNG DẪN NHANH

**Trạng thái:** ✅ Backend và Frontend đã khởi động!

---

## 📡 SERVER STATUS

✅ **Backend:** http://127.0.0.1:8000 (Terminal ID: 2)  
✅ **Frontend:** Vite dev server đang chạy (Terminal ID: 3)

---

## 🎯 3 BƯỚC TEST NHANH (2 PHÚT)

### **Bước 1: Mở website**
```
http://127.0.0.1:8000
```

### **Bước 2: Login**
- Email: `hocvien1@gmail.com`
- Password: `password`

### **Bước 3: Mở chatbot** (góc dưới bên phải)
Click vào icon chat bubble 💬

---

## 🧪 TEST 3 CÂU ĐƠN GIẢN NHẤT

### ✅ Test #1: "Xin chào"
**Mục đích:** Kiểm tra greeting response

**Mong đợi:**
```
👋 Chào bạn! Mình là EduBot...

Mình có thể giúp bạn:
📅 Xem lịch học
📊 Kiểm tra điểm
💰 Thông tin thanh toán

Bạn cần hỗ trợ gì nhé? 😊
```

**Đánh giá:**
- ✅ Response dài 50-100 từ
- ✅ Có emoji 👋📅📊💰
- ✅ Có danh sách (bullet points)
- ✅ Kết thúc bằng câu hỏi

---

### ✅ Test #2: "Hôm nay tôi học gì?"
**Mục đích:** Kiểm tra lịch học chi tiết

**Mong đợi:**
```
📅 Lịch học hôm nay của bạn:

✅ Tiếng Anh - Buổi 6
⏰ 18:00-20:00
📍 Phòng 106
👨‍🏫 Giáo viên: Nguyễn Văn Giáo

💡 Nhớ mang sách giáo trình nhé!
Bạn muốn xem lịch tuần này không?
```

**Đánh giá:**
- ✅ Có đầy đủ: giờ, phòng, giáo viên
- ✅ Response 60-120 từ (KHÔNG phải "Hôm nay bạn học Tiếng Anh" - 5 từ)
- ✅ Có emoji 📅✅⏰📍👨‍🏫
- ✅ Kết thúc bằng câu hỏi gợi ý

---

### ✅ Test #3: "Điểm của tôi thế nào?"
**Mục đích:** Kiểm tra liệt kê chi tiết điểm

**Mong đợi:**
```
📊 Kết quả học tập của bạn:

✅ Kiểm tra giữa kỳ: 8.5/10 (Khá)
✅ Bài tập về nhà: 9.0/10 (Giỏi)
📈 Điểm trung bình: 8.75/10

🎉 Bạn học rất tốt! Tiếp tục phát huy nhé!
Bạn muốn xem chi tiết từng bài kiểm tra không?
```

**Đánh giá:**
- ✅ Liệt kê từng bài kiểm tra cụ thể
- ✅ Có điểm trung bình
- ✅ Response 60-100 từ
- ✅ Kết thúc bằng câu hỏi

---

## 📊 BẢNG CHẤM ĐIỂM

| Test | Độ dài (50-150 từ) | Emoji | Chi tiết | Câu hỏi | Pass? |
|------|-------------------|-------|----------|---------|-------|
| #1 Xin chào | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| #2 Hôm nay học gì | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| #3 Điểm | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |

**Pass criteria:** ≥ 2/3 tests PASS

---

## ⚠️ NẾU GẶP LỖI

### Lỗi 503 (Model quá tải)
**Hiện tượng:**
```json
{
  "error": {
    "code": 503,
    "message": "This model is currently experiencing high demand..."
  }
}
```

**Giải pháp:**
1. ✅ Đợi 5-10 phút rồi test lại
2. ✅ Test vào giờ thấp điểm (sáng sớm hoặc đêm khuya)
3. ❌ KHÔNG spam hỏi liên tục → sẽ bị block IP

### Lỗi chatbot không xuất hiện
1. Kiểm tra Console (F12) → xem có lỗi JS không
2. Hard refresh: Ctrl + Shift + R
3. Xóa cache browser

### Lỗi "Cannot connect to API"
1. Kiểm tra backend: http://127.0.0.1:8000 có mở được không
2. Check terminal backend có lỗi không
3. Restart backend: `php artisan serve`

---

## 🎯 KẾT QUẢ MONG ĐỢI

### ✅ PASS (Prompt đã tối ưu thành công)
- Response dài **50-150 từ** (thay vì 5-10 từ)
- Có **emoji** 📅✅📊💡👋
- Có **chi tiết cụ thể** (giờ, phòng, điểm...)
- Có **câu hỏi gợi ý** cuối cùng
- Thời gian phản hồi: **2-8 giây** (bình thường)

### ❌ FAIL (Cần điều chỉnh thêm)
- Response < 20 từ (vẫn quá ngắn)
- Không có emoji hoặc format
- Mơ hồ: "Bạn có lịch học" (không nói cụ thể)
- Không có câu hỏi gợi ý

---

## 📸 SO SÁNH TRƯỚC/SAU

### TRƯỚC (Response quá ngắn):
```
User: "Hôm nay tôi học gì?"
Bot: "Hôm nay bạn học Tiếng Anh."
```
**Vấn đề:** Chỉ 5 từ, không có giờ/phòng/giáo viên

### SAU (Response đầy đủ):
```
User: "Hôm nay tôi học gì?"
Bot: "📅 Lịch học hôm nay của bạn:

✅ Tiếng Anh - Buổi 6
⏰ 18:00-20:00
📍 Phòng 106
👨‍🏫 Giáo viên: Nguyễn Văn Giáo

💡 Nhớ mang sách giáo trình nhé!
Bạn muốn xem lịch tuần này không?"
```
**Cải thiện:** 80-100 từ, đầy đủ chi tiết + gợi ý

---

## 🔧 TEST NÂNG CAO (Nếu có thời gian)

Sau khi test 3 câu cơ bản, thử thêm:

4. **"Lịch học tuần này thế nào?"** - Test nhiều lịch
5. **"Khi nào tôi tốt nghiệp?"** - Test logic suy luận
6. **"Trung tâm có dạy tiếng Đức không?"** - Test xử lý không có data
7. **"Làm thơ về học tiếng Anh"** - Test boundary (ngoài phạm vi)

---

## 📞 BÁO CÁO KẾT QUẢ

Sau khi test xong, cho tôi biết:

1. **Bao nhiêu câu PASS?** (X/3 hoặc X/7)
2. **Response dài bao nhiêu từ?** (ước lượng)
3. **Có emoji và format đẹp không?**
4. **Có gặp lỗi 503 không?**
5. **Thời gian phản hồi:** (2-5s hay 10s+?)

---

## 🎯 HÀNH ĐỘNG TIẾP THEO

### Nếu ≥ 2/3 tests PASS:
✅ **Thành công!** Prompt tối ưu đã hoạt động.
- Gemini giờ trả lời đầy đủ và chi tiết
- Có thể sử dụng cho production

### Nếu < 2/3 tests PASS:
⚠️ **Cần điều chỉnh thêm**
- Kiểm tra xem có lỗi 503 không
- Thử đổi model: `gemini-1.5-flash` (ổn định hơn)
- Tăng temperature lên 0.5-0.7

---

**Chúc bạn test thành công! 🚀**

---

## 📌 QUICK REFERENCE

- Backend: http://127.0.0.1:8000
- Login: hocvien1@gmail.com / password
- Test câu 1: "Xin chào"
- Test câu 2: "Hôm nay tôi học gì?"
- Test câu 3: "Điểm của tôi thế nào?"
- Pass criteria: Response 50-150 từ + emoji + chi tiết + câu hỏi
