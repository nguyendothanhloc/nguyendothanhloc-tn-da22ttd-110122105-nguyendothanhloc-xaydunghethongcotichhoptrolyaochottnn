# Hướng dẫn test chức năng điểm danh

## 🚀 Server đang chạy

✅ **Laravel Development Server đang chạy tại:**
- **URL:** http://127.0.0.1:8000
- **Port:** 8000
- **Status:** RUNNING ✅

---

## 📝 Hướng dẫn test chi tiết

### BƯỚC 1: Đăng nhập tài khoản giáo viên

1. Mở trình duyệt, truy cập: **http://127.0.0.1:8000/login**
2. Đăng nhập với tài khoản giáo viên (teacher)
3. Sau khi đăng nhập, bạn sẽ vào dashboard giáo viên

---

### BƯỚC 2: Test chức năng điểm danh giáo viên

#### A. Vào trang lớp học
1. Từ menu, chọn **"Lớp học của tôi"** hoặc truy cập: `http://127.0.0.1:8000/teacher/classes`
2. Bạn sẽ thấy danh sách các lớp học mà bạn đang dạy

#### B. Chọn lớp cần điểm danh
1. Trong danh sách lớp, tìm lớp cần điểm danh
2. Click nút **"Điểm danh"** hoặc icon điểm danh
3. URL sẽ dạng: `http://127.0.0.1:8000/teacher/attendance/{class_id}`

#### C. Chọn buổi học
1. Bạn sẽ thấy danh sách tất cả buổi học của lớp đó
2. Chọn buổi học cần điểm danh
3. Click vào buổi học hoặc nút **"Điểm danh"**
4. URL sẽ dạng: `http://127.0.0.1:8000/teacher/attendance/{class_id}/{schedule_id}`

#### D. Điểm danh học viên
1. Bạn sẽ thấy danh sách học viên trong lớp (chỉ những học viên có enrollment status = 'approved')
2. Mỗi học viên có các lựa chọn:
   - ✅ **Present** (Có mặt)
   - ❌ **Absent** (Vắng mặt)
   - ⏰ **Late** (Đi muộn)
   - 📝 **Excused** (Có phép)
3. Có thể thêm ghi chú cho mỗi học viên (tùy chọn)
4. Click nút **"Lưu điểm danh"**
5. ✅ Kiểm tra thông báo: "Điểm danh đã được lưu thành công"

#### E. Kiểm tra lại điểm danh
1. Quay lại trang điểm danh của buổi học đó
2. Kiểm tra trạng thái điểm danh đã được lưu chính xác

---

### BƯỚC 3: Đăng xuất và đăng nhập tài khoản học viên

1. Click **"Đăng xuất"** (Logout)
2. Quay lại trang login: **http://127.0.0.1:8000/login**
3. Đăng nhập với tài khoản học viên (student)
   - **Quan trọng:** Học viên phải có enrollment status = 'approved' trong lớp đã điểm danh
4. Sau khi đăng nhập, bạn sẽ vào dashboard học viên

---

### BƯỚC 4: Test xem thống kê điểm danh học viên

#### A. Vào trang "Thời khóa biểu"
1. Từ menu, chọn **"Thời khóa biểu"** hoặc truy cập: `http://127.0.0.1:8000/student/schedule`
2. Trang này sẽ hiển thị lịch học của học viên

#### B. Kiểm tra Card thống kê điểm danh (phía trên)
✅ **Những gì cần kiểm tra:**

1. **Card thống kê theo từng lớp học:**
   - 📚 Tên lớp học hiển thị đúng
   - 📖 Tên khóa học hiển thị đúng
   - 🔢 **Tổng buổi học:** Kiểm tra số đúng không
   - ✅ **Số buổi có mặt:** Kiểm tra số đúng không (bao gồm Present + Late)
   - 📊 **Progress bar:**
     - Kiểm tra % hiển thị đúng không
     - Kiểm tra màu sắc:
       - 🟢 **Xanh lá** nếu ≥ 80%
       - 🟡 **Vàng** nếu 60-79%
       - 🔴 **Đỏ** nếu < 60%
   - ⚠️ **Cảnh báo:** Kiểm tra thông báo "Cần đạt ≥80% để nhận chứng chỉ" hiển thị khi < 80%

#### C. Kiểm tra cột "Điểm danh" trong bảng lịch học
✅ **Những gì cần kiểm tra:**

1. **Cột "Điểm danh" hiển thị:**
   - ✅ **Badge xanh với icon ✓** cho buổi "Present"
   - ❌ **Badge đỏ với icon ✗** cho buổi "Absent"
   - ⏰ **Badge vàng với icon ⏰** cho buổi "Late"
   - 📝 **Badge xanh dương với icon 📝** cho buổi "Excused"
   - **Badge xám với "-"** cho buổi đã qua nhưng chưa điểm danh
   - **Text xám với "-"** cho buổi chưa diễn ra

2. **Kiểm tra tooltip (title):**
   - Hover chuột vào từng badge
   - Kiểm tra tooltip hiển thị đúng: "Có mặt", "Vắng mặt", "Đi muộn", "Có phép", "Chưa điểm danh", "Chưa diễn ra"

#### D. Kiểm tra chú thích (Legend)
✅ **Những gì cần kiểm tra:**

1. **Chú thích trạng thái buổi học:**
   - [Hôm nay] (đỏ)
   - [Đã học] (xanh)
   - [Sắp tới] (xanh dương)
   - [Hủy] (đỏ)

2. **Chú thích điểm danh:** (MỚI)
   - [✓ Có mặt] (xanh)
   - [✗ Vắng mặt] (đỏ)
   - [⏰ Đi muộn] (vàng)
   - [📝 Có phép] (xanh dương)
   - [- Chưa điểm danh] (xám)

---

### BƯỚC 5: Test trang "Lịch sử điểm danh"

1. Từ menu, chọn **"Lịch sử điểm danh"** hoặc truy cập: `http://127.0.0.1:8000/student/attendance`
2. Kiểm tra:
   - ✅ Card thống kê tổng quan (tất cả các lớp)
   - ✅ Bảng lịch sử điểm danh chi tiết
   - ✅ Phân trang hoạt động

---

## 🧪 Test cases quan trọng

### Test Case 1: Giáo viên điểm danh - Lớp không có học viên approved
**Kịch bản:**
- Giáo viên vào lớp không có học viên nào có enrollment status = 'approved'
**Kết quả mong đợi:**
- Danh sách học viên trống hoặc thông báo "Chưa có học viên trong lớp"

---

### Test Case 2: Giáo viên điểm danh lại buổi học đã điểm danh
**Kịch bản:**
- Giáo viên điểm danh buổi học A
- Giáo viên vào lại trang điểm danh buổi học A
**Kết quả mong đợi:**
- Trạng thái điểm danh cũ được hiển thị
- Giáo viên có thể cập nhật lại điểm danh
- Dữ liệu mới ghi đè dữ liệu cũ (updateOrCreate)

---

### Test Case 3: Học viên xem thống kê - Tỷ lệ điểm danh thấp
**Kịch bản:**
- Học viên có tỷ lệ điểm danh < 80%
**Kết quả mong đợi:**
- Progress bar màu đỏ hoặc vàng
- Hiển thị cảnh báo: "⚠️ Cần đạt ≥80% để nhận chứng chỉ"

---

### Test Case 4: Học viên xem thống kê - Nhiều lớp
**Kịch bản:**
- Học viên đăng ký nhiều lớp (ví dụ: 3 lớp)
**Kết quả mong đợi:**
- Hiển thị 3 card thống kê, mỗi card cho 1 lớp
- Mỗi card có progress bar riêng với màu sắc phù hợp

---

### Test Case 5: Học viên xem cột điểm danh - Buổi chưa diễn ra
**Kịch bản:**
- Học viên xem lịch học có buổi học trong tương lai
**Kết quả mong đợi:**
- Cột điểm danh hiển thị "-" (text xám nhạt)
- Tooltip: "Chưa diễn ra"

---

### Test Case 6: Học viên xem cột điểm danh - Buổi đã qua nhưng chưa điểm danh
**Kịch bản:**
- Học viên xem lịch học có buổi đã qua (isPast = true) và schedule status = 'completed' nhưng chưa có dữ liệu điểm danh
**Kết quả mong đợi:**
- Cột điểm danh hiển thị "-" (badge xám)
- Tooltip: "Chưa điểm danh"

---

## 🐛 Lỗi thường gặp và cách fix

### Lỗi 1: "Student profile not found"
**Nguyên nhân:** User đăng nhập không có record trong bảng `students`
**Cách fix:**
- Đảm bảo user có role = 'student' và có record trong bảng `students`
- Kiểm tra foreign key: `students.user_id` = `users.id`

---

### Lỗi 2: "Bạn không có quyền truy cập lớp học này"
**Nguyên nhân:** Giáo viên đang cố truy cập lớp không phải của mình
**Cách fix:**
- Đảm bảo `classes.teacher_id` = ID giáo viên đăng nhập

---

### Lỗi 3: Card thống kê không hiển thị
**Nguyên nhân:** Biến `$attendanceStats` rỗng hoặc null
**Cách fix:**
- Kiểm tra học viên có enrollment với status = 'approved' không
- Kiểm tra có schedule trong class không
- Kiểm tra logic trong `StudentController@schedule()`

---

### Lỗi 4: Progress bar không có màu
**Nguyên nhân:** Blade condition không chạy đúng
**Cách fix:**
- Kiểm tra `$stats['percentage']` có giá trị hợp lệ không (0-100)
- Kiểm tra CSS class `bg-success`, `bg-warning`, `bg-danger` có load không

---

### Lỗi 5: Cột điểm danh không hiển thị icon
**Nguyên nhân:** 
- Không load được Bootstrap Icons
- Attendance relationship không eager load
**Cách fix:**
- Kiểm tra `<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">`
- Kiểm tra query trong controller có `->with(['attendances' => ...])` không

---

## 📊 Dữ liệu test mẫu

### Tạo dữ liệu test nhanh (nếu cần)

```sql
-- Tạo schedule test
INSERT INTO schedules (class_id, date, start_time, end_time, location, topic, status, created_at, updated_at)
VALUES (1, '2024-01-15', '18:00:00', '20:00:00', 'P.101', 'Grammar Basics', 'completed', NOW(), NOW());

-- Tạo attendance test (giả sử schedule_id = 1, student_id = 1)
INSERT INTO attendances (schedule_id, student_id, status, note, recorded_at, created_at, updated_at)
VALUES (1, 1, 'present', NULL, NOW(), NOW(), NOW());

-- Kiểm tra enrollment status
SELECT * FROM enrollments WHERE student_id = 1;
-- Nếu status != 'approved', cập nhật:
UPDATE enrollments SET status = 'approved' WHERE student_id = 1;
```

---

## ✅ Checklist test hoàn chỉnh

### Giáo viên:
- [ ] Đăng nhập giáo viên thành công
- [ ] Vào "Lớp học của tôi" thấy danh sách lớp
- [ ] Click "Điểm danh" vào được trang chọn buổi học
- [ ] Chọn buổi học, thấy danh sách học viên (status = approved)
- [ ] Điểm danh học viên (Present/Absent/Late/Excused)
- [ ] Lưu điểm danh thành công
- [ ] Quay lại trang điểm danh, thấy trạng thái cũ

### Học viên:
- [ ] Đăng nhập học viên thành công
- [ ] Vào "Thời khóa biểu" thấy lịch học
- [ ] Card thống kê hiển thị đúng số liệu
- [ ] Progress bar màu sắc chính xác theo %
- [ ] Cảnh báo hiển thị khi < 80%
- [ ] Cột "Điểm danh" hiển thị đúng icon/badge
- [ ] Tooltip hiển thị đúng khi hover
- [ ] Chú thích (Legend) hiển thị đầy đủ
- [ ] Vào "Lịch sử điểm danh" thấy chi tiết

---

## 🎯 Kết luận

**Server đang chạy tại:** http://127.0.0.1:8000

**Bắt đầu test ngay:**
1. Mở trình duyệt
2. Truy cập: http://127.0.0.1:8000/login
3. Đăng nhập giáo viên → Test điểm danh
4. Đăng xuất → Đăng nhập học viên → Test xem thống kê

**Nếu gặp lỗi:**
- Kiểm tra console log trong browser (F12)
- Kiểm tra Laravel log: `storage/logs/laravel.log`
- Kiểm tra server output trong terminal

**Chúc bạn test thành công! 🎉**
