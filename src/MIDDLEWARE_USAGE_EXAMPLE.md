# Role Middleware Usage Examples

## Overview
The RoleMiddleware has been successfully implemented and registered. It allows you to restrict routes based on user roles (admin, teacher, student).

## Middleware Registration
The middleware is registered in `bootstrap/app.php` with the alias `'role'`.

## Usage Examples

### 1. Single Role Protection
Protect a route for admin users only:

```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::resource('courses', CourseController::class);
});
```

### 2. Multiple Roles Protection
Allow access to both admin and teacher:

```php
Route::middleware(['auth', 'role:admin,teacher'])->group(function () {
    Route::get('/classes', [ClassController::class, 'index']);
    Route::post('/classes', [ClassController::class, 'store']);
});
```

### 3. Student-Only Routes
Protect routes for students:

```php
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/enrollments', [EnrollmentController::class, 'index']);
    Route::post('/enrollments', [EnrollmentController::class, 'store']);
});
```

### 4. Individual Route Protection
Apply middleware to a single route:

```php
Route::get('/admin/reports', [ReportController::class, 'index'])
    ->middleware(['auth', 'role:admin']);
```

### 5. Controller-Level Protection
Apply middleware in controller constructor:

```php
class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])->except(['index', 'show']);
        $this->middleware(['auth', 'role:student'])->only(['index', 'show']);
    }
}
```

## Behavior

### Authenticated User with Correct Role
- Request proceeds normally
- User can access the protected resource

### Authenticated User with Incorrect Role
- Returns HTTP 403 Forbidden
- Shows "Unauthorized action." message

### Unauthenticated User
- Redirects to login page
- After login, user is redirected back to the intended page

## Testing
Unit tests are available in `tests/Unit/RoleMiddlewareTest.php` covering:
- Access with correct role
- Access denial with incorrect role
- Multiple roles support
- Unauthenticated user redirection

Run tests with:
```bash
php artisan test --filter=RoleMiddlewareTest
```

## Implementation Details

### User Model
- Role field added to `fillable` array
- Role field added to `casts` array as string
- Supports roles: 'admin', 'teacher', 'student'

### Middleware Location
- File: `app/Http/Middleware/RoleMiddleware.php`
- Accepts multiple roles as variadic parameters
- Uses `in_array()` for role checking

### Database
- Role field is an ENUM in the users table migration
- Indexed for performance
- Required field (not nullable)
