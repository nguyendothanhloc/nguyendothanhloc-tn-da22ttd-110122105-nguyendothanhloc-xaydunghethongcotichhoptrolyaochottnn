# 📚 HƯỚNG DẪN TEST CHỨC NĂNG FAQ MANAGEMENT

## 🎯 Tổng quan

Hệ thống FAQ Management (Chatbot Knowledge Base) cho phép Admin quản lý các câu hỏi thường gặp của chatbot với đầy đủ chức năng CRUD và Toggle Status.

---

## 📁 Files đã tạo

### 1. **test_faq_crud_full.php** - Test tự động đầy đủ
- ✅ Test CREATE (tạo FAQ)
- ✅ Test READ (đọc danh sách)
- ✅ Test UPDATE (cập nhật)
- ✅ Test DELETE (xóa)
- ✅ Test TOGGLE STATUS (bật/tắt)
- ✅ Test FILTER (lọc theo category & status)
- ⏱️ Thời gian chạy: ~5 giây

### 2. **test_faq_toggle_detail.php** - Test chi tiết Toggle Status
- ✅ Test toggle với FAQ "Giáo viên của tôi"
- ✅ Test toggle với FAQ mới tạo
- ✅ Verify chatbot response khi FAQ bật/tắt
- ⏱️ Thời gian chạy: ~3 giây

### 3. **demo_faq_management.php** - Demo tương tác (RECOMMENDED)
- 🎬 Demo đầy đủ với UI đẹp
- 🖱️ Tương tác từng bước (nhấn ENTER để tiếp tục)
- 📊 Hiển thị thống kê real-time
- ⏱️ Thời gian: ~2-3 phút (tùy tốc độ đọc)

### 4. **add_teacher_faq.php** - Tạo FAQ "Giáo viên của tôi"
- Script nhanh để tạo FAQ cụ thể
- ⏱️ Thời gian: <1 giây

### 5. **FAQ_CRUD_TEST_SUMMARY.md** - Báo cáo kết quả
- 📄 Tài liệu chi tiết kết quả test
- 📊 Screenshots và ví dụ
- 📝 Giải thích cách hoạt động

---

## 🚀 Cách chạy

### Option 1: Chạy test tự động (NHANH)
```bash
cd d:\xamp\htdocs\khoaluan
php test_faq_crud_full.php
```

**Output mẫu:**
```
=================================================================
           TEST CHỨC NĂNG FAQ CRUD & TOGGLE STATUS              
=================================================================

📝 TEST 1: TẠO FAQ MỚI
-----------------------------------
✅ FAQ mới được tạo:
   ID: 30
   Câu hỏi: Làm thế nào để đăng ký khóa học?
   ...
```

### Option 2: Chạy demo tương tác (RECOMMENDED - ĐẸP NHẤT!)
```bash
cd d:\xamp\htdocs\khoaluan
php demo_faq_management.php
```

**Tính năng:**
- ✅ UI đẹp với box và emoji
- ✅ Tương tác từng bước
- ✅ Test real-time với chatbot
- ✅ Thống kê trực quan

### Option 3: Chạy test chi tiết toggle
```bash
cd d:\xamp\htdocs\khoaluan
php test_faq_toggle_detail.php
```

---

## 📋 Checklist kiểm tra

Khi chạy tests, bạn cần kiểm tra:

### ✅ CREATE (Tạo mới)
- [ ] FAQ mới được tạo thành công
- [ ] ID được generate tự động
- [ ] Các trường bắt buộc đều có giá trị
- [ ] Trạng thái mặc định là Active

### ✅ READ (Đọc)
- [ ] Hiển thị đúng tổng số FAQ
- [ ] Sắp xếp theo priority (cao → thấp)
- [ ] Hiển thị đúng trạng thái (🟢/🔴)
- [ ] Filter theo category hoạt động
- [ ] Filter theo status hoạt động

### ✅ UPDATE (Cập nhật)
- [ ] Cập nhật question thành công
- [ ] Cập nhật answer thành công
- [ ] Cập nhật priority thành công
- [ ] Cập nhật keywords thành công
- [ ] Thay đổi category thành công

### ✅ DELETE (Xóa)
- [ ] FAQ bị xóa khỏi database
- [ ] Không thể tìm thấy FAQ sau khi xóa
- [ ] Chatbot không trả lời FAQ đã xóa

### ✅ TOGGLE STATUS (Bật/Tắt)
- [ ] Tắt FAQ: is_active = false
- [ ] Chatbot KHÔNG trả lời khi FAQ tắt
- [ ] Mở FAQ: is_active = true
- [ ] Chatbot TRẢ LỜI khi FAQ mở
- [ ] Toggle hoạt động real-time

---

## 🔍 Test Cases chi tiết

### Test Case 1: Toggle FAQ "Giáo viên của tôi"

**Input:**
- FAQ ID: 29
- Question: "Giáo viên của tôi là ai?"
- Keywords: "giao vien cua toi, thay cua toi, co cua toi"

**Steps:**
1. FAQ is_active = true → Test chatbot
2. FAQ is_active = false → Test chatbot
3. FAQ is_active = true → Test chatbot

**Expected Results:**
- Step 1: Response type = "knowledge_base" ✅
- Step 2: Response type = "ai_powered" (không phải "knowledge_base") ✅
- Step 3: Response type = "knowledge_base" ✅

---

### Test Case 2: Create → Toggle → Delete

**Steps:**
1. CREATE: Tạo FAQ mới
2. TEST: Chatbot trả lời → PASS ✅
3. TOGGLE OFF: Tắt FAQ
4. TEST: Chatbot không trả lời → PASS ✅
5. TOGGLE ON: Mở FAQ
6. TEST: Chatbot trả lời → PASS ✅
7. DELETE: Xóa FAQ
8. VERIFY: FAQ không tồn tại → PASS ✅

---

## 🎯 Kết quả mong đợi

Khi chạy `test_faq_crud_full.php`, bạn phải thấy:

```
=================================================================
                       KẾT QUẢ TỔNG HỢP                         
=================================================================
✅ CREATE (Tạo mới): PASSED
✅ READ (Đọc danh sách): PASSED
✅ UPDATE (Cập nhật): PASSED
✅ DELETE (Xóa): PASSED
✅ TOGGLE OFF (Tắt trạng thái): PASSED
✅ TOGGLE ON (Mở trạng thái): PASSED
✅ FILTER BY CATEGORY (Lọc danh mục): PASSED
✅ FILTER BY STATUS (Lọc trạng thái): PASSED
=================================================================

🎉 TẤT CẢ TESTS ĐÃ HOÀN THÀNH!
```

---

## 🐛 Troubleshooting

### Issue 1: FAQ vẫn trả lời dù đã tắt

**Nguyên nhân:**
- Có FAQ khác cũng match với câu hỏi
- FAQ khác có priority cao hơn

**Giải pháp:**
- Kiểm tra `searchKnowledgeBase()` log
- Xem FAQ nào đang match
- Tắt các FAQ không mong muốn

### Issue 2: Chatbot không trả lời FAQ đã mở

**Nguyên nhân:**
- Keywords không match
- Normalized text không đúng

**Giải pháp:**
- Check keywords có chứa từ khóa cần match
- Test với `removeVietnameseAccents()`
- Thêm nhiều keywords hơn

### Issue 3: Database connection error

**Giải pháp:**
```bash
# Check MySQL running
php artisan db:show

# Clear cache
php artisan config:clear
php artisan cache:clear
```

---

## 📞 Support

Nếu gặp vấn đề:

1. **Check Laravel log:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check database:**
   ```sql
   SELECT * FROM chatbot_knowledge WHERE id = 29;
   ```

3. **Debug chatbot response:**
   ```bash
   php artisan tinker
   >>> $service = new \App\Services\RuleBasedChatbotService();
   >>> $response = $service->processMessage('test');
   >>> print_r($response);
   ```

---

## 🎓 Học thêm

### Tài liệu liên quan:
- `app/Models/ChatbotKnowledge.php` - Model FAQ
- `app/Services/RuleBasedChatbotService.php` - Logic chatbot
- `app/Http/Controllers/ChatbotKnowledgeController.php` - CRUD controller

### Database schema:
```sql
CREATE TABLE chatbot_knowledge (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    keywords TEXT NULL,
    priority INT DEFAULT 50,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

**📅 Last Updated**: 2026-06-25  
**✍️ Author**: Kiro AI  
**📧 Contact**: support@kiro.ai  
