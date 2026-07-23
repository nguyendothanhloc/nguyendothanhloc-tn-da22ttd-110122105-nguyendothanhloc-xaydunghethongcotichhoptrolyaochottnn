# Language Center Management System - Setup Guide

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.2 or higher** with extensions:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath
  - Fileinfo
  - GD (for PDF generation)
  
- **Composer** (latest version)
- **Node.js 18+** and **npm**
- **MySQL 8.0** or higher
- **Git** (optional, for version control)

## 🚀 Quick Start

### 1. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure Database

Edit your `.env` file and update the database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=language_center
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### 4. Create Database

**Option A: Using MySQL Command Line**
```bash
mysql -u root -p
CREATE DATABASE language_center CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Option B: Using MySQL Workbench**
1. Open MySQL Workbench
2. Connect to your MySQL server
3. Click "Create a new schema" button
4. Name it `language_center`
5. Set charset to `utf8mb4` and collation to `utf8mb4_unicode_ci`
6. Click "Apply"

**Option C: Using phpMyAdmin**
1. Open phpMyAdmin in your browser
2. Click "New" in the left sidebar
3. Enter database name: `language_center`
4. Select collation: `utf8mb4_unicode_ci`
5. Click "Create"

### 5. Run Migrations

```bash
php artisan migrate
```

This will create all necessary tables for authentication, sessions, cache, and queue jobs.

### 6. Build Frontend Assets

```bash
# For development (with hot reload)
npm run dev

# For production
npm run build
```

### 7. Start Development Server

```bash
php artisan serve
```

Your application will be available at: `http://localhost:8000`

## 🔧 Additional Configuration

### Queue Worker (for Email Notifications)

In a separate terminal, start the queue worker:

```bash
php artisan queue:work
```

For development, you can use:
```bash
php artisan queue:listen
```

### Mail Configuration (Optional)

To enable email notifications, update your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@languagecenter.com"
MAIL_FROM_NAME="Language Center"
```

**For Development:** Use [Mailtrap](https://mailtrap.io/) or [MailHog](https://github.com/mailhog/MailHog)

**For Production:** Use services like:
- Gmail SMTP
- SendGrid
- Amazon SES
- Mailgun

### Google Gemini API (for Virtual Assistant)

1. Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy the API key
5. Add it to your `.env` file:

```env
GEMINI_API_KEY=your_api_key_here
```

## 🗄️ Database Seeding (Optional)

To populate the database with sample data for testing:

```bash
php artisan db:seed
```

## 🧪 Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## 📁 Project Structure

```
language-center-management-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Application controllers
│   │   ├── Middleware/      # Custom middleware
│   │   └── Requests/        # Form request validation
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   └── View/                # View components
├── config/                  # Configuration files
├── database/
│   ├── factories/           # Model factories
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── public/                  # Public assets
├── resources/
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript files
│   └── views/               # Blade templates
├── routes/
│   ├── web.php             # Web routes
│   └── api.php             # API routes
├── storage/                 # File storage
├── tests/                   # Test files
├── .env                     # Environment configuration
├── composer.json            # PHP dependencies
└── package.json             # Node.js dependencies
```

## 🔐 Default User Roles

The system supports three user roles:

1. **Admin** - Full system access
2. **Teacher** - Manage classes, schedules, attendance, assessments
3. **Student** - Enroll in courses, view progress, use virtual assistant

## 🛠️ Troubleshooting

### Database Connection Error

**Error:** `SQLSTATE[HY000] [1049] Unknown database 'language_center'`

**Solution:** Make sure you've created the database (see Step 4 above)

### Permission Errors

**Error:** `The stream or file could not be opened`

**Solution:** Set proper permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

On Windows, make sure your user has write permissions to these directories.

### Vite Build Errors

**Error:** `Cannot find module 'bootstrap'`

**Solution:** Reinstall npm dependencies:
```bash
rm -rf node_modules package-lock.json
npm install
```

### Queue Jobs Not Processing

**Solution:** Make sure the queue worker is running:
```bash
php artisan queue:work
```

For production, use a process manager like Supervisor.

## 📚 Documentation

- [Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)
- [jQuery Documentation](https://api.jquery.com/)
- [DomPDF Documentation](https://github.com/barryvdh/laravel-dompdf)
- [Google Gemini API Documentation](https://ai.google.dev/docs)

## 🤝 Support

For issues and questions:
1. Check the troubleshooting section above
2. Review the Laravel documentation
3. Check the project's issue tracker

## 📝 License

This project is open-sourced software licensed under the MIT license.

---

**Happy Coding! 🎉**
