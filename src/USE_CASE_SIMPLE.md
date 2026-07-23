# Use Case Diagram - Hệ thống Quản lý Trung tâm Ngoại ngữ (PHIÊN BẢN GỌN)

## 📋 Copy đoạn này gửi ChatGPT:

```
Hãy vẽ Use Case Diagram từ mã PlantUML này, giữ nguyên tiếng Việt và xuất ra ảnh PNG đẹp:

@startuml
left to right direction
skinparam actorStyle awesome

actor "Khách" as Guest
actor "Học viên" as Student  
actor "Giáo viên" as Teacher
actor "Quản trị viên" as Admin
actor "Gemini AI" as AI

rectangle "Hệ thống Quản lý Trung tâm Ngoại ngữ" {
  
  ' Khách
  (Xem khóa học) as UC1
  (Đăng ký tài khoản) as UC2
  
  ' Học viên - Nhóm chính
  (Đăng nhập) as UC3
  (Đăng ký khóa học) as UC4
  (Quản lý lớp học) as UC5
  (Chat với Chatbot AI) as UC6
  
  ' Chatbot 3 lớp (Tính năng đặc trưng)
  (Khớp mẫu câu hỏi) as UC7
  (Tra cứu FAQ) as UC8  
  (Hỏi Gemini AI) as UC9
  
  ' Giáo viên
  (Quản lý lớp dạy) as UC10
  (Điểm danh và chấm điểm) as UC11
  
  ' Quản trị viên
  (Quản lý hệ thống) as UC12
  (Quản lý FAQ Chatbot) as UC13
}

' Kết nối
Guest --> UC1
Guest --> UC2

Student --> UC3
Student --> UC4
Student --> UC5
Student --> UC6

UC6 ..> UC7 : <<bao gồm>>
UC6 ..> UC8 : <<bao gồm>>
UC6 ..> UC9 : <<bao gồm>>
UC9 --> AI : <<sử dụng>>

Teacher --> UC3
Teacher --> UC10
Teacher --> UC11

Admin --> UC3
Admin --> UC12
Admin --> UC13

note right of UC6
  Chatbot 3 lớp thông minh:
  1. Khớp mẫu câu hỏi (nhanh)
  2. Tra cứu FAQ (chính xác)
  3. Hỏi Gemini AI (phức tạp)
end note

@enduml
```

---

## 📖 Giải thích đơn giản cho Use Case:

### 👤 **Khách**
- **UC1: Xem khóa học** - Xem thông tin các khóa học tiếng Anh/Nhật/Hàn/Trung, thông tin giáo viên
- **UC2: Đăng ký tài khoản** - Đăng ký làm học viên hoặc giáo viên mới

### 👨‍🎓 **Học viên**
- **UC3: Đăng nhập** - Đăng nhập vào hệ thống với email và mật khẩu
- **UC4: Đăng ký khóa học** - Chọn và đăng ký tham gia khóa học mong muốn
- **UC5: Quản lý lớp học** - Xem lịch học, điểm số, điểm danh của các lớp đã đăng ký
- **UC6: Chat với Chatbot AI** - Hỏi đáp với Chatbot thông minh 3 lớp **(TÍNH NĂNG ĐẶC TRƯNG)**

### 🤖 **Chatbot 3 lớp - Tính năng đặc trưng của hệ thống**
- **UC7: Khớp mẫu câu hỏi** - Trả lời nhanh câu hỏi về học phí, giáo viên, lịch học từ database
- **UC8: Tra cứu FAQ** - Trả lời câu hỏi về chính sách hoàn tiền, chuyển lớp, nghỉ học từ cơ sở tri thức
- **UC9: Hỏi Gemini AI** - Xử lý câu hỏi phức tạp bằng AI của Google khi 2 lớp trên không trả lời được

### 👨‍🏫 **Giáo viên**
- **UC3: Đăng nhập** - Đăng nhập vào hệ thống
- **UC10: Quản lý lớp dạy** - Xem thông tin lớp được phân công, danh sách học viên trong lớp
- **UC11: Điểm danh và chấm điểm** - Điểm danh học viên mỗi buổi học và nhập điểm đánh giá

### 👨‍💼 **Quản trị viên**
- **UC3: Đăng nhập** - Đăng nhập vào hệ thống
- **UC12: Quản lý hệ thống** - Quản lý khóa học, lớp học, học viên, giáo viên, đăng ký, xem báo cáo
- **UC13: Quản lý FAQ Chatbot** - Thêm/sửa/xóa câu hỏi và câu trả lời trong cơ sở tri thức FAQ

---

## 🎯 Điểm đặc biệt của hệ thống:

### **Chatbot AI 3 lớp thông minh:**

**Lớp 1 - Khớp mẫu câu hỏi (Nhanh nhất):**
- Ví dụ: "Học phí tiếng Anh bao nhiêu?" → Trả lời ngay từ database
- Ví dụ: "Giáo viên dạy tiếng Hàn là ai?" → Lấy thông tin từ database

**Lớp 2 - Tra cứu FAQ (Chính xác):**
- Ví dụ: "Chính sách hoàn tiền như thế nào?" → Trả lời từ FAQ do Admin quản lý
- Ví dụ: "Muốn chuyển lớp thì làm sao?" → Trả lời từ cơ sở tri thức

**Lớp 3 - Gemini AI (Thông minh):**
- Ví dụ: "Tôi nên học tiếng gì để đi du học Nhật?" → AI phân tích và tư vấn
- Ví dụ: Câu hỏi phức tạp không khớp với 2 lớp trên

**Ưu điểm:**
- Admin có thể cập nhật FAQ để chatbot trả lời đúng theo chính sách trung tâm
- Học viên được hỗ trợ 24/7 không cần chờ nhân viên

---

## ✨ Tổng kết:

- **4 Actor:** Khách, Học viên, Giáo viên, Admin (+ Gemini AI)
- **13 Use Case chính** (đã gộp các UC phụ để dễ nhìn)
- **Tính năng đặc trưng:** Chatbot AI 3 lớp với khả năng học hỏi từ FAQ

---

**Lưu ý:** Đây là phiên bản rút gọn để dễ trình bày. Mỗi Use Case chính (như "Quản lý lớp học", "Điểm danh & Chấm điểm") bao gồm nhiều chức năng con bên trong.
