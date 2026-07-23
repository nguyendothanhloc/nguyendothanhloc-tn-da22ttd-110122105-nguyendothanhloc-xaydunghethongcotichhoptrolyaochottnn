# Sửa lỗi đếm số lớp đang mở - Class Count Fix

## Vấn đề
Khi admin tạo lớp học mới, số lượng "lớp đang mở" hiển thị ở trang khóa học của học viên không chính xác.

**Nguyên nhân**: Code đang đếm classes với status = 'active', nhưng trong database, status của ClassModel là: 'upcoming', 'ongoing', 'completed' - không có giá trị 'active'.

## Class Status Values
Các giá trị status hợp lệ cho ClassModel:
- ✅ **upcoming** - Lớp sắp diễn ra (chưa bắt đầu)
- ✅ **ongoing** - Lớp đang diễn ra (đang học)
- ❌ **completed** - Lớp đã hoàn thành (không tính là "đang mở")

## Các thay đổi

### 1. app/Services/CourseService.php
**Trước:**
```php
$query->withCount(['classes' => function ($q) {
    $q->where('status', 'active');  // ❌ SAI - không có status 'active'
}]);
```

**Sau:**
```php
$query->withCount(['classes' => function ($q) {
    $q->whereIn('status', ['upcoming', 'ongoing']);  // ✅ ĐÚNG
}]);
```

### 2. resources/views/welcome.blade.php
**Trước:**
```php
$courses = \App\Models\Course::where('is_active', true)
    ->withCount(['classes' => function($q) {
        $q->where('status', 'active');  // ❌ SAI
    }])
```

**Sau:**
```php
$courses = \App\Models\Course::where('is_active', true)
    ->withCount(['classes' => function($q) {
        $q->whereIn('status', ['upcoming', 'ongoing']);  // ✅ ĐÚNG
    }])
```

## Logic đếm lớp đang mở

**Lớp được tính là "đang mở"** khi:
- ✅ Status = 'upcoming' (sắp diễn ra, chưa bắt đầu học)
- ✅ Status = 'ongoing' (đang diễn ra, đang trong quá trình học)

**Lớp KHÔNG được tính** khi:
- ❌ Status = 'completed' (đã hoàn thành, học viên không thể đăng ký)

## Các màn hình bị ảnh hưởng

1. ✅ **Trang chủ (welcome.blade.php)** - Hiển thị 6 khóa học nổi bật với số lớp đang mở
2. ✅ **Khám phá khóa học (courses/browse.blade.php)** - Danh sách khóa học cho học viên
3. ✅ **Chi tiết khóa học (courses/detail.blade.php)** - Hiển thị thông tin khóa học

## Test Case

### Scenario: Admin tạo lớp mới
1. Admin tạo 1 lớp mới cho khóa "Tiếng Anh Beginner" với status = 'upcoming'
2. Học viên vào trang "Khám phá khóa học"
3. **Kết quả mong đợi**: Số lớp đang mở tăng lên 1
4. **Kết quả trước khi fix**: Số lớp không thay đổi (vì đếm sai status)
5. **Kết quả sau khi fix**: ✅ Số lớp hiển thị chính xác

### Scenario: Lớp học hoàn thành
1. Admin cập nhật status của lớp từ 'ongoing' → 'completed'
2. **Kết quả mong đợi**: Số lớp đang mở giảm đi 1
3. **Kết quả sau khi fix**: ✅ Số lớp hiển thị chính xác

## Cache
- ✅ View cache đã được clear
- ✅ Thay đổi có hiệu lực ngay

## Ngày thực hiện
10/06/2026
