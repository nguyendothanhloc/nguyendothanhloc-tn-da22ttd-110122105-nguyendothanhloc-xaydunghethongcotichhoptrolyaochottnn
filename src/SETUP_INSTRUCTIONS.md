# Hướng dẫn cài đặt Laravel 11 Project

## Vấn đề hiện tại
PHP zip extension chưa được bật, khiến composer không thể cài đặt dependencies.

## Giải pháp

### Cách 1: Bật ZIP Extension (Khuyến nghị)

1. Mở file `D:\xampp\php\php.ini` bằng Notepad (chạy as Administrator)
2. Tìm dòng `;extension=zip` (khoảng dòng 962)
3. Xóa dấu `;` ở đầu để thành: `extension=zip`
4. Lưu file
5. Mở terminal mới và chạy:
   ```bash
   composer install --no-interaction
   ```

### Cách 2: Sử dụng XAMPP Control Panel

1. Mở XAMPP Control Panel
2. Click vào "Config" bên cạnh Apache
3. Chọn "PHP (php.ini)"
4. Tìm dòng `;extension=zip`
5. Xóa dấu `;` ở đầu
6. Lưu file và đóng
7. Mở terminal mới và chạy:
   ```bash
   composer install --no-interaction
   ```

## Sau khi cài đặt xong

Chạy các lệnh sau để hoàn tất setup:

```bash
# Generate application key
php artisan key:generate

# Install Laravel Breeze
composer require laravel/breeze --dev
php artisan breeze:install blade

# Install DomPDF
composer require barryvdh/laravel-dompdf

# Install npm dependencies
npm install

# Create database
# Tạo database tên "language_center" trong MySQL

# Run migrations
php artisan migrate

# Setup queue tables
php artisan queue:table
php artisan migrate
```

## Trạng thái hiện tại

✅ Laravel 11 project structure đã được tạo
✅ File .env đã được cấu hình cho MySQL
✅ Database connection: MySQL 8.0
✅ Database name: language_center
✅ Queue driver: database

❌ Composer dependencies chưa được cài đặt đầy đủ (cần bật zip extension)

## Liên hệ

Sau khi bật zip extension và chạy `composer install`, hãy cho tôi biết để tôi tiếp tục cài đặt các packages còn lại.
