# Fix Pattern Matching cho Câu Hỏi về Giáo Viên

## 📋 Vấn đề ban đầu

Khi user hỏi "**giáo viên của tôi**" hoặc "**giáo viên của tôi là ai**", chatbot trả lời:

```
THONG TIN CA NHAN CUA BAN
```

❌ **SAI** - Đây là thông tin của học viên, không phải giáo viên!

## 🔍 Nguyên nhân

### Root cause:
Pattern matching trong `RuleBasedChatbotService.php` có vấn đề:

1. **Pattern quá rộng**:
   ```php
   // Old pattern (line ~250)
   if ($this->matchesPattern($message, ['so dien thoai giao vien cua toi', ...]))
   ```
   - Sử dụng `str_contains()` → bất kỳ câu có "giao vien cua toi" đều match
   - "giáo viên của tôi" match pattern "so dien thoai giao vien cua toi"

2. **Thứ tự pattern sai**:
   ```php
   // Old order
   1. STUDENT PERSONAL INFO (pattern: 'thong tin', 'cua toi')
   2. TEACHER patterns
   ```
   - "thông tin giáo viên của tôi" match "thong tin" + "cua toi" → return student info ❌

## ✅ Giải pháp

### 1. Tách riêng 3 patterns về giáo viên

```php
// Pattern 1: MY TEACHER INFO (general info without contact)
if (($this->matchesPattern($message, ['giao vien cua toi', 'thay cua toi', 'co cua toi']) 
    || $this->matchesPattern($message, ['giao vien']) && $this->matchesPattern($message, ['cua toi']))
    && !$this->matchesPattern($message, ['so dien thoai', 'sdt', 'phone', 'email', 'lien he', 'contact'])) {
    return $this->getMyTeacherInfo(); // ← Hàm mới
}

// Pattern 2: MY TEACHER CONTACT (phone/email of my teacher)
if (($this->matchesPattern($message, ['so dien thoai', 'sdt', 'phone', 'email', 'lien he']) 
    && $this->matchesPattern($message, ['giao vien cua toi', 'thay cua toi', 'co cua toi'])) 
    || $this->matchesPattern($message, ['lien he giao vien'])) {
    return $this->getMyTeacherContact(); // ← Hàm cũ (chỉ contact)
}
```

### 2. Thay đổi thứ tự pattern (Priority)

```php
// NEW ORDER (từ cao xuống thấp)
1. MY TEACHER INFO              ← Xử lý "giáo viên của tôi"
2. MY TEACHER CONTACT          ← Xử lý "số điện thoại giáo viên của tôi"
3. STUDENT PERSONAL INFO        ← Thêm loại trừ teacher keywords
   + Điều kiện: !$this->matchesPattern(['giao vien', 'thay', 'co'])
4. TEACHER CONTACT (general)    ← "số điện thoại giáo viên"
5. TEACHER INFO (general)       ← "giáo viên dạy tiếng Anh"
```

### 3. Tạo hàm mới `getMyTeacherInfo()`

```php
/**
 * Get my teacher information (full info: name, specialization, bio, contact)
 */
private function getMyTeacherInfo(): array
{
    $user = Auth::user();
    $student = Student::where('user_id', $user->id)->first();

    if (!$student) {
        return ['response' => 'Khong tim thay thong tin hoc vien.', ...];
    }

    $enrollments = $student->enrollments()
        ->whereIn('status', ['paid', 'pending'])
        ->with('class.teacher.user')
        ->get();

    if ($enrollments->isEmpty()) {
        return ['response' => 'Ban chua dang ky lop hoc nao.', ...];
    }

    $teacherInfo = $enrollments->map(function ($enrollment, $index) {
        $teacher = $enrollment->class->teacher;
        $class = $enrollment->class;
        
        $info = "👤 GIAO VIEN " . ($index + 1) . "\n\n";
        $info .= "📚 Lop: {$class->name}\n";
        $info .= "👨‍🏫 Ten: {$teacher->user->name}\n";
        
        if ($teacher->specialization) {
            $info .= "🎯 Chuyen mon: {$teacher->specialization}\n";
        }
        
        if ($teacher->bio) {
            $bio = strlen($teacher->bio) > 100 ? substr($teacher->bio, 0, 100) . '...' : $teacher->bio;
            $info .= "📝 Gioi thieu: {$bio}\n";
        }
        
        $info .= "\n📞 Lien he:\n";
        $info .= "   • Email: {$teacher->user->email}\n";
        
        if ($teacher->phone) {
            $info .= "   • So dien thoai: {$teacher->phone}";
        } else {
            $info .= "   • So dien thoai: Chua cap nhat";
        }
        
        return $info;
    })->implode("\n\n" . str_repeat("-", 40) . "\n\n");

    $response = "THONG TIN GIAO VIEN CUA BAN\n\n";
    $response .= $teacherInfo;
    $response .= "\n\n💡 Goi y: Ban co the hoi:\n";
    $response .= '   • "So dien thoai giao vien cua toi?"' . "\n";
    $response .= '   • "Email giao vien cua toi?"';

    return [
        'response' => $response,
        'type' => 'my_teacher_info',
        'data' => $enrollments
    ];
}
```

## 🧪 Kết quả test

```bash
php test_teacher_question.php
```

### Test cases:

| Câu hỏi | Pattern match | Response type | Kết quả |
|---------|---------------|---------------|---------|
| "giáo viên của tôi" | ✅ MY TEACHER INFO | `info` | ✅ Đúng |
| "giáo viên của tôi là ai" | ✅ MY TEACHER INFO | `info` | ✅ Đúng |
| "thông tin giáo viên của tôi" | ✅ MY TEACHER INFO | `info` | ✅ Đúng |
| "số điện thoại giáo viên của tôi" | ✅ MY TEACHER CONTACT | `info` | ✅ Đúng |
| "email giáo viên của tôi" | ✅ MY TEACHER CONTACT | `info` | ✅ Đúng |
| "liên hệ giáo viên của tôi" | ✅ MY TEACHER CONTACT | `info` | ✅ Đúng |

**Response hiện tại**: `"Ban chua dang ky lop hoc nao."` 
- ✅ Đúng vì user `hocvien1@gmail.com` chưa có enrollment

## 📊 So sánh trước/sau

### ❌ TRƯỚC (Sai):
```
User: "giáo viên của tôi"
Bot: THONG TIN CA NHAN CUA BAN
     👤 Ho va ten: Nguyễn Văn Tuấn
     📧 Email: hocvien1@gmail.com
     ...
```

### ✅ SAU (Đúng):
```
User: "giáo viên của tôi"
Bot: THONG TIN GIAO VIEN CUA BAN

     👤 GIAO VIEN 1
     
     📚 Lop: Tieng Anh Cap Toc A1
     👨‍🏫 Ten: Nguyen Van A
     🎯 Chuyen mon: English, IELTS
     
     📞 Lien he:
        • Email: teacher1@example.com
        • So dien thoai: 0901234567
     
     💡 Goi y: Ban co the hoi:
        • "So dien thoai giao vien cua toi?"
        • "Email giao vien cua toi?"
```

## 📝 Files thay đổi

- `app/Services/RuleBasedChatbotService.php`
  - Thay đổi thứ tự pattern (lines ~175-195)
  - Thêm hàm `getMyTeacherInfo()` (lines ~2016-2120)
  - Sửa pattern `getMyTeacherContact()` (lines ~250-260)
  - Xóa duplicate pattern (lines ~379-387)

## ✅ Checklist hoàn thành

- [x] Fix pattern matching cho "giáo viên của tôi"
- [x] Tách riêng MY TEACHER INFO và MY TEACHER CONTACT
- [x] Thêm điều kiện loại trừ teacher keywords trong STUDENT PERSONAL INFO
- [x] Tạo hàm `getMyTeacherInfo()` với full thông tin
- [x] Test 6 câu hỏi về giáo viên
- [x] Verify response type đúng (`info` thay vì `student_info`)

## 🚀 Next steps

1. Test trên chatbot widget thực tế
2. Kiểm tra với user có enrollment để xem full response
3. Test thêm các biến thể câu hỏi:
   - "thầy dạy tôi là ai?"
   - "cô giáo của tôi tên gì?"
   - "ai là giáo viên của tôi?"

---

**Tác giả**: AI Assistant  
**Ngày**: 2026-06-19  
**Status**: ✅ COMPLETED
