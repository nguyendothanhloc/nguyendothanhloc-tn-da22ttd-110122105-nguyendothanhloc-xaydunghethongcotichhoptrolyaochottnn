# 🧪 TEST CHATBOT - LỊCH HỌC

## ✅ ĐÃ SỬA

### 1. **StudentController.php**
- ✅ Filter lớp `cancelled` khi lấy lịch học trong `dashboard()`
- ✅ Filter lớp `cancelled` khi lấy lịch học trong `schedule()`

### 2. **GeminiChatbotService.php**  
- ✅ Filter lớp `cancelled` trong `buildStudentContext()`
- ✅ Chatbot sẽ CHỈ đọc lịch học từ lớp ĐANG HOẠT ĐỘNG

---

## 🔍 KIỂM TRA DATABASE

### **Bước 1: Kiểm tra enrollment của học viên**

Đăng nhập: `hocvien1@gmail.com` / `password`

Chạy query trong MySQL:

```sql
-- 1. Kiểm tra student_id
SELECT id, user_id, level FROM students WHERE user_id = (SELECT id FROM users WHERE email = 'hocvien1@gmail.com');

-- 2. Kiểm tra enrollments (giả sử student_id = 1)
SELECT 
    e.id,
    e.status,
    c.name AS class_name,
    c.status AS class_status,
    co.name AS course_name
FROM enrollments e
JOIN classes c ON e.class_id = c.id
JOIN courses co ON c.course_id = co.id
WHERE e.student_id = 1;

-- 3. Kiểm tra schedules
SELECT 
    s.id,
    s.date,
    s.start_time,
    s.end_time,
    s.location,
    c.name AS class_name,
    c.status AS class_status
FROM schedules s
JOIN classes c ON s.class_id = c.id
WHERE c.id IN (SELECT class_id FROM enrollments WHERE student_id = 1)
AND s.date >= CURDATE()
ORDER BY s.date ASC
LIMIT 10;
```

---

## ❌ VẤN ĐỀ CÓ THỂ XẢY RA

### **Vấn đề 1: Enrollment status = 'pending' thay vì 'approved'**

**Nguyên nhân:** Học viên vừa đăng ký, admin chưa duyệt

**Giải pháp:** Admin duyệt enrollment:

```sql
-- Check status hiện tại
SELECT id, student_id, class_id, status FROM enrollments WHERE student_id = 1;

-- Nếu status = 'pending', đổi thành 'approved'
UPDATE enrollments SET status = 'approved' WHERE student_id = 1 AND status = 'pending';
```

---

### **Vấn đề 2: Lớp bị cancelled**

**Nguyên nhân:** Lớp học đã bị hủy bởi admin

**Giải pháp:** Admin tạo lớp mới hoặc mở lại lớp cũ:

```sql
-- Check status lớp học
SELECT id, name, status, start_date, end_date FROM classes WHERE id IN (SELECT class_id FROM enrollments WHERE student_id = 1);

-- Nếu status = 'cancelled', đổi thành 'active'
UPDATE classes SET status = 'active' WHERE id = X AND status = 'cancelled';
```

---

### **Vấn đề 3: Không có schedule trong database**

**Nguyên nhân:** Admin chưa tạo lịch học cho lớp

**Giải pháp:** Admin tạo schedule trong "Quản lý lớp học" → "Lịch học"

Hoặc chạy SQL test:

```sql
-- Tạo lịch học test (giả sử class_id = 1)
INSERT INTO schedules (class_id, date, start_time, end_time, location, topic, created_at, updated_at)
VALUES
(1, '2026-06-17', '18:00:00', '20:00:00', 'Phòng 101', 'Buổi 1: Giới thiệu', NOW(), NOW()),
(1, '2026-06-19', '18:00:00', '20:00:00', 'Phòng 101', 'Buổi 2: Ngữ pháp cơ bản', NOW(), NOW()),
(1, '2026-06-21', '18:00:00', '20:00:00', 'Phòng 101', 'Buổi 3: Từ vựng', NOW(), NOW());
```

---

## 🧪 TEST CHATBOT SAU KHI SỬA

### **Câu hỏi test 1: Lịch học hôm nay**
```
"Hôm nay tôi có lịch học không?"
```

**Kỳ vọng:**
- Nếu CÓ lịch: Hiển thị ngày, giờ, phòng học, giáo viên
- Nếu KHÔNG CÓ: "Hôm nay bạn không có lịch học."

---

### **Câu hỏi test 2: Lịch học tuần này**
```
"Lịch học tuần này của tôi thế nào?"
```

**Kỳ vọng:**
- Liệt kê tất cả lịch học từ hôm nay đến cuối tuần
- Format: Ngày + Giờ + Phòng + Chủ đề

---

### **Câu hỏi test 3: Lịch học sắp tới**
```
"Tôi sẽ học gì trong tuần tới?"
```

**Kỳ vọng:**
- Hiển thị lịch học 7 ngày tới
- Có thông tin chi tiết về chủ đề bài học

---

## 🚨 CÁCH KIỂM TRA CHATBOT ĐANG DÙNG DATA GÌ

### **Check logs Laravel**

```bash
tail -f d:\xamp\htdocs\khoaluan\storage\logs\laravel.log
```

Tìm dòng:
```
[timestamp] local.INFO: Student context built successfully {"student_id":1,"enrollments_count":X,"schedules_count":Y,...}
```

- Nếu `schedules_count = 0` → Không có lịch học trong DB
- Nếu `schedules_count > 0` → Chatbot ĐÃ NHẬN được dữ liệu lịch học

---

## 📊 KẾT QUẢ MONG ĐỢI

### ✅ **SAU KHI SỬA XONG:**

1. **Dashboard học viên:** CHỈ hiển thị lớp ĐANG HOẠT ĐỘNG (không có lớp cancelled)
2. **Lịch học:** CHỈ hiển thị lịch của lớp ĐANG HOẠT ĐỘNG
3. **Chatbot:** Đọc được lịch học và trả lời chính xác

---

## 🔧 DEBUG STEP-BY-STEP

**Nếu chatbot vẫn không đọc được lịch học:**

### 1. Check database có dữ liệu không?
```sql
SELECT COUNT(*) FROM schedules WHERE class_id IN (
    SELECT class_id FROM enrollments WHERE student_id = 1
) AND date >= CURDATE();
```
→ Nếu = 0: KHÔNG CÓ LỊCH HỌC

### 2. Check enrollment status
```sql
SELECT status FROM enrollments WHERE student_id = 1;
```
→ Phải là `'approved'`, nếu `'pending'` thì chatbot không lấy được

### 3. Check class status
```sql
SELECT c.status FROM enrollments e
JOIN classes c ON e.class_id = c.id
WHERE e.student_id = 1;
```
→ Phải là `'active'` hoặc `'ongoing'`, KHÔNG ĐƯỢC là `'cancelled'`

---

**Sau khi check xong, báo kết quả cho tôi:**
- Câu SQL nào trả về 0 rows?
- Chatbot trả lời gì khi bạn hỏi về lịch học?
- Logs có báo `schedules_count = 0` không?

Tôi sẽ sửa tiếp! 🚀
