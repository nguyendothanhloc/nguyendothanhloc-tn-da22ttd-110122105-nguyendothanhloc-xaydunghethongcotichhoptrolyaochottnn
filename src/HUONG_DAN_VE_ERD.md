# HƯỚNG DẪN VẼ SƠ ĐỒ ERD - HỆ THỐNG QUẢN LÝ TRUNG TÂM NGOẠI NGỮ

## 📁 CÁC FILE ERD ĐÃ TẠO

Tôi đã tạo 3 file ERD cho bạn với các định dạng khác nhau:

### 1. **ERD_MERMAID.md** 
- ✅ Xem trực tiếp trên **GitHub** (tự động render)
- ✅ Xem trên **VSCode** với extension "Markdown Preview Mermaid Support"
- ✅ Xuất hình tại https://mermaid.live/

### 2. **ERD_DBDIAGRAM.dbml**
- ✅ Vẽ ERD **đẹp nhất, chuyên nghiệp nhất**
- ✅ Truy cập https://dbdiagram.io/
- ✅ Copy/paste toàn bộ nội dung vào editor
- ✅ Export PNG/PDF/SQL

### 3. **ERD_PLANTUML.puml**
- ✅ Sử dụng PlantUML (công cụ UML phổ biến)
- ✅ Cài PlantUML extension cho VSCode
- ✅ Hoặc render online tại http://www.plantuml.com/plantuml/

---

## 🎯 KHUYẾN NGHỊ SỬ DỤNG

### Cho Khóa luận (Đề xuất):
👉 **Sử dụng dbdiagram.io** (ERD_DBDIAGRAM.dbml)

**Lý do:**
- ✅ Sơ đồ đẹp, chuyên nghiệp nhất
- ✅ Tự động sắp xếp bảng hợp lý
- ✅ Hiển thị rõ quan hệ 1-1, 1-N
- ✅ Export chất lượng cao (PNG, PDF)
- ✅ Phù hợp với khóa luận tốt nghiệp

---

## 📝 HƯỚNG DẪN CHI TIẾT

### CÁCH 1: Sử dụng dbdiagram.io (Khuyến nghị ⭐⭐⭐⭐⭐)

**Bước 1:** Truy cập https://dbdiagram.io/

**Bước 2:** Mở file `ERD_DBDIAGRAM.dbml`

**Bước 3:** Copy **toàn bộ** nội dung file

**Bước 4:** Paste vào ô editor bên trái của dbdiagram.io

**Bước 5:** Sơ đồ ERD sẽ tự động hiện ra bên phải 🎉

**Bước 6:** Chỉnh sửa layout (kéo thả các bảng để sắp xếp đẹp hơn)

**Bước 7:** Export sơ đồ:
- Click **Export** ở góc trên
- Chọn **Export to PNG** (độ phân giải cao)
- Hoặc **Export to PDF** (cho khóa luận in)

**Tính năng hay:**
- Tự động phân nhóm bảng theo TableGroup
- Hiển thị rõ Primary Key (màu cam)
- Hiển thị Foreign Key với mũi tên liên kết
- Có tooltip khi hover vào từng trường
- Có note giải thích cho mỗi bảng

---

### CÁCH 2: Sử dụng Mermaid (Xem nhanh)

**Bước 1:** Mở file `ERD_MERMAID.md`

**Bước 2:** Chọn một trong các cách sau:

#### Option A: Xem trên VSCode
1. Cài extension **"Markdown Preview Mermaid Support"**
2. Mở file `ERD_MERMAID.md`
3. Nhấn `Ctrl+Shift+V` để xem preview
4. Sơ đồ sẽ hiện ra ngay

#### Option B: Xem trên GitHub
1. Commit file `ERD_MERMAID.md` lên GitHub
2. GitHub tự động render sơ đồ Mermaid
3. Xem sơ đồ trực tiếp trên repo

#### Option C: Xuất hình bằng mermaid.live
1. Truy cập https://mermaid.live/
2. Copy **phần code trong ``` ```** (không copy dòng ```mermaid)
3. Paste vào editor
4. Click **Download PNG** hoặc **Download SVG**

---

### CÁCH 3: Sử dụng PlantUML

**Bước 1:** Cài đặt PlantUML

**Option A: Trên VSCode**
1. Cài extension **"PlantUML"**
2. Mở file `ERD_PLANTUML.puml`
3. Nhấn `Alt+D` để preview

**Option B: Online**
1. Truy cập http://www.plantuml.com/plantuml/
2. Copy **toàn bộ** nội dung `ERD_PLANTUML.puml`
3. Paste vào editor
4. Click **Submit** để render
5. Download PNG/SVG

---

## 🎨 SO SÁNH CÁC CÔNG CỤ

| Tiêu chí | dbdiagram.io | Mermaid | PlantUML |
|----------|--------------|---------|----------|
| Độ đẹp | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ |
| Dễ sử dụng | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ |
| Tùy chỉnh layout | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐ |
| Chất lượng xuất | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| Phù hợp khóa luận | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| Miễn phí | ✅ Có | ✅ Miễn phí | ✅ Miễn phí |

**Kết luận:** 👉 **Dùng dbdiagram.io** cho khóa luận

---

## 📊 CẤU TRÚC DATABASE TỔNG QUAN

### Tổng số: **17 bảng**, chia thành **8 nhóm**

#### NHÓM 1: Quản lý Người dùng (3 bảng)
- `users` - Tài khoản người dùng
- `students` - Hồ sơ học viên
- `teachers` - Hồ sơ giáo viên

#### NHÓM 2: Khóa học & Lớp học (3 bảng)
- `courses` - Khóa học
- `classes` - Lớp học
- `enrollments` - Đăng ký học

#### NHÓM 3: Lịch học & Điểm danh (2 bảng)
- `schedules` - Lịch học
- `attendances` - Điểm danh

#### NHÓM 4: Đánh giá & Kiểm tra (3 bảng)
- `assessments` - Bài kiểm tra
- `assessment_scores` - Điểm kiểm tra
- `certificates` - Chứng chỉ

#### NHÓM 5: Thanh toán (1 bảng)
- `payments` - Thanh toán học phí

#### NHÓM 6: Phản hồi (1 bảng)
- `feedbacks` - Đánh giá khóa học

#### NHÓM 7: Thông báo (1 bảng)
- `notifications` - Thông báo hệ thống

#### NHÓM 8: Chatbot (3 bảng)
- `conversations` - Cuộc trò chuyện
- `messages` - Tin nhắn
- `chatbot_knowledge` - Kiến thức chatbot

---

## 🔗 MỐI QUAN HỆ CHÍNH

### Quan hệ 1-1 (One-to-One):
- `users` ↔ `students` (1 user có tối đa 1 hồ sơ học viên)
- `users` ↔ `teachers` (1 user có tối đa 1 hồ sơ giáo viên)
- `enrollments` ↔ `payments` (1 đăng ký có 1 thanh toán)

### Quan hệ 1-N (One-to-Many):
- `courses` → `classes` (1 khóa học có nhiều lớp)
- `teachers` → `classes` (1 giáo viên dạy nhiều lớp)
- `students` → `enrollments` (1 học viên đăng ký nhiều lớp)
- `classes` → `schedules` (1 lớp có nhiều buổi học)
- `classes` → `assessments` (1 lớp có nhiều bài kiểm tra)
- `students` → `conversations` (1 học viên có nhiều cuộc trò chuyện)
- ... (và nhiều quan hệ khác)

### Bảng trung gian (Many-to-Many):
- `enrollments` (Kết nối `students` ↔ `classes`)

---

## 💡 LƯU Ý KHI VẼ ERD CHO KHÓA LUẬN

### 1. Độ phân giải
- Export ở độ phân giải **tối thiểu 300 DPI**
- Đảm bảo chữ **rõ ràng, dễ đọc**
- Tránh bị vỡ hình khi in

### 2. Sắp xếp layout
- **Nhóm các bảng liên quan gần nhau**
- Ví dụ: Đặt `users`, `students`, `teachers` gần nhau
- Tránh đường nối chéo nhau nhiều

### 3. Chú thích
- Thêm **chú thích** giải thích:
  - PK = Primary Key (Khóa chính)
  - FK = Foreign Key (Khóa ngoại)
  - UK = Unique Key (Duy nhất)
- Giải thích ký hiệu quan hệ:
  - `1:1` = One-to-One
  - `1:N` = One-to-Many

### 4. Màu sắc
- Sử dụng màu để **phân nhóm bảng**
- Ví dụ: 
  - Màu xanh: Người dùng
  - Màu vàng: Khóa học
  - Màu đỏ: Thanh toán

### 5. Đặt tên
- Đặt tên hình: `Hinh_2.X_So_do_ERD_Database.png`
- Thêm caption: "Hình 2.X: Sơ đồ ERD cơ sở dữ liệu hệ thống quản lý trung tâm ngoại ngữ"

---

## ❓ CÂU HỎI THƯỜNG GẶP

### Q1: File nào nên dùng cho khóa luận?
**A:** Dùng `ERD_DBDIAGRAM.dbml` với công cụ dbdiagram.io

### Q2: Làm sao để export độ phân giải cao?
**A:** 
- dbdiagram.io: Export PNG (tự động HD)
- Mermaid: Chọn SVG (vector, không giới hạn độ phân giải)
- PlantUML: Cài đặt DPI trong file config

### Q3: Có thể chỉnh sửa được không?
**A:** Có! 
- Mở file `.dbml` hoặc `.puml`
- Sửa trực tiếp trong file text
- Các công cụ sẽ tự động render lại

### Q4: Tôi muốn thêm/bớt bảng?
**A:** 
1. Mở file text (.dbml hoặc .puml)
2. Thêm/xóa định nghĩa bảng
3. Thêm/xóa mối quan hệ (ref)
4. Render lại

### Q5: Có cần học ngôn ngữ DBML/PlantUML không?
**A:** Không! Tôi đã tạo sẵn hết rồi. Bạn chỉ cần copy/paste thôi.

---

## 📚 TÀI LIỆU THAM KHẢO

- **dbdiagram.io:** https://dbdiagram.io/docs
- **Mermaid:** https://mermaid.js.org/syntax/entityRelationshipDiagram.html
- **PlantUML:** https://plantuml.com/ie-diagram

---

## ✅ CHECKLIST HOÀN THÀNH

- [x] Tạo file ERD_MERMAID.md
- [x] Tạo file ERD_DBDIAGRAM.dbml
- [x] Tạo file ERD_PLANTUML.puml
- [x] Tạo file hướng dẫn này
- [ ] Bạn vẽ ERD bằng dbdiagram.io
- [ ] Export hình PNG/PDF chất lượng cao
- [ ] Thêm vào khóa luận với caption đầy đủ
- [ ] Kiểm tra độ rõ nét khi in thử

---

**Chúc bạn vẽ ERD thành công! 🎉**

Nếu gặp khó khăn, hãy cho tôi biết nhé!
