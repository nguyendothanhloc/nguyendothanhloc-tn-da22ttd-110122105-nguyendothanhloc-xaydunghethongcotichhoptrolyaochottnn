# Chatbot Questions - Before vs After Comparison

## Question Coverage

| Category | Before | After | New Questions Added |
|----------|--------|-------|---------------------|
| **Course Info** | 5 questions | 6 questions | ✅ "Khi nào tôi tốt nghiệp?" |
| **Teacher Info** | 5 questions | 7 questions | ✅ "Số điện thoại/Email giáo viên của tôi?" |
| **Schedule** | 5 questions | 6 questions | ✅ "Hôm nay tôi học môn gì?" |
| **Class Info** | 3 questions | 4 questions | ✅ "Bạn cùng lớp của tôi là ai?" |
| **Grades** | 2 questions | 2 questions | - |
| **Attendance** | 1 question | 3 questions | ✅ "Vắng bao nhiêu buổi?", "Đi muộn bao nhiêu lần?" |
| **Payment** | 2 questions | 3 questions | ✅ "Còn nợ bao nhiêu tiền?" |
| **Certificate** | 1 question | 1 question | - |
| **Contact** | 2 questions | 3 questions | ✅ "Giờ làm việc của trung tâm?" |
| **TOTAL** | **26 questions** | **35 questions** | **+9 questions** |

## New Methods Added

| # | Method Name | Purpose |
|---|-------------|---------|
| 1 | `getGraduationDate()` | Calculate expected graduation date |
| 2 | `getClassmates()` | List students in same class |
| 3 | `getAbsentCount()` | Count absent sessions |
| 4 | `getLateCount()` | Count late arrivals |
| 5 | `getTodaySubjects()` | Show today's subjects/courses |
| 6 | `getUnpaidAmount()` | Calculate unpaid fees |
| 7 | `getMyTeacherContact()` | Show teacher contact info |
| 8 | `getOfficeHours()` | Display center operating hours |

## Pattern Matching Strategy

### Pattern Priority Order (in processMessage)
1. **Specific patterns** (language-specific, multi-keyword patterns)
2. **New specific patterns** (8 new patterns added here)
3. **General patterns** (single-keyword, broad patterns)

### Example Pattern Hierarchy
```
SPECIFIC (checked first):
├── "giao vien" + "tieng anh" → getTeacherByLanguage('English')
├── "hoc phi" + "tieng nhat" → getSpecificCourseFee('Japanese')
├── "khi nao" + "tot nghiep" → getGraduationDate() ✨ NEW
├── "ban cung lop" → getClassmates() ✨ NEW
└── "vang bao nhieu buoi" → getAbsentCount() ✨ NEW

GENERAL (checked last):
├── "giao vien" (general) → getTeacherInfo()
├── "hoc phi" (general) → getFeeInformation()
└── "diem danh" (general) → getAttendanceInfo()
```

## Database Queries Used

### New Database Interactions
| Method | Tables Used | Key Columns |
|--------|-------------|-------------|
| `getGraduationDate()` | enrollments, courses | enrollment_date, duration_weeks |
| `getClassmates()` | enrollments, students, users | class_id, student_id |
| `getAbsentCount()` | attendances | student_id, status='absent' |
| `getLateCount()` | attendances | student_id, status='late' |
| `getTodaySubjects()` | schedules, classes, courses | date, class_id |
| `getUnpaidAmount()` | payments, enrollments | status!='paid', amount |
| `getMyTeacherContact()` | enrollments, teachers, users | class_id, teacher_id |
| `getOfficeHours()` | (none - static data) | - |

## Updated Help Section

### New Sections in Help Message
```
VE KHOA HOC:
+ "Khi nao toi tot nghiep?" ✨ NEW

VE GIAO VIEN:
+ "So dien thoai giao vien cua toi?" ✨ NEW
+ "Email giao vien cua toi?" ✨ NEW

VE LICH HOC:
+ "Hom nay toi hoc mon gi?" ✨ NEW

VE LOP HOC:
+ "Ban cung lop cua toi la ai?" ✨ NEW

VE DIEM DANH: ✨ NEW SECTION
+ "Toi vang bao nhieu buoi?" ✨ NEW
+ "Toi di muon bao nhieu lan?" ✨ NEW

VE THANH TOAN:
+ "Con no bao nhieu tien?" ✨ NEW

LIEN HE:
+ "Gio lam viec cua trung tam?" ✨ NEW
```

## Implementation Quality

### ✅ Requirements Met
- [x] 8 new patterns added
- [x] 8 new methods implemented
- [x] Patterns placed in correct order (specific before general)
- [x] No emojis/icons in responses
- [x] Vietnamese accent removal for pattern matching
- [x] Clear, numbered lists in responses
- [x] Database schema requirements followed
- [x] PHP syntax validation passed
- [x] Help message updated
- [x] Default response updated

### Code Quality
- Follows existing code structure
- Consistent naming conventions
- Proper error handling
- Clear method documentation
- Efficient database queries with eager loading

## Testing Recommendations

### Test Cases to Run
1. **Graduation Date**: Ask "Khi nào tôi tốt nghiệp?" as enrolled student
2. **Classmates**: Ask "Bạn cùng lớp của tôi là ai?" with multiple enrollments
3. **Absent Count**: Ask "Tôi vắng bao nhiêu buổi?" with attendance records
4. **Late Count**: Ask "Tôi đi muộn bao nhiêu lần?" with late records
5. **Today's Subjects**: Ask "Hôm nay tôi học môn gì?" on a scheduled day
6. **Unpaid Amount**: Ask "Còn nợ bao nhiêu tiền?" with unpaid payments
7. **Teacher Contact**: Ask "Số điện thoại giáo viên của tôi?" as enrolled student
8. **Office Hours**: Ask "Giờ làm việc của trung tâm?" (always works)

### Edge Cases Handled
- Student without enrollments
- No attendance records
- All payments paid
- No classmates in class
- No schedule today
- Missing teacher contact info
