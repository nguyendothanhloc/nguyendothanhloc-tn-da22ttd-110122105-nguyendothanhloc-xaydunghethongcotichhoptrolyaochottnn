-- =====================================================
-- FIX FAQ KEYWORDS - Bổ sung keywords để chatbot tìm được
-- =====================================================

USE language_center;

-- Xóa FAQ cũ (nếu có) và thêm FAQ mới với keywords đầy đủ
DELETE FROM chatbot_knowledge WHERE question LIKE '%tiếng Nhật%' OR question LIKE '%tiếng nhật%';

-- Thêm FAQ MỚI với KEYWORDS ĐẦY ĐỦ
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
(
    'Có dạy tiếng Nhật không?',
    'Có! Trung tâm có dạy tiếng Nhật với nhiều cấp độ:\n\n📚 N5 (Sơ cấp)\n📚 N4 (Trung cấp thấp)\n📚 N3 (Trung cấp)\n📚 N2-N1 (Cao cấp)\n\nGiáo viên là người Nhật Bản và giáo viên Việt Nam có chứng chỉ JLPT. Bạn có thể đăng ký học online hoặc offline.',
    'Khóa học',
    'co day,day,hoc,tieng nhat,nhat,japanese,nhat ban,n5,n4,n3,n2,n1,jlpt,khoa hoc nhat,giao vien nhat',
    10,
    1,
    NOW(),
    NOW()
);

-- Thêm FAQ về các ngôn ngữ khác với keywords đầy đủ
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
(
    'Có dạy tiếng Anh không?',
    'Có! Trung tâm có nhiều khóa tiếng Anh:\n\n📚 Tiếng Anh giao tiếp (Communication)\n📚 Tiếng Anh IELTS (4.0 - 8.0+)\n📚 Tiếng Anh TOEIC (450 - 990)\n📚 Tiếng Anh thiếu nhi (Kids)\n📚 Tiếng Anh doanh nghiệp (Business)\n\nGiáo viên có chứng chỉ TESOL/CELTA và native speakers.',
    'Khóa học',
    'co day,day,hoc,tieng anh,anh,english,ielts,toeic,khoa hoc anh,giao vien anh',
    10,
    1,
    NOW(),
    NOW()
),
(
    'Có dạy tiếng Hàn không?',
    'Có! Trung tâm có dạy tiếng Hàn với các cấp độ:\n\n🇰🇷 Topik 1 (Sơ cấp)\n🇰🇷 Topik 2 (Trung cấp)\n🇰🇷 Topik 3-4 (Nâng cao)\n\nGiáo viên là người Hàn Quốc và giáo viên Việt Nam có chứng chỉ Topik. Bạn có thể đăng ký học online hoặc offline.',
    'Khóa học',
    'co day,day,hoc,tieng han,han,korean,han quoc,topik,khoa hoc han,giao vien han',
    10,
    1,
    NOW(),
    NOW()
),
(
    'Có dạy tiếng Trung không?',
    'Có! Trung tâm có dạy tiếng Trung với các cấp độ:\n\n🇨🇳 HSK 1-2 (Sơ cấp)\n🇨🇳 HSK 3-4 (Trung cấp)\n🇨🇳 HSK 5-6 (Nâng cao)\n\nGiáo viên là người Trung Quốc và giáo viên Việt Nam có chứng chỉ HSK. Bạn có thể đăng ký học online hoặc offline.',
    'Khóa học',
    'co day,day,hoc,tieng trung,trung,chinese,trung quoc,hsk,khoa hoc trung,giao vien trung',
    10,
    1,
    NOW(),
    NOW()
);

-- Thêm FAQ về học online
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
(
    'Có hỗ trợ học online không?',
    'Có! Trung tâm hỗ trợ cả 2 hình thức:\n\n✅ Lớp học trực tiếp (offline)\n✅ Lớp học online qua Zoom/Google Meet\n\nBạn có thể chọn hình thức phù hợp khi đăng ký. Chất lượng giảng dạy đảm bảo như nhau.',
    'Hình thức học',
    'co,ho tro,hoc online,online,truc tuyen,tu xa,zoom,google meet,lop online,khoa online',
    10,
    1,
    NOW(),
    NOW()
);

-- Thêm FAQ về giáo viên bản xứ
INSERT INTO chatbot_knowledge (question, answer, category, keywords, priority, is_active, created_at, updated_at) VALUES
(
    'Giáo viên có phải người bản xứ không?',
    'Trung tâm có cả 2 loại giáo viên:\n\n👨‍🏫 Giáo viên bản địa:\nNgười Việt có chứng chỉ quốc tế (TESOL, IELTS 8.0+, JLPT N1, HSK 6...)\n\n👩‍🏫 Giáo viên nước ngoài:\nNative speakers từ Anh, Mỹ, Úc, Nhật, Hàn, Trung...\n\nTùy theo khóa học và lịch học, bạn sẽ được học với giáo viên phù hợp.',
    'Giáo viên',
    'giao vien,co phai,ban xu,native,nguoi nuoc ngoai,foreign teacher,teacher,nguoi ban xu',
    10,
    1,
    NOW(),
    NOW()
);

SELECT '✅ Đã fix FAQ keywords! Test lại chatbot với câu "Có dạy tiếng Nhật không?"' AS status;
