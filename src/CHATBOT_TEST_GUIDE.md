# Hướng Dẫn Test Chatbot

## ✅ CÂU HỎI VỀ DỮ LIỆU CÁ NHÂN (Rule-Based - Lấy từ Database)

### 📌 Điều kiện: Học viên PHẢI có dữ liệu trong database

### Các câu hỏi đã được implement:

#### 1. GIÁO VIÊN
- "Giáo viên của tôi là ai?"
- "Giáo viên dạy tôi?"
- "Ai là giáo viên?"

→ **Trả lời:** Tên giáo viên + tên lớp (lấy từ enrollments)

---

#### 2. ĐIỂM SỐ
- "Điểm của tôi thế nào?"
- "Điểm số của tôi?"
- "Grade của tôi?"
- "Kết quả học tập?"

→ **Trả lời:** Danh sách điểm từ bảng `assessment_scores`

---

#### 3. HỌC PHÍ
- "Học phí bao nhiêu?"
- "Học phí của tôi?"
- "Phí khóa học?"

→ **Trả lời:** Thông tin học phí từ bảng `payments`

---

#### 4. LỊCH HỌC
- "Lịch học hôm nay"
- "Tôi có học hôm nay không?"
- "Schedule hôm nay?"

→ **Trả lời:** Lịch học hôm nay từ bảng `schedules`

---

#### 5. LỊCH HỌC TUẦN
- "Lịch học tuần này"
- "Lịch tuần"

→ **Trả lời:** Lịch học 7 ngày tới

---

#### 6. ĐIỂM DANH
- "Điểm danh của tôi?"
- "Tôi vắng bao nhiêu buổi?"
- "Attendance?"

→ **Trả lời:** Tỷ lệ điểm danh từ bảng `attendances`

---

#### 7. THÔNG TIN LỚP HỌC
- "Lớp học của tôi có bao nhiêu người?"
- "Sĩ số lớp?"

→ **Trả lời:** Sĩ số lớp học

---

## ✅ CÂU HỎI CHUNG VỀ TRUNG TÂM (FAQ Database)

### Cần thêm vào bảng `chatbot_knowledge`:

```sql
-- 1. Về ngôn ngữ dạy
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active) VALUES
('Trung tâm dạy những ngôn ngữ nào?', 'Trung tâm hiện đang dạy 6 ngôn ngữ: Tiếng Anh, Tiếng Nhật, Tiếng Hàn, Tiếng Trung, Tiếng Pháp, Tiếng Tây Ban Nha với đầy đủ các cấp độ từ cơ bản đến nâng cao.', 'Khóa học', 'ngon ngu,day gi,languages,khoa hoc', 10, 1);

-- 2. Về tiếng Nhật
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active) VALUES
('Có dạy tiếng Nhật không?', 'Có, trung tâm có dạy tiếng Nhật với nhiều cấp độ:\n- N5 (Sơ cấp)\n- N4 (Trung cấp thấp)\n- N3 (Trung cấp)\n- N2-N1 (Cao cấp)\nGiáo viên là người Nhật Bản và giáo viên Việt Nam có chứng chỉ JLPT.', 'Khóa học', 'tieng nhat,japanese,nhat ban,n5,n4', 10, 1);

-- 3. Về học online
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active) VALUES
('Có hỗ trợ học online không?', 'Có, trung tâm hỗ trợ cả 2 hình thức:\n- Lớp học trực tiếp (offline)\n- Lớp học online qua Zoom/Google Meet\nBạn có thể chọn hình thức phù hợp khi đăng ký. Chất lượng giảng dạy đảm bảo như nhau.', 'Hình thức học', 'online,truc tuyen,tu xa,zoom,google meet', 10, 1);

-- 4. Về giáo viên bản xứ
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active) VALUES
('Giáo viên có phải người bản xứ không?', 'Trung tâm có cả 2 loại giáo viên:\n- Giáo viên bản địa: Người Việt có chứng chỉ quốc tế (TESOL, IELTS 8.0+, JLPT N1, HSK 6...)\n- Giáo viên nước ngoài: Native speakers từ Anh, Mỹ, Nhật, Hàn...\nTùy theo khóa học và lịch học, bạn sẽ được học với giáo viên phù hợp.', 'Giáo viên', 'giao vien,ban xu,native,nguoi nuoc ngoai,foreign teacher', 10, 1);

-- 5. Về thời lượng khóa học
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active) VALUES
('Khóa học kéo dài bao lâu?', 'Thời lượng khóa học phụ thuộc vào cấp độ:\n- Cơ bản (Beginner): 3-4 tháng\n- Trung cấp (Intermediate): 4-6 tháng\n- Nâng cao (Advanced): 6-8 tháng\nMỗi khóa có 2-3 buổi/tuần, mỗi buổi 90-120 phút.', 'Khóa học', 'thoi luong,keo dai,bao lau,duration,thoi gian hoc', 10, 1);

-- 6. Về giờ làm việc
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active) VALUES
('Giờ làm việc của trung tâm?', 'Giờ làm việc:\n- Thứ 2 - Thứ 6: 8:00 - 21:00\n- Thứ 7: 8:00 - 18:00\n- Chủ nhật: 9:00 - 17:00\n\nLớp học:\n- Sáng: 8:00 - 11:30\n- Chiều: 14:00 - 17:00\n- Tối: 18:00 - 21:00', 'Thông tin chung', 'gio lam viec,gio mo cua,working hours,office hours,gio hoc', 10, 1);

-- 7. Về địa chỉ và liên hệ
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active) VALUES
('Địa chỉ và liên hệ trung tâm?', 'Thông tin liên hệ:\n📍 Địa chỉ: 123 Nguyễn Huệ, Quận 1, TP.HCM\n📞 Hotline: 0123-456-789\n📧 Email: contact@languagecenter.edu.vn\n🌐 Website: www.languagecenter.edu.vn\n\nHoặc bạn có thể inbox fanpage Facebook để được tư vấn nhanh nhất.', 'Thông tin chung', 'dia chi,lien he,contact,address,phone,email,hotline', 10, 1);

-- 8. Về cách đăng ký
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active) VALUES
('Làm thế nào để đăng ký khóa học?', 'Có 3 cách đăng ký:\n\n1. Online (Website):\n   - Đăng nhập tài khoản\n   - Chọn khóa học\n   - Điền form đăng ký\n   - Thanh toán online\n\n2. Trực tiếp:\n   - Đến trung tâm\n   - Tư vấn với nhân viên\n   - Điền form và thanh toán\n\n3. Hotline:\n   - Gọi 0123-456-789\n   - Tư vấn viên hỗ trợ đăng ký', 'Đăng ký', 'dang ky,register,enroll,cach dang ky,lam sao de dang ky', 10, 1);

-- 9. Về chứng chỉ
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active) VALUES
('Học xong có chứng chỉ không?', 'Có. Sau khi hoàn thành khóa học, bạn sẽ nhận được:\n\n✅ Chứng chỉ hoàn thành khóa học (của trung tâm)\n✅ Điều kiện nhận chứng chỉ:\n   - Tham gia đầy đủ ≥ 80% buổi học\n   - Điểm trung bình ≥ 6.0/10\n   - Vượt qua bài kiểm tra cuối khóa\n\nNếu muốn lấy chứng chỉ quốc tế (IELTS, TOEIC, JLPT...), trung tâm hỗ trợ đăng ký thi và ôn luyện.', 'Chứng chỉ', 'chung chi,certificate,hoan thanh,tot nghiep', 10, 1);

-- 10. Về học phí và thanh toán
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active) VALUES
('Học phí và hình thức thanh toán?', 'Học phí:\n- Khóa cơ bản: 3.000.000 - 4.000.000 VNĐ\n- Khóa trung cấp: 4.000.000 - 5.500.000 VNĐ\n- Khóa nâng cao: 5.500.000 - 7.000.000 VNĐ\n\nHình thức thanh toán:\n✅ Tiền mặt tại trung tâm\n✅ Chuyển khoản ngân hàng\n✅ Thanh toán online (VNPAY, Momo)\n✅ Trả góp 0% (qua thẻ tín dụng)\n\n🎁 Ưu đãi: Giảm 10% khi đăng ký 2 khóa trở lên', 'Học phí', 'hoc phi,gia,fee,thanh toan,payment,tra gop', 10, 1);
```

---

## ❌ CÂU HỎI PHỨC TẠP (Gemini AI - Hiện tại yếu)

Các câu hỏi này cần AI mạnh hơn:
- "So sánh khóa tiếng Anh và Nhật cho tôi"
- "Tư vấn khóa học phù hợp với tôi"
- "Giải thích khác biệt giữa IELTS và TOEIC"

---

## 📋 CÁCH TEST

### Bước 1: Thêm FAQ vào database
```bash
# Chạy lệnh SQL ở trên trong phpMyAdmin hoặc:
php artisan tinker
```

Rồi paste từng câu INSERT vào.

### Bước 2: Đăng nhập với tài khoản học viên CÓ DỮ LIỆU

Ví dụ: `student1@example.com` (nếu có trong seeder)

### Bước 3: Test các câu hỏi

**Câu hỏi cá nhân** (Rule-Based):
1. "Giáo viên của tôi là ai?" → Phải trả lời tên giáo viên
2. "Điểm của tôi?" → Phải trả lời điểm số
3. "Lịch học hôm nay?" → Phải trả lời lịch học

**Câu hỏi chung** (FAQ):
1. "Có dạy tiếng Nhật không?" → "Có, trung tâm có dạy tiếng Nhật..."
2. "Có học online không?" → "Có, trung tâm hỗ trợ..."
3. "Giáo viên có phải người bản xứ không?" → "Trung tâm có cả 2 loại..."

---

## ✅ KẾT LUẬN

Chatbot của bạn **ĐÃ HOẠT ĐỘNG TỐT** cho:
- ✅ Câu hỏi về dữ liệu cá nhân (Rule-Based)
- ✅ Câu hỏi chung (FAQ - sau khi thêm dữ liệu)

Chỉ yếu ở:
- ❌ Câu hỏi phức tạp cần suy luận (Gemini Flash model yếu)

**Giải pháp:** Thêm FAQ cho các câu hỏi chung → Giảm tải cho AI
