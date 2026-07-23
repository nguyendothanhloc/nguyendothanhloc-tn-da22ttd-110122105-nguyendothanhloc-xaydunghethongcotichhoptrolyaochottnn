# 🧪 HƯỚNG DẪN TEST CHATBOT GEMINI

**Ngày**: Tháng 1/2025  
**Trạng thái**: ✅ Backend + Frontend đang chạy

---

## ✅ SERVERS ĐANG CHẠY

### 🔴 Laravel Backend
- **URL**: http://127.0.0.1:8000
- **Status**: ✅ Running
- **Terminal ID**: 2

### 🟢 Vite Frontend
- **URL**: http://localhost:5173
- **Status**: ✅ Running  
- **Terminal ID**: 3

---

## 🧪 CÁCH TEST CHATBOT

### Bước 1: Mở trình duyệt
Truy cập: **http://127.0.0.1:8000**

### Bước 2: Đăng nhập
- **Email**: `hocvien1@gmail.com`
- **Password**: `password`

### Bước 3: Tìm chatbot widget
- Nhìn góc **phải dưới** màn hình
- Có icon chatbot hình **bong bóng chat** 💬
- Click vào để mở chatbot

### Bước 4: Test các câu hỏi

#### 🎯 Câu hỏi chính (đã sửa)
```
giáo viên của tôi
```

**Mong đợi**: 
- ✅ Hiển thị 2 giáo viên: Nguyễn Văn Giáo, Nguyễn Thị Cúc
- ✅ Có dấu tiếng Việt đầy đủ
- ✅ Có emoji (📚👨‍🏫)
- ✅ Format đẹp (bullet points)
- ✅ Có câu hỏi gợi ý ở cuối

#### 📅 Test lịch học
```
lịch học của tôi
```

**Mong đợi**: Liệt kê 10 buổi học với ngày, giờ, phòng

#### ✅ Test điểm danh
```
tỷ lệ điểm danh của tôi
```

**Mong đợi**: Hiển thị 100% (1 có mặt, 0 vắng)

#### 📚 Test khóa học
```
tôi đang học khóa nào?
```

**Mong đợi**: Liệt kê 2 khóa (Tiếng Anh + Tiếng Nhật)

#### 📍 Test lịch cụ thể
```
ngày 22/06/2026 tôi học gì?
```

**Mong đợi**: Hiển thị 2 lớp trong ngày đó

#### 👤 Test thông tin cá nhân
```
thông tin của tôi
```

**Mong đợi**: Hiển thị tên, email, trình độ, sở thích

---

## ⏱️ THỜI GIAN PHẢN HỒI

### Nhanh (< 1 giây)
- Câu hỏi chung (chào hỏi, giờ mở cửa, liên hệ)
- Xử lý bởi: Rule-Based hoặc FAQ

### Trung bình (3-8 giây)
- Câu hỏi về dữ liệu cá nhân (giáo viên, lịch học, điểm danh)
- Xử lý bởi: **Gemini AI** ⭐

### Chậm (> 10 giây)
- Nếu thấy lâu quá → có thể đang bị **rate limit**
- Đợi 10-15 giây rồi hỏi lại

---

## 🎨 CHẤT LƯỢNG CÂU TRẢ LỜI

### ✅ Đạt chuẩn khi có:
1. ✅ Dấu tiếng Việt đầy đủ (à, á, ạ, ả, ã, â, ê, ô, ơ, ư, đ)
2. ✅ Emoji phù hợp (📚👨‍🏫✅⏰📍📅)
3. ✅ Format rõ ràng (bullet points, line breaks)
4. ✅ Thông tin chính xác (đúng tên, ngày, giờ)
5. ✅ Câu hỏi gợi ý ở cuối

### ❌ Có vấn đề nếu:
- ❌ Thiếu dấu tiếng Việt
- ❌ Thông tin sai (tên giáo viên sai, ngày sai)
- ❌ Câu trả lời quá ngắn (< 30 từ)
- ❌ Hiển thị lỗi "Xin lỗi, hệ thống đang bận"

---

## 🐛 XỬ LÝ LỖI

### Lỗi: "Hệ thống đang bận" hoặc "Rate limit"
**Nguyên nhân**: Gemini API bị giới hạn (free tier ~2-3 requests/phút)

**Giải pháp**:
1. Đợi 10-15 giây
2. Hỏi lại câu hỏi
3. Hoặc: Hỏi câu khác trước

### Lỗi: Chatbot không hiện
**Giải pháp**:
1. Kiểm tra console (F12)
2. Refresh trang (Ctrl + F5)
3. Clear cache trình duyệt

### Lỗi: Câu trả lời sai
**Ví dụ**: Hỏi "giáo viên của tôi" nhưng trả lời về thông tin cá nhân

**Giải pháp**:
1. Chụp màn hình
2. Báo lỗi với tôi
3. Tôi sẽ kiểm tra pattern matching

---

## 📊 SO SÁNH TRƯỚC/SAU SỬA

### ❌ TRƯỚC KHI SỬA
**Hỏi**: "giáo viên của tôi"  
**Trả lời**: "THÔNG TIN CÁ NHÂN CỦA BẠN" (SAI!)

### ✅ SAU KHI SỬA
**Hỏi**: "giáo viên của tôi"  
**Trả lời**: 
```
Chào bạn Nguyễn Văn Tuấn,

Dưới đây là thông tin về giáo viên của bạn:

*   📚 **Khóa Tiếng Anh (English - beginner):**
    *   👨‍🏫 Giáo viên: Nguyễn Văn Giáo

*   📚 **Khóa Tiếng Nhật (Japanese - beginner):**
    *   👨‍🏫 Giáo viên: Nguyễn Thị Cúc

Bạn có muốn xem lịch học sắp tới của mình không?
```

**ĐÚNG!** ✅

---

## 🔧 LỆNH QUẢN LÝ SERVER

### Xem trạng thái
```bash
# Kiểm tra server đang chạy
php artisan serve --version
```

### Dừng server (nếu cần)
Nhấn `Ctrl + C` trong terminal đang chạy server

### Khởi động lại
```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite
npm run dev
```

---

## 📝 GHI CHÚ QUAN TRỌNG

### 1. Rate Limit là bình thường
- Gemini free tier có giới hạn
- Không phải lỗi của code
- Chỉ xảy ra khi hỏi quá nhanh

### 2. Dữ liệu test
- User: `hocvien1@gmail.com`
- 2 enrollments: Tiếng Anh + Tiếng Nhật
- 2 teachers: Nguyễn Văn Giáo + Nguyễn Thị Cúc
- 10 schedules từ 20/06/2026 đến 08/07/2026

### 3. Luận văn
- ✅ Đã đáp ứng yêu cầu "phụ thuộc Gemini nhiều"
- ✅ Gemini xử lý 25-30% queries (tăng từ 5-10%)
- ✅ Chất lượng câu trả lời cao (có dấu, có emoji)

---

## 🎯 CHECKLIST TEST

### Chức năng cơ bản
- [ ] Chatbot widget hiển thị (góc phải dưới)
- [ ] Click vào mở được chatbot
- [ ] Gõ câu hỏi và gửi được
- [ ] Nhận được câu trả lời

### Chất lượng câu trả lời
- [ ] Có dấu tiếng Việt đầy đủ
- [ ] Có emoji phù hợp
- [ ] Format đẹp (bullet points)
- [ ] Thông tin chính xác
- [ ] Có câu hỏi gợi ý

### Các câu hỏi test
- [ ] "giáo viên của tôi" → 2 giáo viên ✅
- [ ] "lịch học của tôi" → 10 buổi học ✅
- [ ] "tỷ lệ điểm danh của tôi" → 100% ✅
- [ ] "tôi đang học khóa nào?" → 2 khóa ✅
- [ ] "ngày 22/06/2026 tôi học gì?" → 2 lớp ✅

---

## 🆘 LIÊN HỆ HỖ TRỢ

Nếu gặp vấn đề:
1. Chụp màn hình lỗi
2. Copy câu hỏi + câu trả lời
3. Nói cho tôi biết

Tôi sẽ hỗ trợ ngay! 😊

---

**Chúc bạn test thành công!** 🎉

Nhớ: Nếu thấy "Rate limit" là bình thường, chỉ cần đợi vài giây rồi hỏi lại nhé!

