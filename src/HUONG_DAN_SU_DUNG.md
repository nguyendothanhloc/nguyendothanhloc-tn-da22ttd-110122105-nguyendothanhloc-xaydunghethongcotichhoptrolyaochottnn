# 📖 HƯỚNG DẪN SỬ DỤNG HỆ THỐNG QUẢN LÝ TRUNG TÂM NGOẠI NGỮ

## 🚀 KHỞI ĐỘNG HỆ THỐNG

### Bước 1: Khởi động server
```bash
cd d:\xamp\htdocs\khoaluan
php artisan serve
```

### Bước 2: Truy cập website
```
http://127.0.0.1:8000
```

---

## 👑 HƯỚNG DẪN SỬ DỤNG CHO ADMIN

### 🔐 Đăng nhập Admin
1. Truy cập: http://127.0.0.1:8000/login
2. Email: `admin1@admin.com`
3. Password: `password`

### 📋 CHỨC NĂNG CHÍNH

#### 1. Quản lý đăng ký học viên (QUAN TRỌNG!)
**Vị trí:** Admin Dashboard → "Xem đăng ký mới" HOẶC `/admin/enrollments`

**Khi học viên đăng ký:**
1. Dashboard sẽ hiển thị badge đỏ với số đơn chờ duyệt
2. Vào menu "Quản lý đăng ký học viên"
3. Đơn chờ duyệt sẽ có nền màu vàng
4. Click nút "Duyệt" để duyệt đơn
5. Click nút "Từ chối" để từ chối đơn

**Lưu ý:** SAU KHI DUYỆT, học viên mới có quyền truy cập lớp học!

#### 2. Tạo giáo viên mới
**Vị trí:** `/teachers` → "Tạo tài khoản giáo viên"

**Các bước:**
1. Điền thông tin cơ bản (tên, email, mật khẩu)
2. **QUAN TRỌNG:** Điền "Chuyên môn" (VD: Tiếng Anh, Tiếng Nhật, Tiếng Hàn)
3. Điền bằng cấp và giới thiệu (tùy chọn)
4. Click "Tạo tài khoản giáo viên"

**Lưu ý:** Chuyên môn dùng để lọc giáo viên khi tạo lớp học!

#### 3. Tạo lớp học mới
**Vị trí:** `/classes/create`

**Các bước:**
1. Chọn khóa học
2. Chọn giáo viên (hệ thống tự động lọc theo chuyên môn)
3. Điền tên lớp
4. Chọn ngày bắt đầu/kết thúc
5. Nhập sức chứa tối đa
6. Chọn ca học và thứ trong tuần
7. Click "Tạo lớp học"

#### 4. Quản lý khóa học
**Vị trí:** `/courses`

- Tạo khóa học mới
- Sửa thông tin khóa học
- Vô hiệu hóa khóa học

---

## 🎓 HƯỚNG DẪN SỬ DỤNG CHO HỌC VIÊN

### 🔐 Đăng nhập Học viên
1. Truy cập: http://127.0.0.1:8000/login
2. Email: `hocvien1@gmail.com`
3. Password: `password`

### 📋 CHỨC NĂNG CHÍNH

#### 1. Đăng ký học
**Vị trí:** Student Dashboard → "Xem khóa học"

**Các bước:**
1. Duyệt danh sách khóa học
2. Click "Xem chi tiết" trên khóa học muốn đăng ký
3. Xem danh sách lớp học của khóa đó
4. Click "Đăng ký ngay" trên lớp học
5. Xác nhận đăng ký
6. **CHỜ ADMIN DUYỆT** (status: "Chờ thanh toán")

**Lưu ý:** SAU KHI ADMIN DUYỆT, bạn mới thấy lớp học trong lịch!

#### 2. Xem lịch học
**Vị trí:** Menu → "Lịch học" HOẶC `/student/schedule`

**Hiển thị:**
- Tất cả lớp học ĐÃ ĐƯỢC DUYỆT
- Lịch học theo tuần với ngày giờ cụ thể
- Thông tin giáo viên

**Lưu ý:** Chỉ hiển thị lớp đã được admin duyệt (status = 'approved')!

#### 3. Xem điểm danh
**Vị trí:** Menu → "Điểm danh"

#### 4. Chat với chatbot
**Vị trí:** Nút chat góc dưới bên phải

**Có thể hỏi:**
- "Giáo viên của tôi là ai?"
- "Lịch học của tôi?"
- "Có dạy tiếng Nhật không?"
- "Có học online không?"

---

## 👨‍🏫 HƯỚNG DẪN SỬ DỤNG CHO GIÁO VIÊN

### 🔐 Đăng nhập Giáo viên
1. Truy cập: http://127.0.0.1:8000/login
2. Email: `teacher1@teacher.com`
3. Password: `password`

### 📋 CHỨC NĂNG CHÍNH

#### 1. Xem lớp học của tôi
**Vị trí:** Teacher Dashboard → "Lớp học của tôi"

#### 2. Điểm danh học viên
**Vị trí:** Lớp học → "Điểm danh"

#### 3. Nhập điểm
**Vị trí:** Lớp học → "Đánh giá"

---

## ❓ GIẢI QUYẾT VẤN ĐỀ THƯỜNG GẶP

### ❌ Vấn đề: "Học viên đăng ký mà admin không thấy"

**Nguyên nhân:** Admin chưa vào đúng menu

**Giải pháp:**
1. Đăng nhập admin
2. Vào `/admin/enrollments` (KHÔNG PHẢI `/enrollments`)
3. Hoặc click "Xem đăng ký mới" trên dashboard
4. Nếu có đơn pending, sẽ hiển thị nền vàng

### ❌ Vấn đề: "Đăng ký 2 lớp nhưng lịch chỉ hiển thị 1 lớp"

**Nguyên nhân:** Lớp thứ 2 chưa được admin duyệt HOẶC lớp không có schedule

**Giải pháp:**
1. Kiểm tra status enrollment:
   - Login admin → `/admin/enrollments`
   - Xem status có phải "Đã thanh toán" (approved) không
   - Nếu "Chờ thanh toán" → Click "Duyệt"

2. Kiểm tra schedule:
   - Lớp học phải có lịch học (schedules)
   - Nếu không có → Admin cần tạo schedule cho lớp đó

### ❌ Vấn đề: "Tạo giáo viên rồi mà không thấy trong dropdown khi tạo lớp"

**Nguyên nhân:** Giáo viên không có "Chuyên môn" HOẶC chuyên môn không khớp với khóa học

**Giải pháp:**
1. Khi tạo giáo viên, PHẢI điền "Chuyên môn"
2. Chuyên môn phải khớp với language của khóa học
   - VD: Khóa học "Tiếng Nhật" → Chuyên môn: "Tiếng Nhật" hoặc "Japanese"
3. Nếu không khớp → Hệ thống vẫn hiển thị tất cả giáo viên (với cảnh báo màu vàng)

### ❌ Vấn đề: "Không đăng nhập được"

**Giải pháp:**
1. Kiểm tra server đang chạy:
   ```bash
   php artisan serve
   ```

2. Xóa cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. Sử dụng tài khoản test:
   - Admin: admin1@admin.com / password
   - Teacher: teacher1@teacher.com / password
   - Student: hocvien1@gmail.com / password

---

## 🔧 LỖI NGHIÊM TRỌNG VÀ CÁCH SỬA

### Chạy script kiểm tra hệ thống:
```bash
php full_system_check.php
```

Script này sẽ:
- ✅ Kiểm tra kết nối database
- ✅ Kiểm tra tài khoản người dùng
- ✅ Kiểm tra courses & classes
- ✅ Kiểm tra enrollment workflow
- ✅ Kiểm tra lịch học
- ✅ TỰ ĐỘNG SỬA lỗi về duplicate enrollments và class capacity

---

## 📊 FLOW ĐĂNG KÝ HỌC ĐÚNG

```
1. HỌC VIÊN: Đăng ký lớp học
   ↓
2. HỆ THỐNG: Tạo enrollment với status = 'pending'
   ↓
3. ADMIN: Nhận thông báo trên dashboard (badge đỏ)
   ↓
4. ADMIN: Vào /admin/enrollments
   ↓
5. ADMIN: Click "Duyệt" trên đơn đăng ký
   ↓
6. HỆ THỐNG: Đổi status = 'approved'
   ↓
7. HỌC VIÊN: Thấy lớp học trong "Lịch học"
   ↓
8. HỌC VIÊN: Có thể xem điểm danh, điểm số
```

**LƯU Ý QUAN TRỌNG:** 
- Status 'pending' = "Chờ thanh toán" (CHỜ ADMIN DUYỆT)
- Status 'approved' = "Đã thanh toán" (ĐÃ ĐƯỢC DUYỆT)
- Chỉ enrollment với status 'approved' mới hiển thị trong lịch học!

---

## 🎯 CHECKLIST TRƯỚC KHI BẢO VỆ

- [ ] Server đang chạy: `php artisan serve`
- [ ] Database đã có dữ liệu mẫu (courses, classes, teachers, students)
- [ ] Test đăng nhập với cả 3 loại tài khoản (admin, teacher, student)
- [ ] Test flow đăng ký học: Student đăng ký → Admin duyệt → Student thấy lịch
- [ ] Test tạo giáo viên mới (nhớ điền chuyên môn!)
- [ ] Test tạo lớp học mới
- [ ] Test chatbot (hỏi "Có dạy tiếng Nhật không?")
- [ ] Chạy `php full_system_check.php` để đảm bảo không có lỗi

---

## 📞 LIÊN HỆ

Nếu gặp lỗi, chạy:
```bash
php full_system_check.php
```

Và gửi kết quả để được hỗ trợ!

---

**CẬP NHẬT:** 2026-06-15
**TRẠNG THÁI:** ✅ HỆ THỐNG HOẠT ĐỘNG TỐT
