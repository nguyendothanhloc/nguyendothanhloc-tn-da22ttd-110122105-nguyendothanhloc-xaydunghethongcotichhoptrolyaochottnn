# 📝 BỘ CÂU HỎI TEST TOÀN DIỆN CHO CHATBOT

**User Test**: `hocvien1@gmail.com` (Nguyễn Văn Tuấn)

**Expected Data**:
- 2 Enrollments: Tiếng Anh (Teacher: Nguyễn Văn Giáo), Tiếng Nhật (Teacher: Nguyễn Thị Cúc)
- 10 Schedules (từ 20/06/2026 đến 08/07/2026)
- Attendance: 100% (1 present, 0 absent, 0 late)
- Assessments: Empty (chưa có bài kiểm tra)

---

## 📚 NHÓM 1: KHÓA HỌC & ENROLLMENT (5 câu)

### 1. ✅ "giáo viên của tôi"
**Expected Response**:
```
📚 Giáo viên của bạn:

1️⃣ Khóa Tiếng Anh (English - beginner)
   👨‍🏫 Giáo viên: Nguyễn Văn Giáo

2️⃣ Khóa Tiếng Nhật (Japanese - beginner)
   👩‍🏫 Giáo viên: Nguyễn Thị Cúc

Bạn có muốn xem lịch học không?
```

### 2. "tôi đang học khóa nào?"
**Expected Response**:
```
📚 Bạn đang học 2 khóa:

1️⃣ Tiếng Anh (English - beginner)
   📍 Lớp: Tiếng Anh sáng thứ 2
   👨‍🏫 GV: Nguyễn Văn Giáo
   
2️⃣ Tiếng Nhật (Japanese - beginner)
   📍 Lớp: Tiếng Nhật
   👩‍🏫 GV: Nguyễn Thị Cúc
```

### 3. "trình độ lớp học của tôi"
**Expected Response**:
```
📚 Trình độ của bạn:

- Tiếng Anh: beginner (sơ cấp)
- Tiếng Nhật: beginner (sơ cấp)
```

### 4. "tôi đã hoàn thành bao nhiêu phần trăm?"
**Expected Response**:
```
📊 Tiến độ học tập:

- Tiếng Anh: [X]%
- Tiếng Nhật: [X]%
```

### 5. "trạng thái đăng ký của tôi"
**Expected Response**:
```
✅ Trạng thái đăng ký:

- Tiếng Anh: approved (đã phê duyệt)
- Tiếng Nhật: approved (đã phê duyệt)
```

---

## 📅 NHÓM 2: LỊCH HỌC (8 câu)

### 6. "lịch học của tôi"
**Expected Response**:
```
📅 Lịch học sắp tới của bạn:

1. Tiếng Nhật - 20/06/2026 (Saturday)
   ⏰ 19:00-21:00 | 📍 Phòng 202

2. Tiếng Anh - 22/06/2026 (Monday)
   ⏰ 18:00-20:00 | 📍 Phòng 104

3. Tiếng Nhật - 22/06/2026 (Monday)
   ⏰ 19:00-21:00 | 📍 Phòng 203

... (list 10 schedules)
```

### 7. "hôm nay tôi học gì?"
**Expected Response**: Depends on today's date
- If today matches a schedule: List that schedule
- If not: "Hôm nay bạn không có lịch học"

### 8. "ngày 22/06/2026 tôi học gì?"
**Expected Response**:
```
📅 Lịch học ngày 22/06/2026 (Monday):

1. Tiếng Anh
   ⏰ 18:00-20:00 | 📍 Phòng 104
   👨‍🏫 GV: Nguyễn Văn Giáo

2. Tiếng Nhật
   ⏰ 19:00-21:00 | 📍 Phòng 203
   👩‍🏫 GV: Nguyễn Thị Cúc
```

### 9. "thứ 2 tới tôi học gì?"
**Expected Response**:
```
📅 Thứ 2 tới (22/06/2026 hoặc 29/06/2026):

- Tiếng Anh: 18:00-20:00, Phòng [X]
- Tiếng Nhật: 19:00-21:00, Phòng [X]
```

### 10. "buổi học tiếp theo là khi nào?"
**Expected Response**:
```
📅 Buổi học tiếp theo:

Tiếng Nhật - 20/06/2026 (Saturday)
⏰ 19:00-21:00
📍 Phòng 202
👩‍🏫 GV: Nguyễn Thị Cúc
```

### 11. "tôi học ở phòng nào?"
**Expected Response**:
```
📍 Các phòng học của bạn:

Tiếng Anh: Phòng 104, 108, 101, 110, 103
Tiếng Nhật: Phòng 202, 203, 204, 205
```

### 12. "lớp tiếng Anh học lúc mấy giờ?"
**Expected Response**:
```
⏰ Lịch lớp Tiếng Anh:
18:00 - 20:00 (2 tiếng)
```

### 13. "lớp tiếng Nhật học lúc mấy giờ?"
**Expected Response**:
```
⏰ Lịch lớp Tiếng Nhật:
19:00 - 21:00 (2 tiếng)
```

---

## ✅ NHÓM 3: ĐIỂM DANH (4 câu)

### 14. "tỷ lệ điểm danh của tôi"
**Expected Response**:
```
✅ Tỷ lệ điểm danh của bạn: 100% 🎉

Tổng: 1 buổi
- Có mặt: 1 buổi ✅
- Vắng: 0 buổi
- Đi muộn: 0 lần

Tuyệt vời! Bạn đang duy trì điểm danh rất tốt!
```

### 15. "tôi đã vắng bao nhiêu buổi?"
**Expected Response**:
```
✅ Bạn chưa vắng buổi nào! (0 buổi)

Tỷ lệ tham gia: 100%
```

### 16. "tôi có đi muộn không?"
**Expected Response**:
```
✅ Bạn chưa đi muộn lần nào! (0 lần)

Tuyệt vời! Tiếp tục duy trì nhé!
```

### 17. "tôi đã học được bao nhiêu buổi?"
**Expected Response**:
```
📚 Bạn đã học được: 1 buổi
✅ Có mặt: 1 buổi (100%)
```

---

## 📊 NHÓM 4: ĐIỂM SỐ & ASSESSMENT (3 câu)

### 18. "điểm số của tôi"
**Expected Response**:
```
📊 Kết quả học tập:

Bạn chưa có bài kiểm tra nào.

Các bài kiểm tra sẽ được thông báo trong lớp học.
```

### 19. "điểm trung bình của tôi"
**Expected Response**:
```
📊 Điểm trung bình:

Chưa có dữ liệu điểm số.

Bạn sẽ có điểm sau khi hoàn thành bài kiểm tra đầu tiên.
```

### 20. "tôi có bài kiểm tra nào không?"
**Expected Response**:
```
📊 Chưa có bài kiểm tra nào được ghi nhận.

Giáo viên sẽ thông báo lịch kiểm tra trên lớp.
```

---

## 👤 NHÓM 5: THÔNG TIN CÁ NHÂN (3 câu)

### 21. "thông tin của tôi"
**Expected Response**:
```
👤 THÔNG TIN CÁ NHÂN

📛 Họ tên: Nguyễn Văn Tuấn
📧 Email: hocvien1@gmail.com
📚 Trình độ: beginner
🎯 Sở thích: Tieng Anh, Du lich
```

### 22. "email của tôi"
**Expected Response**:
```
📧 Email của bạn: hocvien1@gmail.com
```

### 23. "trình độ của tôi"
**Expected Response**:
```
📚 Trình độ của bạn: beginner (sơ cấp)
```

---

## 💰 NHÓM 6: THANH TOÁN (2 câu)

### 24. "học phí của tôi"
**Expected Response**: Depends on payment data in enrollments

### 25. "tôi đã thanh toán chưa?"
**Expected Response**: Depends on payment status

---

## 🧪 HOW TO TEST

### Option 1: Manual Testing (RECOMMENDED)
1. Login: `hocvien1@gmail.com` / `password`
2. Mở chatbot widget (góc dưới phải)
3. Hỏi từng câu trong danh sách trên
4. So sánh response với Expected Response

### Option 2: Automated Testing
Run: `php test_comprehensive_chatbot.php`

---

## ✅ PASS CRITERIA

Response được coi là **PASS** nếu:
1. ✅ Có đầy đủ dấu tiếng Việt
2. ✅ Có emoji phù hợp
3. ✅ Thông tin chính xác (ngày, giờ, tên giáo viên, phòng học)
4. ✅ Format rõ ràng (bullet points, line breaks)
5. ✅ Có câu hỏi gợi ý ở cuối
6. ✅ Response time < 10 giây

Response **FAIL** nếu:
- ❌ Thiếu dấu tiếng Việt
- ❌ Thông tin sai (ngày, giờ, tên)
- ❌ Response quá ngắn (< 30 từ)
- ❌ Không match với data trong Student Context
- ❌ Response time > 15 giây

---

**Test Date**: [To be filled]
**Tested By**: [To be filled]
**Pass Rate**: [To be filled] / 25 questions
