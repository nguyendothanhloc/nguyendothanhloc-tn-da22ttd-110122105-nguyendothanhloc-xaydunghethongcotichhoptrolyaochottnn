# KHUNG BÀI CHI TIẾT - CHƯƠNG 2 & CHƯƠNG 3

**Tên đề tài:** Xây dựng hệ thống quản lý các khóa học có tích hợp trợ lý ảo cho trung tâm ngoại ngữ

---

## CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG

### ✅ 2.1 KHẢO SÁT YÊU CẦU (ĐÃ CÓ)
- 2.1.1 Khảo sát hiện trạng ✅
- 2.1.2 Yêu cầu chức năng ✅
- 2.1.3 Yêu cầu phi chức năng ✅

### ✅ 2.2 PHÂN TÍCH NGHIỆP VỤ (ĐÃ CÓ)
- 2.2.1 Phân tích nghiệp vụ quản lý người dùng ✅
- 2.2.2 Phân tích nghiệp vụ quản lý khóa học ✅
- 2.2.3 Phân tích nghiệp vụ quản lý học viên ✅
- 2.2.4 Phân tích nghiệp vụ quản lý giảng viên ✅
- 2.2.5 Phân tích nghiệp vụ trợ lý ảo AI ✅
- 2.2.6 Xác định các tác nhân của hệ thống ✅
- 2.2.7 Biểu đồ Use Case tổng thể ✅
- 2.2.8 Đặc tả các Use Case ✅

### ✅ 2.3 THIẾT KẾ HỆ THỐNG
- 2.3.1 Kiến trúc hệ thống ✅
- 2.3.2 Thiết kế theo mô hình MVC ✅
  - 2.3.2.1 Route ✅
  - 2.3.2.2 Controller ✅
  - 2.3.2.3 Model ✅
  - 2.3.2.4 View ✅
  - 2.3.2.5 Quy trình hoạt động của mô hình MVC ✅

### ⚠️ 2.3.3 BIỂU ĐỒ TUẦN TỰ (CHƯA ĐẦY ĐỦ)
**Hiện trạng:** Chỉ có mục 2.3.3.1 (Biểu đồ tuần tự chức năng đăng nhập) - CHƯA CÓ HÌNH

**CẦN BỔ SUNG:**


#### 2.3.3.1 Biểu đồ tuần tự chức năng đăng nhập
- **Nội dung:** Mô tả luồng đăng nhập từ User → Route → Controller → Model → Database
- **Cần:** Vẽ sơ đồ bằng PlantUML hoặc Draw.io
- **Tác nhân:** User, Route, Controller, Model, Database

#### 2.3.3.2 Biểu đồ tuần tự chức năng đăng ký khóa học
- **Nội dung:** Mô tả quy trình học viên đăng ký khóa học
- **Luồng:** User → Route → Controller → Model → Database → Kiểm tra điều kiện → Lưu đăng ký
- **Cần:** Vẽ sơ đồ sequence diagram

#### 2.3.3.3 Biểu đồ tuần tự chức năng chat với Chatbot AI  
- **Nội dung:** Mô tả luồng xử lý khi học viên chat với AI
- **Luồng:** User → Route → Controller → Kiểm tra FAQ → (Nếu không có) Gọi Gemini API → Trả kết quả
- **Cần:** Vẽ sơ đồ với 2 nhánh (có FAQ / không có FAQ)

---

### ❌ 2.3.4 THIẾT KẾ CƠ SỞ DỮ LIỆU (THIẾU HOÀN TOÀN - ƯU TIÊN CAO!)

**Đây là phần QUAN TRỌNG NHẤT cần bổ sung cho Chương 2!**

#### 2.3.4.1 Tổng quan cơ sở dữ liệu
**Nội dung cần viết:**
- Giới thiệu về database `language_center`
- Hệ quản trị: MySQL 8.0
- Tổng số bảng: 17 bảng
- Phân nhóm 8 nhóm chức năng:
  - Nhóm 1: Quản lý người dùng (users, students, teachers)
  - Nhóm 2: Quản lý khóa học & lớp học (courses, classes, enrollments)
  - Nhóm 3: Lịch học & điểm danh (schedules, attendances)
  - Nhóm 4: Đánh giá & kiểm tra (assessments, assessment_scores, certificates)
  - Nhóm 5: Thanh toán (payments)
  - Nhóm 6: Phản hồi (feedbacks)
  - Nhóm 7: Thông báo (notifications)
  - Nhóm 8: Chatbot (conversations, messages, chatbot_knowledge)

**Nguồn tham khảo:** File `GIAI_THICH_DATABASE.md` - Phần "Tổng quan hệ thống"


#### 2.3.4.2 Sơ đồ ERD (Entity Relationship Diagram)
**Nội dung cần viết:**
- Giới thiệu về sơ đồ ERD
- Ý nghĩa các ký hiệu (1-1, 1-N, khóa chính, khóa ngoại)
- **Hình:** Vẽ sơ đồ ERD đầy đủ 17 bảng

**Công cụ gợi ý:**
- draw.io (https://app.diagrams.net/)
- dbdiagram.io (https://dbdiagram.io/)
- MySQL Workbench (Generate từ database)

**Nguồn tham khảo:** File `GIAI_THICH_DATABASE.md` - Phần "Hướng dẫn vẽ mô hình ERD"

#### 2.3.4.3 Mô tả chi tiết các bảng
**Nội dung cần viết (17 bảng):**

**A. Nhóm Quản lý người dùng:**

**Bảng 1: users (Người dùng)**
- Mục đích: Lưu thông tin đăng nhập và thông tin cơ bản
- Các trường: id, name, email, password, role, phone, avatar, is_active, email_verified_at, created_at, updated_at
- Mối quan hệ: 1-1 với students, 1-1 với teachers, 1-N với notifications
- Giải thích vai trò (admin, teacher, student)

**Bảng 2: students (Hồ sơ học viên)**
- Mục đích: Thông tin bổ sung cho học viên
- Các trường: id, user_id, level, interests, created_at, updated_at
- Mối quan hệ: 1-N với enrollments, attendances, assessment_scores, certificates, feedbacks, conversations

**Bảng 3: teachers (Hồ sơ giáo viên)**
- Mục đích: Thông tin bổ sung cho giáo viên
- Các trường: id, user_id, specialization, qualifications, bio, created_at, updated_at
- Mối quan hệ: 1-N với classes

**B. Nhóm Quản lý khóa học & lớp học:**

**Bảng 4: courses (Khóa học)**
- Các trường: id, name, description, language, level, duration_weeks, price, is_active, created_at, updated_at
- Mối quan hệ: 1-N với classes, certificates

**Bảng 5: classes (Lớp học)**
- Các trường: id, course_id, teacher_id, name, start_date, end_date, max_capacity, current_enrollment, status, shift, weekdays, created_at, updated_at
- Mối quan hệ: N-1 với courses, N-1 với teachers, 1-N với enrollments, schedules, assessments, feedbacks


**Bảng 6: enrollments (Đăng ký học)**
- Các trường: id, student_id, class_id, enrollment_date, status, completion_percentage, created_at, updated_at
- Mối quan hệ: N-1 với students, N-1 với classes, 1-1 với payments

**C. Nhóm Lịch học & Điểm danh:**

**Bảng 7: schedules (Lịch học)**
- Các trường: id, class_id, date, start_time, end_time, location, topic, status, created_at, updated_at
- Mối quan hệ: N-1 với classes, 1-N với attendances

**Bảng 8: attendances (Điểm danh)**
- Các trường: id, schedule_id, student_id, status, note, recorded_at, created_at, updated_at
- Mối quan hệ: N-1 với schedules, N-1 với students

**D. Nhóm Đánh giá & Kiểm tra:**

**Bảng 9: assessments (Bài kiểm tra)**
- Các trường: id, class_id, name, type, max_score, assessment_date, description, created_at, updated_at
- Mối quan hệ: N-1 với classes, 1-N với assessment_scores

**Bảng 10: assessment_scores (Điểm kiểm tra)**
- Các trường: id, assessment_id, student_id, score, feedback, created_at, updated_at
- Mối quan hệ: N-1 với assessments, N-1 với students

**Bảng 11: certificates (Chứng chỉ)**
- Các trường: id, student_id, course_id, certificate_number, issue_date, pdf_path, created_at, updated_at
- Mối quan hệ: N-1 với students, N-1 với courses

**E. Nhóm Thanh toán:**

**Bảng 12: payments (Thanh toán)**
- Các trường: id, enrollment_id, amount, payment_method, status, due_date, paid_date, proof_image, note, created_at, updated_at
- Mối quan hệ: 1-1 với enrollments

**F. Nhóm Phản hồi:**

**Bảng 13: feedbacks (Đánh giá khóa học)**
- Các trường: id, student_id, class_id, course_rating, teacher_rating, comment, is_anonymous, created_at, updated_at
- Mối quan hệ: N-1 với students, N-1 với classes

**G. Nhóm Thông báo:**

**Bảng 14: notifications (Thông báo)**
- Các trường: id, user_id, type, title, message, is_read, sent_at, created_at, updated_at
- Mối quan hệ: N-1 với users


**H. Nhóm Chatbot:**

**Bảng 15: conversations (Cuộc trò chuyện)**
- Các trường: id, student_id, started_at, last_message_at, message_count, created_at, updated_at
- Mối quan hệ: N-1 với students, 1-N với messages

**Bảng 16: messages (Tin nhắn)**
- Các trường: id, conversation_id, sender_type, content, created_at, updated_at
- Mối quan hệ: N-1 với conversations

**Bảng 17: chatbot_knowledge (Kiến thức chatbot)**
- Các trường: id, category, question, answer, keywords, priority, is_active, created_at, updated_at
- Mối quan hệ: Bảng độc lập (không có quan hệ)

**Nguồn tham khảo:** File `GIAI_THICH_DATABASE.md` - Phần "Chi tiết các bảng"

#### 2.3.4.4 Mối quan hệ giữa các bảng
**Nội dung cần viết:**
- Sơ đồ cây mối quan hệ (dạng text hoặc hình)
- Giải thích các mối quan hệ chính:
  - users → students/teachers (1-1)
  - students → enrollments → classes (N-N thông qua bảng trung gian)
  - classes → schedules → attendances (1-N → 1-N)
  - classes → assessments → assessment_scores (1-N → 1-N)
  - students → conversations → messages (1-N → 1-N)

**Nguồn tham khảo:** File `GIAI_THICH_DATABASE.md` - Phần "Tổng kết mối quan hệ giữa các bảng"

#### 2.3.4.5 Ví dụ dữ liệu mẫu
**Nội dung cần viết:**
- Lấy 3-4 bảng quan trọng nhất làm ví dụ:
  - Bảng users: 3 dòng (admin, teacher, student)
  - Bảng courses: 2 dòng (2 khóa học mẫu)
  - Bảng classes: 2 dòng (2 lớp học)
  - Bảng enrollments: 2 dòng (học viên đăng ký lớp)

**Nguồn tham khảo:** File `GIAI_THICH_DATABASE.md` - Các ví dụ trong phần chi tiết từng bảng

---

### 2.3.5 THIẾT KẾ GIAO DIỆN (TÙY CHỌN - HOẶC ĐỂ CHƯƠNG 3)
**Nội dung nếu muốn thêm:**
- Wireframe/Mockup các màn hình chính
- Thiết kế responsive cho mobile

---


## CHƯƠNG 3: XÂY DỰNG VÀ ĐÁNH GIÁ HỆ THỐNG

### ❌ 3.1 MÔI TRƯỜNG PHÁT TRIỂN (CHƯA CÓ NỘI DUNG)

#### 3.1.1 Phần cứng
**Nội dung cần viết:**
- Máy chủ phát triển:
  - CPU: Intel Core i5/i7 hoặc tương đương
  - RAM: 8GB trở lên
  - Ổ cứng: SSD 256GB trở lên
- Máy trạm (cho người dùng):
  - Cấu hình tối thiểu để truy cập web browser

#### 3.1.2 Phần mềm
**Nội dung cần viết:**

**A. Hệ điều hành:**
- Windows 10/11, macOS, hoặc Linux

**B. Môi trường phát triển web:**
- **XAMPP 8.2.x** (hoặc phiên bản tương đương)
  - Apache Web Server 2.4.x
  - PHP 8.1.x hoặc 8.2.x
  - MySQL 8.0.x
  - phpMyAdmin (quản lý database)

**C. Framework và thư viện:**
- **Laravel 11.x** - PHP Framework chính
- **Composer 2.x** - Quản lý dependencies PHP
- **Node.js 18.x** và **NPM** - Quản lý front-end assets
- **Tailwind CSS** - Framework CSS
- **Laravel Breeze** - Authentication scaffolding

**D. API và dịch vụ bên ngoài:**
- **Google Gemini API** - Dịch vụ AI cho chatbot
- **Gemini API Key** - Cần đăng ký tại Google AI Studio

**E. Công cụ phát triển:**
- **Visual Studio Code** - Code editor
- **Git** - Version control
- **Postman** (tùy chọn) - Test API

**F. Trình duyệt web:**
- Google Chrome, Firefox, hoặc Edge (phiên bản mới nhất)


---

### ❌ 3.2 CÀI ĐẶT HỆ THỐNG (CHƯA CÓ NỘI DUNG)

#### 3.2.1 Cài đặt môi trường
**Nội dung cần viết:**

**Bước 1: Cài đặt XAMPP**
- Tải XAMPP từ https://www.apachefriends.org/
- Cài đặt và khởi động Apache, MySQL
- Kiểm tra: http://localhost

**Bước 2: Cài đặt Composer**
- Tải từ https://getcomposer.org/
- Kiểm tra: `composer --version`

**Bước 3: Cài đặt Node.js**
- Tải từ https://nodejs.org/
- Kiểm tra: `node --version` và `npm --version`

#### 3.2.2 Cài đặt Laravel Project
**Nội dung cần viết:**

**Bước 1: Clone hoặc tạo project Laravel**
```bash
composer create-project laravel/laravel language-center
cd language-center
```

**Bước 2: Cấu hình file .env**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=language_center
DB_USERNAME=root
DB_PASSWORD=

GEMINI_API_KEY=your_api_key_here
```

**Bước 3: Tạo database**
- Mở phpMyAdmin: http://localhost/phpmyadmin
- Tạo database mới tên: `language_center`

**Bước 4: Chạy migration**
```bash
php artisan migrate
```

**Bước 5: Chạy seeder (nếu có)**
```bash
php artisan db:seed
```

**Bước 6: Cài đặt dependencies front-end**
```bash
npm install
npm run dev
```

**Bước 7: Khởi động server**
```bash
php artisan serve
```
- Truy cập: http://127.0.0.1:8000

#### 3.2.3 Cấu hình Gemini API
**Nội dung cần viết:**
- Đăng ký API key tại: https://makersuite.google.com/app/apikey
- Copy API key vào file `.env`
- Test kết nối chatbot


---

### ❌ 3.3 CÁC CHỨC NĂNG CHÍNH (CHƯA CÓ NỘI DUNG)

**Cần bổ sung: Screenshot + Mô tả cho từng nhóm chức năng**

#### 3.3.1 Giao diện dành cho Khách (Guest)
**Chức năng 1: Trang chủ**
- **Mô tả:** Hiển thị thông tin trung tâm, giới thiệu khóa học
- **Screenshot:** Hình ảnh màn hình trang chủ
- **Thao tác:** Xem thông tin, điều hướng menu

**Chức năng 2: Xem danh sách khóa học**
- **Mô tả:** Hiển thị các khóa học đang mở, thông tin học phí, thời lượng
- **Screenshot:** Hình ảnh trang danh sách khóa học
- **Thao tác:** Click xem chi tiết từng khóa

**Chức năng 3: Xem chi tiết khóa học**
- **Mô tả:** Thông tin chi tiết khóa học, danh sách lớp đang mở
- **Screenshot:** Hình ảnh trang chi tiết khóa học
- **Thao tác:** Xem thông tin, nút đăng ký (chuyển sang đăng nhập)

**Chức năng 4: Đăng ký tài khoản**
- **Mô tả:** Form đăng ký học viên mới
- **Screenshot:** Hình ảnh form đăng ký
- **Thao tác:** Nhập thông tin → Submit → Tạo tài khoản

**Chức năng 5: Đăng nhập**
- **Mô tả:** Form đăng nhập cho tất cả người dùng
- **Screenshot:** Hình ảnh trang đăng nhập
- **Thao tác:** Nhập email/password → Đăng nhập

#### 3.3.2 Giao diện dành cho Học viên (Student)
**Chức năng 1: Dashboard học viên**
- **Mô tả:** Tổng quan thông tin học viên, lớp đang học, thông báo
- **Screenshot:** Hình ảnh dashboard
- **Thông tin hiển thị:** Tên, lớp học, lịch học sắp tới

**Chức năng 2: Đăng ký khóa học**
- **Mô tả:** Xem và đăng ký các khóa học/lớp học
- **Screenshot:** Hình ảnh trang đăng ký
- **Thao tác:** Chọn lớp → Xác nhận đăng ký

**Chức năng 3: Quản lý lớp học**
- **Mô tả:** Xem danh sách lớp đã đăng ký, thông tin giáo viên, lịch học
- **Screenshot:** Hình ảnh danh sách lớp học
- **Thông tin:** Tên lớp, giáo viên, thời gian, trạng thái


**Chức năng 4: Xem lịch học**
- **Mô tả:** Lịch các buổi học sắp tới
- **Screenshot:** Hình ảnh lịch học
- **Thông tin:** Ngày, giờ, phòng học, nội dung

**Chức năng 5: Xem điểm danh**
- **Mô tả:** Theo dõi tình trạng điểm danh các buổi học
- **Screenshot:** Hình ảnh bảng điểm danh
- **Thông tin:** Ngày, trạng thái (có mặt/vắng/muộn)

**Chức năng 6: Xem kết quả học tập**
- **Mô tả:** Xem điểm các bài kiểm tra, feedback giáo viên
- **Screenshot:** Hình ảnh bảng điểm
- **Thông tin:** Tên bài kiểm tra, điểm, nhận xét

**Chức năng 7: Chat với Chatbot AI**
- **Mô tả:** Trò chuyện với trợ lý ảo để hỏi thông tin
- **Screenshot:** Hình ảnh giao diện chat
- **Thao tác:** Nhập câu hỏi → Nhận câu trả lời từ AI

#### 3.3.3 Giao diện dành cho Giáo viên (Teacher)
**Chức năng 1: Dashboard giáo viên**
- **Mô tả:** Tổng quan lớp đang dạy, lịch giảng dạy
- **Screenshot:** Hình ảnh dashboard giáo viên
- **Thông tin:** Số lớp, số học viên, lịch dạy tuần này

**Chức năng 2: Quản lý lớp dạy**
- **Mô tả:** Xem danh sách lớp được phân công
- **Screenshot:** Hình ảnh danh sách lớp dạy
- **Thông tin:** Tên lớp, số học viên, lịch học

**Chức năng 3: Xem danh sách học viên**
- **Mô tả:** Danh sách học viên trong lớp
- **Screenshot:** Hình ảnh danh sách học viên
- **Thông tin:** Tên, email, số điện thoại

**Chức năng 4: Điểm danh học viên**
- **Mô tả:** Điểm danh từng buổi học
- **Screenshot:** Hình ảnh form điểm danh
- **Thao tác:** Chọn buổi học → Đánh dấu có mặt/vắng/muộn

**Chức năng 5: Quản lý bài kiểm tra**
- **Mô tả:** Tạo và quản lý các bài kiểm tra
- **Screenshot:** Hình ảnh danh sách bài kiểm tra
- **Thao tác:** Tạo mới, chỉnh sửa, xóa

**Chức năng 6: Chấm điểm**
- **Mô tả:** Nhập điểm cho từng học viên
- **Screenshot:** Hình ảnh form chấm điểm
- **Thao tác:** Chọn bài kiểm tra → Nhập điểm → Nhận xét


#### 3.3.4 Giao diện dành cho Quản trị viên (Admin)
**Chức năng 1: Dashboard admin**
- **Mô tả:** Tổng quan toàn bộ hệ thống
- **Screenshot:** Hình ảnh dashboard admin
- **Thông tin:** Số học viên, giáo viên, khóa học, lớp học, doanh thu

**Chức năng 2: Quản lý người dùng**
- **Mô tả:** CRUD (Create, Read, Update, Delete) tài khoản
- **Screenshot:** Hình ảnh danh sách người dùng
- **Thao tác:** Thêm, sửa, xóa, khóa tài khoản

**Chức năng 3: Quản lý khóa học**
- **Mô tả:** CRUD khóa học
- **Screenshot:** Hình ảnh danh sách khóa học
- **Thao tác:** Thêm mới, chỉnh sửa, xóa, kích hoạt/vô hiệu hóa

**Chức năng 4: Quản lý lớp học**
- **Mô tả:** CRUD lớp học, phân công giáo viên
- **Screenshot:** Hình ảnh danh sách lớp học
- **Thao tác:** Tạo lớp mới, chọn giáo viên, đặt lịch

**Chức năng 5: Quản lý đăng ký học**
- **Mô tả:** Duyệt/từ chối đăng ký của học viên
- **Screenshot:** Hình ảnh danh sách đăng ký
- **Thao tác:** Xem chi tiết → Duyệt/Từ chối

**Chức năng 6: Quản lý FAQ Chatbot**
- **Mô tả:** CRUD câu hỏi thường gặp cho chatbot
- **Screenshot:** Hình ảnh danh sách FAQ
- **Thao tác:** Thêm câu hỏi/trả lời, chỉnh sửa, xóa

**Chức năng 7: Báo cáo thống kê**
- **Mô tả:** Thống kê số liệu học viên, doanh thu, lớp học
- **Screenshot:** Hình ảnh trang báo cáo
- **Thông tin:** Biểu đồ, bảng số liệu

---

### ❌ 3.4 KIỂM THỬ HỆ THỐNG (CHƯA CÓ NỘI DUNG)

#### 3.4.1 Mục đích kiểm thử
**Nội dung cần viết:**
- Đảm bảo hệ thống hoạt động đúng yêu cầu
- Phát hiện và sửa lỗi trước khi triển khai
- Kiểm tra tính ổn định và bảo mật

#### 3.4.2 Các loại kiểm thử
**A. Kiểm thử chức năng (Functional Testing)**
**B. Kiểm thử giao diện (UI Testing)**
**C. Kiểm thử tích hợp (Integration Testing)**
**D. Kiểm thử bảo mật (Security Testing)**


#### 3.4.3 Kế hoạch kiểm thử
**Nội dung cần viết - Bảng Test Cases:**

| STT | Chức năng | Test Case | Đầu vào | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
|-----|-----------|-----------|---------|------------------|----------------|------------|
| 1 | Đăng nhập | Đăng nhập với tài khoản hợp lệ | Email: test@test.com, Pass: 123456 | Chuyển đến dashboard | Chuyển đến dashboard | ✅ Pass |
| 2 | Đăng nhập | Đăng nhập với email sai | Email: wrong@test.com | Hiển thị lỗi "Email không tồn tại" | Hiển thị lỗi | ✅ Pass |
| 3 | Đăng nhập | Đăng nhập với mật khẩu sai | Pass: wrongpass | Hiển thị lỗi "Sai mật khẩu" | Hiển thị lỗi | ✅ Pass |
| 4 | Đăng ký tài khoản | Đăng ký với thông tin hợp lệ | Form đầy đủ | Tạo tài khoản thành công | Tạo thành công | ✅ Pass |
| 5 | Đăng ký tài khoản | Đăng ký với email trùng | Email đã tồn tại | Hiển thị lỗi "Email đã được sử dụng" | Hiển thị lỗi | ✅ Pass |
| 6 | Đăng ký khóa học | Học viên đăng ký lớp chưa đầy | Chọn lớp có chỗ | Đăng ký thành công | Đăng ký thành công | ✅ Pass |
| 7 | Đăng ký khóa học | Học viên đăng ký lớp đã đầy | Chọn lớp đầy | Hiển thị "Lớp đã đủ số lượng" | Hiển thị thông báo | ✅ Pass |
| 8 | Chat với AI | Hỏi về học phí | "Học phí khóa Giao tiếp là bao nhiêu?" | Trả lời đúng thông tin | Trả lời 3.500.000 VNĐ | ✅ Pass |
| 9 | Chat với AI | Hỏi câu không có trong FAQ | "Thời tiết hôm nay thế nào?" | Gemini API xử lý | Trả lời từ Gemini | ✅ Pass |
| 10 | Điểm danh | Giáo viên điểm danh học viên | Chọn có mặt | Lưu trạng thái "present" | Lưu thành công | ✅ Pass |
| 11 | Chấm điểm | Giáo viên nhập điểm | Điểm: 85 | Lưu điểm vào database | Lưu thành công | ✅ Pass |
| 12 | Quản lý khóa học | Admin tạo khóa học mới | Form tạo khóa học | Khóa học mới được tạo | Tạo thành công | ✅ Pass |
| 13 | Quản lý FAQ | Admin thêm câu hỏi FAQ | Câu hỏi + Trả lời | FAQ mới được lưu | Lưu thành công | ✅ Pass |
| 14 | Bảo mật | Truy cập trang admin khi chưa đăng nhập | URL: /admin | Chuyển về trang đăng nhập | Chuyển về login | ✅ Pass |
| 15 | Bảo mật | Học viên cố truy cập trang admin | Đăng nhập student, vào /admin | Hiển thị "Không có quyền" | Hiển thị lỗi 403 | ✅ Pass |

**Ghi chú:** 
- ✅ Pass: Test thành công
- ❌ Fail: Test thất bại (cần mô tả lỗi và cách sửa)

#### 3.4.4 Kết quả kiểm thử
**Nội dung cần viết:**
- Tổng số test case: 15
- Số test case Pass: 15
- Số test case Fail: 0
- Tỷ lệ thành công: 100%
- Các lỗi phát hiện và đã sửa: (liệt kê nếu có)


---

### ❌ 3.5 ĐÁNH GIÁ KẾT QUẢ (CHƯA CÓ NỘI DUNG)

#### 3.5.1 Đánh giá chung
**Nội dung cần viết:**
- Hệ thống đã hoàn thành đầy đủ các yêu cầu đề ra
- Giao diện thân thiện, dễ sử dụng
- Tích hợp thành công AI chatbot
- Database được thiết kế tối ưu

#### 3.5.2 Ưu điểm
**Nội dung cần viết:**

**A. Về chức năng:**
- ✅ Quản lý tập trung toàn bộ nghiệp vụ của trung tâm ngoại ngữ
- ✅ Phân quyền rõ ràng cho 3 nhóm người dùng
- ✅ Tích hợp chatbot AI hỗ trợ 24/7
- ✅ Tự động hóa nhiều quy trình (đăng ký, điểm danh, chấm điểm)

**B. Về công nghệ:**
- ✅ Sử dụng Laravel Framework hiện đại
- ✅ Database MySQL ổn định, dễ mở rộng
- ✅ Tích hợp Gemini API cho AI chatbot
- ✅ Responsive design, truy cập được trên mobile

**C. Về trải nghiệm người dùng:**
- ✅ Giao diện thân thiện, trực quan
- ✅ Chatbot phản hồi nhanh, chính xác
- ✅ Dễ dàng tra cứu thông tin học tập
- ✅ Tiết kiệm thời gian cho cả admin, giáo viên và học viên

#### 3.5.3 Nhược điểm và hạn chế
**Nội dung cần viết:**

**A. Hạn chế về chức năng:**
- ⚠️ Chưa có chức năng thanh toán online tự động (VNPay, MoMo)
- ⚠️ Chưa có module học online (video bài giảng, bài tập trực tuyến)
- ⚠️ Chưa có app mobile native (iOS, Android)
- ⚠️ Chatbot chưa hỗ trợ giọng nói (voice chat)

**B. Hạn chế về kỹ thuật:**
- ⚠️ Chưa tối ưu hiệu năng cho số lượng người dùng lớn (>10,000)
- ⚠️ Chưa có cơ chế backup tự động
- ⚠️ Phụ thuộc vào Gemini API (nếu API lỗi, chatbot không hoạt động)
- ⚠️ Chưa có hệ thống thông báo real-time (push notification)

**C. Hạn chế về AI:**
- ⚠️ Chatbot đôi khi trả lời sai nếu câu hỏi quá phức tạp
- ⚠️ Chưa học được từ lịch sử chat (không có machine learning)
- ⚠️ Chi phí API khi số lượng chat tăng


---

### ❌ 3.6 HƯỚNG PHÁT TRIỂN (CHƯA CÓ NỘI DUNG)

#### 3.6.1 Nâng cấp chức năng
**Nội dung cần viết:**

**A. Tích hợp thanh toán online:**
- Kết nối với cổng thanh toán VNPay, MoMo, ZaloPay
- Tự động xác nhận thanh toán học phí
- Xuất hóa đơn điện tử

**B. Phát triển module học online:**
- Upload và quản lý video bài giảng
- Bài tập trực tuyến, tự động chấm điểm trắc nghiệm
- Thi thử online
- Diễn đàn thảo luận giữa học viên

**C. Nâng cấp chatbot AI:**
- Tích hợp xử lý giọng nói (speech-to-text, text-to-speech)
- Chatbot đa ngôn ngữ (tiếng Việt, tiếng Anh)
- Học từ lịch sử chat để cải thiện độ chính xác
- Chatbot tư vấn khóa học dựa trên trình độ học viên

**D. Thông báo real-time:**
- Push notification khi có lịch học mới
- Thông báo khi có điểm mới
- Email tự động gửi nhắc nhở học phí

#### 3.6.2 Phát triển ứng dụng mobile
**Nội dung cần viết:**
- Xây dựng app iOS (Swift) và Android (Kotlin/React Native)
- Tính năng: xem lịch học, điểm danh, xem điểm, chat với AI
- Nhận thông báo trên điện thoại
- Học offline (download bài giảng)

#### 3.6.3 Tối ưu hiệu năng
**Nội dung cần viết:**
- Sử dụng Redis cache để tăng tốc độ truy xuất
- Tối ưu database query (indexing, query optimization)
- Load balancing cho traffic lớn
- CDN cho hình ảnh, video

#### 3.6.4 Bảo mật nâng cao
**Nội dung cần viết:**
- Xác thực 2 yếu tố (2FA) cho tài khoản
- Mã hóa dữ liệu nhạy cảm
- Backup tự động hàng ngày
- Giám sát và cảnh báo bảo mật

#### 3.6.5 Phân tích dữ liệu và AI
**Nội dung cần viết:**
- Dashboard phân tích chi tiết (Business Intelligence)
- Dự đoán số lượng học viên đăng ký
- Gợi ý khóa học phù hợp cho học viên (Recommendation System)
- Phát hiện học viên có nguy cơ bỏ học (Dropout Prediction)

---

## TÓM TẮT CẦN LÀM

### CHƯƠNG 2 - CẦN BỔ SUNG:
1. ✅ 2.3.3.1 Biểu đồ tuần tự đăng nhập (có nội dung, thiếu hình)
2. ❌ 2.3.3.2 Biểu đồ tuần tự đăng ký khóa học (thiếu hoàn toàn)
3. ❌ 2.3.3.3 Biểu đồ tuần tự chat với AI (thiếu hoàn toàn)
4. ❌ **2.3.4 Thiết kế cơ sở dữ liệu (QUAN TRỌNG NHẤT - thiếu hoàn toàn)**
   - 2.3.4.1 Tổng quan
   - 2.3.4.2 Sơ đồ ERD
   - 2.3.4.3 Mô tả 17 bảng
   - 2.3.4.4 Mối quan hệ
   - 2.3.4.5 Ví dụ dữ liệu

### CHƯƠNG 3 - CẦN BỔ SUNG TOÀN BỘ:
1. ❌ 3.1 Môi trường phát triển
2. ❌ 3.2 Cài đặt hệ thống
3. ❌ 3.3 Các chức năng chính (cần screenshot)
4. ❌ 3.4 Kiểm thử hệ thống
5. ❌ 3.5 Đánh giá kết quả
6. ❌ 3.6 Hướng phát triển

---

## GỢI Ý ƯU TIÊN

**Tuần 1: Hoàn thiện Chương 2**
- Ngày 1-2: Viết mục 2.3.4 Thiết kế CSDL (dùng file GIAI_THICH_DATABASE.md)
- Ngày 3: Vẽ sơ đồ ERD
- Ngày 4: Vẽ 3 biểu đồ tuần tự

**Tuần 2: Hoàn thiện Chương 3**
- Ngày 1: Viết mục 3.1, 3.2 (môi trường, cài đặt)
- Ngày 2-3: Chụp screenshot + viết mục 3.3 (chức năng)
- Ngày 4: Viết mục 3.4, 3.5, 3.6 (kiểm thử, đánh giá, hướng phát triển)

---

**File này được tạo bởi Kiro AI**
**Ngày:** 15/06/2026
