-- =====================================================
-- INSERT FAQ VÀO BẢNG chatbot_knowledge
-- Chạy file này trong phpMyAdmin hoặc MySQL Workbench
-- =====================================================

USE language_center;

-- 1. Về ngôn ngữ dạy
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Trung tâm dạy những ngôn ngữ nào?', 'Trung tâm hiện đang dạy 6 ngôn ngữ: Tiếng Anh, Tiếng Nhật, Tiếng Hàn, Tiếng Trung, Tiếng Pháp, Tiếng Tây Ban Nha với đầy đủ các cấp độ từ cơ bản đến nâng cao.', 'Khóa học', 'ngon ngu,day gi,languages,khoa hoc,mon hoc', 10, 1, NOW(), NOW());

-- 2. Về tiếng Nhật
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Có dạy tiếng Nhật không?', 'Có, trung tâm có dạy tiếng Nhật với nhiều cấp độ:\n- N5 (Sơ cấp)\n- N4 (Trung cấp thấp)\n- N3 (Trung cấp)\n- N2-N1 (Cao cấp)\nGiáo viên là người Nhật Bản và giáo viên Việt Nam có chứng chỉ JLPT.', 'Khóa học', 'tieng nhat,japanese,nhat ban,n5,n4,n3,jlpt', 10, 1, NOW(), NOW());

-- 3. Về học online
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Có hỗ trợ học online không?', 'Có, trung tâm hỗ trợ cả 2 hình thức:\n✅ Lớp học trực tiếp (offline)\n✅ Lớp học online qua Zoom/Google Meet\n\nBạn có thể chọn hình thức phù hợp khi đăng ký. Chất lượng giảng dạy đảm bảo như nhau.', 'Hình thức học', 'online,truc tuyen,tu xa,zoom,google meet,hoc tu xa', 10, 1, NOW(), NOW());

-- 4. Về giáo viên bản xứ
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Giáo viên có phải người bản xứ không?', 'Trung tâm có cả 2 loại giáo viên:\n\n👨‍🏫 Giáo viên bản địa:\nNgười Việt có chứng chỉ quốc tế (TESOL, IELTS 8.0+, JLPT N1, HSK 6...)\n\n👩‍🏫 Giáo viên nước ngoài:\nNative speakers từ Anh, Mỹ, Úc, Nhật, Hàn, Trung...\n\nTùy theo khóa học và lịch học, bạn sẽ được học với giáo viên phù hợp.', 'Giáo viên', 'giao vien,ban xu,native,nguoi nuoc ngoai,foreign teacher,teacher', 10, 1, NOW(), NOW());

-- 5. Về thời lượng khóa học
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Khóa học kéo dài bao lâu?', 'Thời lượng khóa học phụ thuộc vào cấp độ:\n\n📚 Cơ bản (Beginner): 3-4 tháng\n📚 Trung cấp (Intermediate): 4-6 tháng\n📚 Nâng cao (Advanced): 6-8 tháng\n\nMỗi khóa có 2-3 buổi/tuần, mỗi buổi 90-120 phút.', 'Khóa học', 'thoi luong,keo dai,bao lau,duration,thoi gian hoc,mat bao lau', 10, 1, NOW(), NOW());

-- 6. Về giờ làm việc
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Giờ làm việc của trung tâm?', '⏰ Giờ làm việc:\n\n📅 Thứ 2 - Thứ 6: 8:00 - 21:00\n📅 Thứ 7: 8:00 - 18:00\n📅 Chủ nhật: 9:00 - 17:00\n\n🏫 Lịch học:\n- Sáng: 8:00 - 11:30\n- Chiều: 14:00 - 17:00\n- Tối: 18:00 - 21:00', 'Thông tin chung', 'gio lam viec,gio mo cua,working hours,office hours,gio hoc,mo cua', 10, 1, NOW(), NOW());

-- 7. Về địa chỉ và liên hệ
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Địa chỉ và liên hệ trung tâm?', '📞 Thông tin liên hệ:\n\n📍 Địa chỉ: 123 Nguyễn Huệ, Quận 1, TP.HCM\n📞 Hotline: 0123-456-789\n📧 Email: contact@languagecenter.edu.vn\n🌐 Website: www.languagecenter.edu.vn\n💬 Facebook: fb.com/languagecenter\n\nHoặc bạn có thể inbox fanpage Facebook để được tư vấn nhanh nhất!', 'Thông tin chung', 'dia chi,lien he,contact,address,phone,email,hotline,facebook,website', 10, 1, NOW(), NOW());

-- 8. Về cách đăng ký
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Làm thế nào để đăng ký khóa học?', '📝 Có 3 cách đăng ký:\n\n1️⃣ Online (Website):\n   - Đăng nhập tài khoản\n   - Chọn khóa học\n   - Điền form đăng ký\n   - Thanh toán online\n\n2️⃣ Trực tiếp:\n   - Đến trung tâm\n   - Tư vấn với nhân viên\n   - Điền form và thanh toán\n\n3️⃣ Hotline:\n   - Gọi 0123-456-789\n   - Tư vấn viên hỗ trợ đăng ký', 'Đăng ký', 'dang ky,register,enroll,cach dang ky,lam sao de dang ky,dang ky khoa hoc', 10, 1, NOW(), NOW());

-- 9. Về chứng chỉ
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Học xong có chứng chỉ không?', '🎓 Có! Sau khi hoàn thành khóa học, bạn sẽ nhận được:\n\n✅ Chứng chỉ hoàn thành khóa học (của trung tâm)\n\n📋 Điều kiện nhận chứng chỉ:\n   - Tham gia đầy đủ ≥ 80% buổi học\n   - Điểm trung bình ≥ 6.0/10\n   - Vượt qua bài kiểm tra cuối khóa\n\n🌍 Nếu muốn lấy chứng chỉ quốc tế (IELTS, TOEIC, JLPT, HSK...), trung tâm hỗ trợ đăng ký thi và ôn luyện.', 'Chứng chỉ', 'chung chi,certificate,hoan thanh,tot nghiep,graduation', 10, 1, NOW(), NOW());

-- 10. Về học phí và thanh toán
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Học phí và hình thức thanh toán?', '💰 Học phí:\n- Khóa cơ bản: 3.000.000 - 4.000.000 VNĐ\n- Khóa trung cấp: 4.000.000 - 5.500.000 VNĐ\n- Khóa nâng cao: 5.500.000 - 7.000.000 VNĐ\n\n💳 Hình thức thanh toán:\n✅ Tiền mặt tại trung tâm\n✅ Chuyển khoản ngân hàng\n✅ Thanh toán online (VNPAY, Momo)\n✅ Trả góp 0% (qua thẻ tín dụng)\n\n🎁 Ưu đãi: Giảm 10% khi đăng ký 2 khóa trở lên!', 'Học phí', 'hoc phi,gia,fee,thanh toan,payment,tra gop,phi,tien', 10, 1, NOW(), NOW());

-- 11. Về tiếng Anh
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Có dạy tiếng Anh không?', 'Có, trung tâm có nhiều khóa tiếng Anh:\n\n📚 Tiếng Anh giao tiếp (Communication)\n📚 Tiếng Anh IELTS (4.0 - 8.0+)\n📚 Tiếng Anh TOEIC (450 - 990)\n📚 Tiếng Anh thiếu nhi (Kids)\n📚 Tiếng Anh doanh nghiệp (Business)\n\nGiáo viên có chứng chỉ TESOL/CELTA và native speakers.', 'Khóa học', 'tieng anh,english,ielts,toeic', 10, 1, NOW(), NOW());

-- 12. Về tiếng Hàn
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Có dạy tiếng Hàn không?', 'Có, trung tâm có dạy tiếng Hàn với các cấp độ:\n\n🇰🇷 Topik 1 (Sơ cấp)\n🇰🇷 Topik 2 (Trung cấp)\n🇰🇷 Topik 3-4 (Nâng cao)\n\nGiáo viên là người Hàn Quốc và giáo viên Việt Nam có chứng chỉ Topik.', 'Khóa học', 'tieng han,korean,han quoc,topik', 10, 1, NOW(), NOW());

-- 13. Về tiếng Trung
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Có dạy tiếng Trung không?', 'Có, trung tâm có dạy tiếng Trung với các cấp độ:\n\n🇨🇳 HSK 1-2 (Sơ cấp)\n🇨🇳 HSK 3-4 (Trung cấp)\n🇨🇳 HSK 5-6 (Nâng cao)\n\nGiáo viên là người Trung Quốc và giáo viên Việt Nam có chứng chỉ HSK.', 'Khóa học', 'tieng trung,chinese,trung quoc,hsk', 10, 1, NOW(), NOW());

-- 14. Về lịch học linh hoạt
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Lịch học có linh hoạt không?', 'Có! Trung tâm có nhiều khung giờ linh hoạt:\n\n⏰ Sáng: 8:00 - 11:30 (Thứ 2, 4, 6)\n⏰ Chiều: 14:00 - 17:00 (Thứ 3, 5, 7)\n⏰ Tối: 18:00 - 21:00 (Thứ 2-7)\n⏰ Cuối tuần: Thứ 7 - CN\n\nBạn có thể chọn lịch phù hợp với thời gian của mình khi đăng ký.', 'Lịch học', 'lich hoc,linh hoat,flexible,schedule,khung gio', 10, 1, NOW(), NOW());

-- 15. Về lớp học nhỏ
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
('Lớp học có bao nhiêu người?', 'Trung tâm áp dụng lớp học nhỏ để đảm bảo chất lượng:\n\n👥 Lớp nhóm: 8-15 học viên\n👤 Lớp VIP: 3-5 học viên\n🎯 Lớp 1-1: 1 học viên (kèm riêng)\n\nGiáo viên sẽ có thời gian tương tác nhiều hơn với từng học viên.', 'Thông tin chung', 'si so,bao nhieu nguoi,lop hoc,class size', 10, 1, NOW(), NOW());

SELECT '✅ Đã thêm 15 FAQ vào bảng chatbot_knowledge!' AS status;
