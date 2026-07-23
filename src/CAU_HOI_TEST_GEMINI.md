# 🧪 CÂU HỎI TEST GEMINI AI CHATBOT

## 🎯 HƯỚNG DẪN

1. **Đăng nhập:** http://127.0.0.1:8000/login
   - Email: `hocvien1@gmail.com`
   - Password: `password`

2. **Mở chatbot:** Click vào icon 💬 góc dưới bên phải

3. **Test các câu hỏi bên dưới**

---

## 📊 PHÂN LOẠI CÂU HỎI

### ✅ CÂU HỎI LAYER 1 (Rule-Based - Nhanh ~10ms)

Những câu này sẽ được trả lời bởi **Rule-Based patterns** (KHÔNG dùng Gemini):

1. **"Xin chào"**
   - Mong đợi: Lời chào + menu hỗ trợ
   - Response time: < 50ms

2. **"Thông tin của tôi"**
   - Mong đợi: Tên, email, phone, level, sở thích
   - Response time: < 50ms

3. **"Điểm danh của tôi"**
   - Mong đợi: Thống kê điểm danh (có mặt, vắng, tỷ lệ)
   - Response time: < 100ms

4. **"Lớp học của tôi"**
   - Mong đợi: Danh sách lớp đang tham gia
   - Response time: < 100ms

---

### ✅ CÂU HỎI LAYER 2 (FAQ Database - Trung bình ~100-200ms)

Những câu này sẽ được trả lời từ **Cơ sở dữ liệu FAQ**:

5. **"Học phí là bao nhiêu?"**
   - Mong đợi: Thông tin học phí từ FAQ
   - Response time: 100-300ms

6. **"Làm thế nào để đăng ký khóa học?"**
   - Mong đợi: Hướng dẫn đăng ký từ FAQ
   - Response time: 100-300ms

7. **"Trung tâm ở đâu?"**
   - Mong đợi: Địa chỉ trung tâm từ FAQ
   - Response time: 100-300ms

8. **"Có học tiếng Nhật không?"**
   - Mong đợi: Thông tin về các ngôn ngữ từ FAQ
   - Response time: 100-300ms

---

### 🤖 CÂU HỎI LAYER 3 (Gemini AI - Chậm ~3-8 giây)

Những câu này sẽ được xử lý bởi **Gemini AI** (Test API key mới):

---

#### **NHÓM A: Câu hỏi NGOÀI PHẠM VI (AI sẽ từ chối lịch sự)**

9. **"Viết cho tôi một bài thơ về mùa xuân"** ⭐ KHUYẾN NGHỊ
   - **Mong đợi:** AI từ chối + giải thích chuyên môn về học tập
   - **Response time:** 3-5 giây
   - **Format:** Có emoji, friendly, Vietnamese
   - **Nội dung:** "Câu hỏi này nằm ngoài chuyên môn của mình..."

10. **"Tokyo là thủ đô của nước nào?"** ⭐ KHUYẾN NGHỊ
    - **Mong đợi:** AI từ chối + hướng dẫn liên hệ
    - **Response time:** 3-5 giây
    - **Nội dung:** "Câu hỏi về địa lý nằm ngoài phạm vi..."

11. **"Làm thế nào để nấu phở?"**
    - **Mong đợi:** AI từ chối + hỏi về nhu cầu học tập
    - **Response time:** 3-5 giây
    - **Nội dung:** "Câu hỏi về nấu ăn nằm ngoài chuyên môn..."

12. **"Giải thích định luật Newton thứ hai"**
    - **Mong đợi:** AI từ chối hoặc giải thích ngắn + chuyển về học tập
    - **Response time:** 3-5 giây

---

#### **NHÓM B: Câu hỏi PHỨC TẠP (AI sẽ phân tích và trả lời)**

13. **"Tôi muốn học tiếng Anh nhưng chưa biết bắt đầu từ đâu, bạn tư vấn cho tôi"** ⭐⭐ KHUYẾN NGHỊ
    - **Mong đợi:** AI phân tích + đưa ra lộ trình học
    - **Response time:** 5-8 giây
    - **Nội dung:** Tư vấn level, khóa học phù hợp, timeline
    - **Format:** Có bullet points, emoji, kết thúc bằng call-to-action

14. **"So sánh khóa học tiếng Anh Beginner và Intermediate"** ⭐⭐ KHUYẾN NGHỊ
    - **Mong đợi:** AI so sánh chi tiết 2 khóa
    - **Response time:** 5-8 giây
    - **Nội dung:** Đối tượng, nội dung, thời gian, giá cả
    - **Format:** Table hoặc bullet points

15. **"Tôi học lớp A1 nhưng cảm thấy khó, có nên chuyển xuống Beginner không?"**
    - **Mong đợi:** AI phân tích tình huống + tư vấn
    - **Response time:** 5-8 giây
    - **Nội dung:** Khuyên nên làm gì, liên hệ ai

16. **"Nếu tôi nghỉ học 2 tuần thì có bị ảnh hưởng gì không?"**
    - **Mong đợi:** AI phân tích policy + tư vấn
    - **Response time:** 4-7 giây
    - **Nội dung:** Về điểm danh, học bù, chính sách

---

#### **NHÓM C: Câu hỏi SÁNG TẠO (Test khả năng AI)**

17. **"Kể cho tôi một câu chuyện ngắn về việc học ngoại ngữ"**
    - **Mong đợi:** AI tạo story ngắn về học tập
    - **Response time:** 6-10 giây
    - **Nội dung:** Motivational story

18. **"Đưa ra 5 tips để học tiếng Anh hiệu quả"**
    - **Mong đợi:** AI liệt kê tips
    - **Response time:** 5-8 giây
    - **Format:** Numbered list + giải thích

19. **"Tôi đang stress vì học không hiểu, bạn có thể động viên tôi không?"**
    - **Mong đợi:** AI đồng cảm + động viên + tư vấn
    - **Response time:** 4-7 giây
    - **Tone:** Warm, supportive, encouraging

20. **"Giải thích sự khác biệt giữa học offline và online"**
    - **Mong đợi:** AI so sánh 2 hình thức
    - **Response time:** 5-8 giây
    - **Format:** Pros & cons

---

## 🎯 CÂU HỎI ĐỂ TEST GEMINI API MỚI (TOP PICKS)

### ⭐⭐⭐ KHUYẾN NGHỊ NHẤT (Test API key format mới):

**Câu 1: "Viết cho tôi một bài thơ về mùa xuân"**
- ✅ Chắc chắn trigger Gemini AI
- ✅ Dễ verify response (AI sẽ từ chối)
- ✅ Response time rõ ràng (3-5 giây)

**Câu 2: "Tôi muốn học tiếng Anh nhưng chưa biết bắt đầu từ đâu, bạn tư vấn cho tôi"**
- ✅ Test AI reasoning
- ✅ Test response quality
- ✅ Test Vietnamese language

**Câu 3: "So sánh khóa học tiếng Anh Beginner và Intermediate"**
- ✅ Test AI analysis
- ✅ Test structured response
- ✅ Test domain knowledge

---

## 📝 CHECKLIST KHI TEST

### ✅ Kiểm tra Response Time:
- [ ] Layer 1 (Rule-Based): < 100ms
- [ ] Layer 2 (FAQ): 100-300ms
- [ ] Layer 3 (Gemini AI): 3-8 giây ← **QUAN TRỌNG**

### ✅ Kiểm tra Response Format:
- [ ] Có emoji (📚 ✅ 🎯 💡)
- [ ] Có bullet points hoặc numbered list
- [ ] Có câu hỏi gợi ý ở cuối
- [ ] Tone: Friendly, professional

### ✅ Kiểm tra Response Quality (Gemini):
- [ ] Tiếng Việt đúng ngữ pháp
- [ ] Nội dung có ý nghĩa, logic
- [ ] Không hallucinate (bịa đặt thông tin)
- [ ] Liên quan đến ngữ cảnh trung tâm ngoại ngữ

---

## 🚨 DẤU HIỆU GEMINI ĐANG HOẠT ĐỘNG

### ✅ Response time 3-8 giây:
- Nếu thấy loading spinner quay **3-8 giây** → Gemini đang xử lý
- Nếu < 1 giây → Layer 1 hoặc Layer 2 (không phải Gemini)

### ✅ Response format đặc trưng:
```
👋 Chào bạn [Tên]!

Mình là EduBot, trợ lý AI của Trung tâm Ngoại ngữ.

[Content with emoji and structure]

💡 [Call to action or follow-up question]
```

### ✅ Tone & Style:
- Friendly, warm
- Có empathy
- Có context về trung tâm ngoại ngữ
- Kết thúc bằng câu hỏi gợi ý

---

## 🎓 GHI CHÚ

### Tại sao response time lâu (3-8 giây)?
- ✅ **Bình thường!** Gemini AI cần thời gian để:
  1. Nhận request (network)
  2. Load context (student data)
  3. Generate response (AI reasoning)
  4. Return response (network)

### Làm sao biết đang dùng Gemini API key mới?
- ✅ Model: `gemini-2.5-flash` (June 2025)
- ✅ API Key format: `AQ.Ab8...`
- ✅ Authentication: HTTP Header (`X-Goog-Api-Key`)

### Nếu gặp lỗi?
- Check Laravel logs: `storage/logs/laravel.log`
- Check browser console (F12)
- Verify API key: `php test_gemini_models.php`

---

## 📱 ALTERNATIVE: Test qua Browser Script

Nếu không muốn test qua chatbot widget, test trực tiếp:

```
http://127.0.0.1:8000/test_gemini_browser.php
```

---

**Chúc bạn test thành công! 🚀**

Nếu cần hỗ trợ, check file `GEMINI_API_UPDATE_REPORT.md` để biết chi tiết kỹ thuật.
