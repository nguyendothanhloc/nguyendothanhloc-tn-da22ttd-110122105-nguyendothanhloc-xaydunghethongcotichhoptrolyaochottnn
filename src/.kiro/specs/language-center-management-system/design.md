# Design Document: Language Center Management System

## Overview

Hệ thống quản lý khóa học trung tâm ngoại ngữ là một ứng dụng web monolithic sử dụng kiến trúc MVC với Laravel 11. Hệ thống cung cấp các chức năng quản lý khóa học, lớp học, học viên, giáo viên, lịch học, điểm danh, đánh giá, thanh toán và tích hợp trợ lý ảo AI để hỗ trợ tư vấn và học tập.

### Core Technologies

- **Backend Framework**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templates + Bootstrap 5 + jQuery
- **Database**: MySQL 8.0
- **AI Integration**: Google Gemini API (free tier) + Rule-based chatbot
- **Architecture Pattern**: Monolithic MVC
- **Authentication**: Laravel Breeze/Sanctum
- **PDF Generation**: DomPDF/TCPDF
- **Email**: Laravel Mail with Queue

### Design Principles

1. **Simplicity First**: Giữ thiết kế đơn giản nhất có thể trong khi vẫn đáp ứng đầy đủ yêu cầu
2. **MVC Standard**: Tuân thủ nghiêm ngặt pattern MVC của Laravel
3. **Manual Processes**: Thanh toán thủ công, không tích hợp payment gateway
4. **Async Communication**: Email notification không realtime, sử dụng queue
5. **Hybrid AI**: Kết hợp Gemini API cho câu hỏi phức tạp và rule-based cho câu hỏi đơn giản

## Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Web Browser                          │
│                  (Bootstrap 5 + jQuery)                     │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTP/HTTPS
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Application                      │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                   Routes Layer                        │  │
│  │         (web.php, api.php for AI chat)              │  │
│  └────────────────────┬─────────────────────────────────┘  │
│                       │                                      │
│  ┌────────────────────▼─────────────────────────────────┐  │
│  │              Controllers Layer                        │  │
│  │  - CourseController    - EnrollmentController        │  │
│  │  - ClassController     - AttendanceController        │  │
│  │  - TeacherController   - AssessmentController        │  │
│  │  - StudentController   - PaymentController           │  │
│  │  - ScheduleController  - CertificateController       │  │
│  │  - VirtualAssistantController                        │  │
│  └────────────────────┬─────────────────────────────────┘  │
│                       │                                      │
│  ┌────────────────────▼─────────────────────────────────┐  │
│  │              Services Layer                           │  │
│  │  - CourseService       - NotificationService         │  │
│  │  - EnrollmentService   - ReportService               │  │
│  │  - PaymentService      - CertificateService          │  │
│  │  - AIAssistantService (Gemini + Rule-based)         │  │
│  └────────────────────┬─────────────────────────────────┘  │
│                       │                                      │
│  ┌────────────────────▼─────────────────────────────────┐  │
│  │              Models Layer (Eloquent ORM)             │  │
│  │  - User (Student/Teacher/Admin)  - Enrollment        │  │
│  │  - Course                        - Attendance        │  │
│  │  - ClassModel                    - Assessment        │  │
│  │  - Schedule                      - Payment           │  │
│  │  - Certificate                   - Conversation      │  │
│  │  - Feedback                      - Notification      │  │
│  └────────────────────┬─────────────────────────────────┘  │
│                       │                                      │
└───────────────────────┼──────────────────────────────────────┘
                        │
        ┌───────────────┼───────────────┐
        │               │               │
        ▼               ▼               ▼
┌──────────────┐ ┌─────────────┐ ┌──────────────┐
│    MySQL     │ │   Queue     │ │ Google       │
│   Database   │ │  (Jobs)     │ │ Gemini API   │
└──────────────┘ └─────────────┘ └──────────────┘
```

### MVC Flow

1. **Request Flow**: Browser → Routes → Controller → Service → Model → Database
2. **Response Flow**: Database → Model → Service → Controller → View (Blade) → Browser
3. **AI Chat Flow**: Browser (AJAX) → API Route → VirtualAssistantController → AIAssistantService → Gemini API/Rule Engine → JSON Response

### Layer Responsibilities

**Controllers**: 
- Xử lý HTTP requests/responses
- Validation input
- Gọi services
- Trả về views hoặc JSON

**Services**:
- Business logic
- Orchestrate multiple models
- External API calls (Gemini)
- Complex calculations

**Models**:
- Database interactions (Eloquent ORM)
- Relationships
- Accessors/Mutators
- Query scopes

**Views (Blade)**:
- Presentation logic
- HTML rendering
- Bootstrap components
- jQuery interactions

## Components and Interfaces

### Core Components

#### 1. Authentication & Authorization Component

**Purpose**: Quản lý đăng nhập, phân quyền theo role (Admin, Teacher, Student)

**Implementation**:
- Laravel Breeze cho authentication scaffold
- Middleware: `auth`, `role:admin`, `role:teacher`, `role:student`
- User model với role field (enum: admin, teacher, student)

```php
// Middleware example
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('courses', CourseController::class);
});
```

#### 2. Course Management Component

**Controllers**: `CourseController`, `ClassController`

**Services**: `CourseService`

**Models**: `Course`, `ClassModel`

**Key Methods**:
- `CourseController@index`: Hiển thị danh sách khóa học
- `CourseController@store`: Tạo khóa học mới (Admin only)
- `CourseController@update`: Cập nhật thông tin khóa học
- `ClassController@store`: Tạo lớp học cho khóa học
- `CourseService@validateClassCapacity`: Kiểm tra sức chứa lớp

#### 3. Enrollment Component

**Controllers**: `EnrollmentController`

**Services**: `EnrollmentService`

**Models**: `Enrollment`, `Payment`

**Key Methods**:
- `EnrollmentController@store`: Đăng ký lớp học
- `EnrollmentService@checkCapacity`: Kiểm tra còn chỗ trống
- `EnrollmentService@createPaymentRequest`: Tạo yêu cầu thanh toán
- `EnrollmentService@sendConfirmation`: Gửi email xác nhận

**Flow**:
1. Student chọn lớp học
2. System kiểm tra capacity
3. Tạo enrollment record (status: pending)
4. Tạo payment request
5. Gửi email confirmation
6. Khi payment completed → update enrollment status

#### 4. Schedule & Attendance Component

**Controllers**: `ScheduleController`, `AttendanceController`

**Services**: `ScheduleService`, `AttendanceService`

**Models**: `Schedule`, `Attendance`

**Key Methods**:
- `ScheduleController@store`: Tạo lịch học (Teacher)
- `ScheduleService@notifyStudents`: Gửi thông báo lịch học
- `AttendanceController@store`: Lưu điểm danh
- `AttendanceService@calculateRate`: Tính tỷ lệ tham gia

#### 5. Assessment Component

**Controllers**: `AssessmentController`

**Services**: `AssessmentService`

**Models**: `Assessment`, `AssessmentScore`

**Key Methods**:
- `AssessmentController@store`: Tạo bài đánh giá
- `AssessmentController@updateScores`: Nhập điểm
- `AssessmentService@validateScore`: Kiểm tra điểm hợp lệ
- `AssessmentService@notifyStudents`: Thông báo điểm

#### 6. Virtual Assistant Component

**Controllers**: `VirtualAssistantController`

**Services**: `AIAssistantService`

**Models**: `Conversation`, `Message`

**Architecture**:
```
VirtualAssistantController
    ↓
AIAssistantService
    ↓
┌─────────────┴──────────────┐
│                            │
RuleBasedEngine         GeminiAPIClient
(Simple queries)        (Complex queries)
```

**Rule-Based Engine**: Xử lý các câu hỏi đơn giản
- "Khóa học nào phù hợp với tôi?" → Query database based on student level
- "Lịch học hôm nay?" → Query schedules
- "Điểm của tôi?" → Query assessments

**Gemini API Client**: Xử lý câu hỏi phức tạp
- Câu hỏi học tập: "Giải thích thì quá khứ đơn"
- Tạo bài tập: "Cho tôi 5 câu luyện tập"
- Conversation context: Lưu 10 messages gần nhất

**Key Methods**:
- `AIAssistantService@processMessage($userId, $message)`: Entry point
- `RuleBasedEngine@match($message)`: Pattern matching
- `GeminiAPIClient@sendRequest($prompt, $context)`: Call Gemini API
- `ConversationService@saveMessage()`: Lưu lịch sử chat

**API Integration**:
```php
// Gemini API call example
$client = new \GuzzleHttp\Client();
$response = $client->post('https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent', [
    'headers' => [
        'Content-Type' => 'application/json',
        'x-goog-api-key' => env('GEMINI_API_KEY')
    ],
    'json' => [
        'contents' => [
            ['parts' => [['text' => $prompt]]]
        ]
    ]
]);
```

#### 7. Payment Component

**Controllers**: `PaymentController`

**Services**: `PaymentService`

**Models**: `Payment`

**Implementation**: Manual payment processing
- Admin marks payment as completed manually
- Student uploads payment proof (optional)
- Email receipt sent after confirmation

**Key Methods**:
- `PaymentController@show`: Hiển thị thông tin thanh toán
- `PaymentController@uploadProof`: Upload chứng từ
- `PaymentService@confirmPayment`: Admin xác nhận (manual)
- `PaymentService@sendReceipt`: Gửi hóa đơn email

#### 8. Certificate Component

**Controllers**: `CertificateController`

**Services**: `CertificateService`

**Models**: `Certificate`

**Implementation**: Simple PDF generation (no QR code)
- DomPDF for PDF generation
- Unique certificate number (UUID)
- Public verification page

**Key Methods**:
- `CertificateService@generate($studentId, $courseId)`: Tạo certificate
- `CertificateService@generatePDF($certificate)`: Render PDF
- `CertificateController@verify($certificateNumber)`: Public verification

#### 9. Notification Component

**Services**: `NotificationService`

**Models**: `Notification`

**Implementation**: Email-based (no realtime)
- Laravel Queue for async sending
- Database notifications for in-app display
- Email templates with Blade

**Key Methods**:
- `NotificationService@send($user, $type, $data)`: Send notification
- `NotificationService@sendEmail($user, $template, $data)`: Queue email
- `NotificationService@createInApp($user, $message)`: In-app notification

**Notification Types**:
- Enrollment confirmation
- Schedule created/updated
- Payment reminder
- Assessment scores posted
- Certificate ready

#### 10. Reporting Component

**Controllers**: `ReportController`

**Services**: `ReportService`

**Key Methods**:
- `ReportService@getStudentProgress($studentId)`: Progress report
- `ReportService@getTeacherPerformance($teacherId)`: Teacher metrics
- `ReportService@getRevenueReport($month)`: Revenue analysis
- `ReportService@exportPDF($reportData)`: Export report

### External Interfaces

#### Google Gemini API

**Endpoint**: `https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent`

**Authentication**: API Key (free tier)

**Request Format**:
```json
{
  "contents": [
    {
      "parts": [
        {"text": "Your prompt here"}
      ]
    }
  ]
}
```

**Response Format**:
```json
{
  "candidates": [
    {
      "content": {
        "parts": [
          {"text": "AI response here"}
        ]
      }
    }
  ]
}
```

**Rate Limits**: 60 requests/minute (free tier)

**Error Handling**:
- Timeout: Fallback to rule-based response
- Rate limit: Queue request or show error
- Invalid API key: Log error, show maintenance message

## Data Models

### Database Schema

#### users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_email (email)
);
```

#### teachers
```sql
CREATE TABLE teachers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    specialization VARCHAR(255),
    qualifications TEXT,
    bio TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);
```

#### students
```sql
CREATE TABLE students (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    level ENUM('beginner', 'elementary', 'intermediate', 'advanced') DEFAULT 'beginner',
    interests TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);
```

#### courses
```sql
CREATE TABLE courses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    language VARCHAR(100) NOT NULL,
    level ENUM('beginner', 'elementary', 'intermediate', 'advanced') NOT NULL,
    duration_weeks INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_language (language),
    INDEX idx_level (level),
    INDEX idx_is_active (is_active)
);
```

#### classes
```sql
CREATE TABLE classes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    course_id BIGINT UNSIGNED NOT NULL,
    teacher_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    max_capacity INT NOT NULL,
    current_enrollment INT DEFAULT 0,
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE RESTRICT,
    INDEX idx_course_id (course_id),
    INDEX idx_teacher_id (teacher_id),
    INDEX idx_status (status),
    CHECK (start_date < end_date),
    CHECK (max_capacity > 0)
);
```

#### enrollments
```sql
CREATE TABLE enrollments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    class_id BIGINT UNSIGNED NOT NULL,
    enrollment_date DATE NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    completion_percentage DECIMAL(5, 2) DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, class_id),
    INDEX idx_student_id (student_id),
    INDEX idx_class_id (class_id),
    INDEX idx_status (status)
);
```

#### schedules
```sql
CREATE TABLE schedules (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    class_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    location VARCHAR(255),
    topic VARCHAR(255),
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    INDEX idx_class_id (class_id),
    INDEX idx_date (date),
    INDEX idx_status (status),
    CHECK (start_time < end_time)
);
```

#### attendances
```sql
CREATE TABLE attendances (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    schedule_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    status ENUM('present', 'absent', 'late') NOT NULL,
    note TEXT,
    recorded_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (schedule_id, student_id),
    INDEX idx_schedule_id (schedule_id),
    INDEX idx_student_id (student_id)
);
```

#### assessments
```sql
CREATE TABLE assessments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    class_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('quiz', 'test', 'midterm', 'final', 'assignment') NOT NULL,
    max_score DECIMAL(5, 2) NOT NULL,
    assessment_date DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    INDEX idx_class_id (class_id),
    INDEX idx_type (type)
);
```

#### assessment_scores
```sql
CREATE TABLE assessment_scores (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    assessment_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(5, 2) NOT NULL,
    feedback TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_score (assessment_id, student_id),
    INDEX idx_assessment_id (assessment_id),
    INDEX idx_student_id (student_id)
);
```

#### payments
```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    enrollment_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'credit_card', 'e_wallet'),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    due_date DATE NOT NULL,
    paid_date DATE,
    proof_image VARCHAR(255),
    note TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
    INDEX idx_enrollment_id (enrollment_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date)
);
```

#### certificates
```sql
CREATE TABLE certificates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    certificate_number VARCHAR(50) UNIQUE NOT NULL,
    issue_date DATE NOT NULL,
    pdf_path VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_certificate_number (certificate_number),
    INDEX idx_student_id (student_id)
);
```

#### conversations
```sql
CREATE TABLE conversations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    started_at TIMESTAMP NOT NULL,
    last_message_at TIMESTAMP NOT NULL,
    message_count INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id)
);
```

#### messages
```sql
CREATE TABLE messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    conversation_id BIGINT UNSIGNED NOT NULL,
    sender_type ENUM('student', 'assistant') NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    INDEX idx_conversation_id (conversation_id),
    INDEX idx_created_at (created_at)
);
```

#### notifications
```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    sent_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
);
```

#### feedbacks
```sql
CREATE TABLE feedbacks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    class_id BIGINT UNSIGNED NOT NULL,
    course_rating INT NOT NULL,
    teacher_rating INT NOT NULL,
    comment TEXT,
    is_anonymous BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    INDEX idx_class_id (class_id),
    CHECK (course_rating BETWEEN 1 AND 5),
    CHECK (teacher_rating BETWEEN 1 AND 5)
);
```

### Eloquent Model Relationships

#### User Model
```php
class User extends Authenticatable
{
    public function teacher() {
        return $this->hasOne(Teacher::class);
    }
    
    public function student() {
        return $this->hasOne(Student::class);
    }
    
    public function notifications() {
        return $this->hasMany(Notification::class);
    }
}
```

#### Course Model
```php
class Course extends Model
{
    public function classes() {
        return $this->hasMany(ClassModel::class);
    }
    
    public function certificates() {
        return $this->hasMany(Certificate::class);
    }
}
```

#### ClassModel
```php
class ClassModel extends Model
{
    public function course() {
        return $this->belongsTo(Course::class);
    }
    
    public function teacher() {
        return $this->belongsTo(Teacher::class);
    }
    
    public function enrollments() {
        return $this->hasMany(Enrollment::class, 'class_id');
    }
    
    public function schedules() {
        return $this->hasMany(Schedule::class, 'class_id');
    }
    
    public function assessments() {
        return $this->hasMany(Assessment::class, 'class_id');
    }
    
    public function students() {
        return $this->belongsToMany(Student::class, 'enrollments', 'class_id', 'student_id');
    }
}
```

#### Enrollment Model
```php
class Enrollment extends Model
{
    public function student() {
        return $this->belongsTo(Student::class);
    }
    
    public function class() {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
    
    public function payment() {
        return $this->hasOne(Payment::class);
    }
}
```

### Data Validation Rules

#### Course Creation
- name: required, string, max:255
- description: nullable, string
- language: required, string, max:100
- level: required, in:beginner,elementary,intermediate,advanced
- duration_weeks: required, integer, min:1
- price: required, numeric, min:0

#### Class Creation
- course_id: required, exists:courses,id
- teacher_id: required, exists:teachers,id
- start_date: required, date, after:today
- end_date: required, date, after:start_date
- max_capacity: required, integer, min:1

#### Enrollment
- student_id: required, exists:students,id
- class_id: required, exists:classes,id
- Unique constraint: (student_id, class_id)
- Capacity check: current_enrollment < max_capacity

#### Assessment Score
- score: required, numeric, min:0, max:{assessment.max_score}


## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

After analyzing all acceptance criteria, I identified several areas where properties can be consolidated:

**Consolidation 1**: CRUD operations (Create, Update, Deactivate) for Courses, Teachers, and Classes follow the same pattern. Instead of separate properties for each entity, we can create general CRUD properties that apply to all entities.

**Consolidation 2**: Notification side effects (enrollment confirmation, schedule updates, score posting) all follow the same pattern: "when X happens, notify Y". These can be combined into a single property about notification creation.

**Consolidation 3**: Validation rules (start_date < end_date, start_time < end_time, score <= max_score) are all comparison validations that can be generalized.

**Consolidation 4**: Data retrieval properties (view enrollments, view scores, view schedules) all test the same concept: users can retrieve their associated data.

**Eliminated Properties**:
- Performance requirements (response time < 3 seconds, within 1 second) - these are better tested in integration/load tests
- UI-specific tests (display interface, visualization, prompting) - these require UI testing frameworks
- Time-based scheduled tasks (reminders 24 hours before, 3 days before) - these require time-mocking and are better as integration tests
- Real-time updates - requires websocket testing
- AI response quality - cannot be tested programmatically
- Voice input features - removed from requirements

### Property 1: Course CRUD Operations Preserve Data Integrity

*For any* valid course data (name, description, language, level, duration_weeks, price), when an administrator creates a course, the stored course should contain exactly the same data that was provided.

**Validates: Requirements 1.1**

### Property 2: Course Update Preserves Identity

*For any* existing course and any valid update data, when an administrator updates the course, the course ID should remain unchanged and all updated fields should reflect the new values.

**Validates: Requirements 1.2**

### Property 3: Course Deactivation Changes Status Only

*For any* active course, when an administrator deactivates it, the course should have is_active = false and all other fields should remain unchanged.

**Validates: Requirements 1.3**

### Property 4: Required Field Validation Rejects Incomplete Data

*For any* course data missing required fields (name, language, level, duration_weeks, or price), the system should reject the creation/update request and return a validation error.

**Validates: Requirements 1.4**

### Property 5: Active Course Filter Excludes Inactive Courses

*For any* set of courses with mixed active/inactive status, when students query available courses, the result should contain only courses where is_active = true.

**Validates: Requirements 1.5**

### Property 6: Class Creation Validates Date Ordering

*For any* class data where start_date >= end_date, the system should reject the creation request and return a validation error.

**Validates: Requirements 2.3**

### Property 7: Class Creation Validates Positive Capacity

*For any* class data where max_capacity <= 0, the system should reject the creation request and return a validation error.

**Validates: Requirements 2.4**

### Property 8: Class Creation Requires Teacher Assignment

*For any* class data without a teacher_id, the system should reject the creation request and return a validation error.

**Validates: Requirements 2.5**

### Property 9: Class Details Include All Required Information

*For any* class, when a student views class details, the response should include schedule information, teacher information, and available slots (max_capacity - current_enrollment).

**Validates: Requirements 3.1**

### Property 10: Enrollment Creation Respects Capacity Limits

*For any* class with available capacity (current_enrollment < max_capacity) and any student not already enrolled, when the student requests enrollment, the system should create an enrollment record and increment current_enrollment by 1.

**Validates: Requirements 3.2**

### Property 11: Enrollment Rejection at Maximum Capacity

*For any* class where current_enrollment >= max_capacity, when a student requests enrollment, the system should reject the request and return an error message.

**Validates: Requirements 3.3** (edge case)

### Property 12: Enrollment Creation Triggers Notification

*For any* successful enrollment creation, the system should create a notification record for the enrolled student with type 'enrollment_confirmation'.

**Validates: Requirements 3.4**

### Property 13: Schedule Creation Validates Time Ordering

*For any* schedule data where start_time >= end_time, the system should reject the creation request and return a validation error.

**Validates: Requirements 4.2**

### Property 14: Schedule Creation Notifies All Enrolled Students

*For any* class with N enrolled students, when a teacher creates a schedule for that class, the system should create N notification records (one for each enrolled student).

**Validates: Requirements 4.3**

### Property 15: Attendance Record Includes Timestamp

*For any* attendance record created, the record should have a recorded_at timestamp that is set to the current time when the record is saved.

**Validates: Requirements 5.3**

### Property 16: Attendance Rate Calculation Accuracy

*For any* student in a class with M total schedules and P present attendances, the attendance rate should equal (P / M) * 100.

**Validates: Requirements 5.5**

### Property 17: Assessment Score Validation Against Maximum

*For any* assessment with max_score = X, when a teacher enters a score > X for any student, the system should reject the score and return a validation error.

**Validates: Requirements 6.3**

### Property 18: Score Posting Triggers Student Notifications

*For any* assessment with N students, when a teacher saves scores for that assessment, the system should create N notification records (one for each student).

**Validates: Requirements 6.4**

### Property 19: Course Recommendation Matches Student Level

*For any* student with level L, when the virtual assistant provides course recommendations, all recommended courses should have level = L or level = (L - 1) for review purposes.

**Validates: Requirements 7.3**

### Property 20: Schedule Query Returns Accurate Data

*For any* student enrolled in classes with schedules, when the virtual assistant is asked about schedules, the returned schedule information should match the actual schedule records in the database.

**Validates: Requirements 7.4**

### Property 21: Conversation Context Maintains Message History

*For any* conversation with N messages (N >= 10), when retrieving conversation context, the system should return at least the most recent 10 messages in chronological order.

**Validates: Requirements 7.5**

### Property 22: Exercise Generation Matches Student Level

*For any* student enrolled in a course with level L, when the virtual assistant generates practice exercises, the exercises should be appropriate for level L.

**Validates: Requirements 8.2**

### Property 23: Virtual Assistant Interactions Are Logged

*For any* message sent to the virtual assistant, the system should create a message record in the database with sender_type, content, and timestamp.

**Validates: Requirements 8.5**

### Property 24: Enrollment Creates Payment Request

*For any* successful enrollment with class linked to course with price P, the system should create a payment record with amount = P and due_date = enrollment_date + 7 days.

**Validates: Requirements 9.1**

### Property 25: Payment Method Storage

*For any* payment with method M where M is in ['cash', 'bank_transfer', 'credit_card', 'e_wallet'], the system should store the payment with payment_method = M.

**Validates: Requirements 9.2**

### Property 26: Payment Completion Updates Enrollment Status

*For any* payment with status 'pending' linked to enrollment with status 'pending', when the payment status changes to 'completed', the enrollment status should change to 'paid'.

**Validates: Requirements 9.3**

### Property 27: Payment Completion Triggers Receipt Notification

*For any* payment that changes status to 'completed', the system should create a notification record for the student with type 'payment_receipt'.

**Validates: Requirements 9.4**

### Property 28: Progress Report Contains Required Metrics

*For any* student enrolled in a class, when generating a progress report, the report should include attendance_rate, assessment_scores (list), and completion_percentage.

**Validates: Requirements 10.1**

### Property 29: Progress Report Updates With New Data

*For any* student with existing progress data, when new attendance or assessment data is recorded, the next progress report query should reflect the updated values.

**Validates: Requirements 10.2**

### Property 30: Certificate Eligibility Notification

*For any* student with completion_percentage >= 80 and average assessment score >= passing_score, the system should create a notification with type 'certificate_eligible'.

**Validates: Requirements 10.5**

### Property 31: Certificate Generation Includes Required Fields

*For any* student completing a course, when a certificate is generated, the certificate should include student_name, course_name, issue_date, and a unique certificate_number.

**Validates: Requirements 11.1**

### Property 32: Certificate PDF Generation

*For any* generated certificate, the system should create a PDF file and store the file path in the certificate record's pdf_path field.

**Validates: Requirements 11.2**

### Property 33: Certificate Persistence

*For any* generated certificate, the certificate record should be stored in the database and retrievable by certificate_id.

**Validates: Requirements 11.3**

### Property 34: Certificate Verification by Number

*For any* certificate with certificate_number = N, querying the public verification endpoint with N should return the certificate details (student_name, course_name, issue_date).

**Validates: Requirements 11.4**

### Property 35: Teacher Deactivation Prevents Class Assignment

*For any* teacher with is_active = false, when an administrator attempts to create a class with that teacher_id, the system should reject the request and return a validation error.

**Validates: Requirements 12.5**

### Property 36: Notification Multi-Channel Delivery

*For any* notification created, the system should create both an in-app notification record (in notifications table) and queue an email job for delivery.

**Validates: Requirements 13.5**

### Property 37: Course Search Returns Matching Results

*For any* search query Q, when a student searches courses, all returned courses should have Q as a substring in either the name or description field (case-insensitive).

**Validates: Requirements 14.1**

### Property 38: Course Filter Applies All Criteria

*For any* filter with language = L, level = V, and price_range = [min, max], all returned courses should satisfy: language = L AND level = V AND price >= min AND price <= max.

**Validates: Requirements 14.2**

### Property 39: Course Search Results Include Class Count

*For any* course in search results, the response should include a field showing the count of active classes for that course.

**Validates: Requirements 14.5**

### Property 40: Admin Report Calculations Accuracy

*For any* month M, the admin report should show: total_students = count of students created up to M, active_courses = count of courses with is_active = true, revenue = sum of payments with status 'completed' in month M.

**Validates: Requirements 15.1**

### Property 41: Teacher Performance Metrics Calculation

*For any* teacher, the performance metrics should include: average_rating = average of teacher_rating from feedbacks, completion_rate = (completed_classes / total_classes) * 100.

**Validates: Requirements 15.2**

### Property 42: Course Popularity Ranking

*For any* set of courses, when ordered by popularity, courses should be sorted in descending order by the count of enrollments for that course.

**Validates: Requirements 15.3**

### Property 43: Report Export Format Validation

*For any* report export request with format F where F is in ['pdf', 'excel'], the system should generate a file in format F and return the file path.

**Validates: Requirements 15.4**

### Property 44: Feedback Rating Range Validation

*For any* feedback submission with course_rating = C or teacher_rating = T, if C < 1 or C > 5 or T < 1 or T > 5, the system should reject the submission and return a validation error.

**Validates: Requirements 16.2**

### Property 45: Anonymous Feedback Flag Storage

*For any* feedback submitted with is_anonymous = true, the feedback record should be stored with is_anonymous = true and should not expose student identity in public views.

**Validates: Requirements 16.3**

### Property 46: Average Rating Calculation

*For any* course with N feedbacks, the average course rating should equal (sum of all course_rating values) / N. Same applies for teacher ratings.

**Validates: Requirements 16.4**

### Property 47: Conversation Parsing Produces Structured Objects

*For any* conversation text, when parsed, the result should be a structured object with fields: conversation_id, messages (array of {sender_type, content, timestamp}).

**Validates: Requirements 17.1**

### Property 48: Conversation Serialization Round-Trip

*For any* valid conversation object, serializing to JSON then deserializing should produce an equivalent object with the same conversation_id, message count, and message contents.

**Validates: Requirements 17.2, 17.4**

### Property 49: Conversation Formatter Produces Readable Text

*For any* conversation object with N messages, when formatted, the output should be a string containing all N message contents in chronological order with sender labels.

**Validates: Requirements 17.3**

### Property 50: Invalid Conversation Data Returns Error

*For any* invalid conversation data (missing required fields, invalid JSON, wrong data types), when parsing, the system should return an error object with a descriptive error message.

**Validates: Requirements 17.5**

## Error Handling

### Validation Errors

**Strategy**: Laravel Form Request Validation

**Implementation**:
- Create custom FormRequest classes for each entity (StoreCourseRequest, UpdateClassRequest, etc.)
- Return 422 Unprocessable Entity with validation error messages
- Display errors in Blade views using `@error` directive

**Example**:
```php
class StoreCourseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'level' => 'required|in:beginner,elementary,intermediate,advanced',
            'duration_weeks' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ];
    }
    
    public function messages()
    {
        return [
            'name.required' => 'Tên khóa học là bắt buộc',
            'price.min' => 'Giá phải lớn hơn hoặc bằng 0',
        ];
    }
}
```

### Database Errors

**Strategy**: Try-Catch with Transaction Rollback

**Implementation**:
- Wrap database operations in DB::transaction()
- Catch QueryException and return user-friendly messages
- Log detailed errors for debugging

**Example**:
```php
try {
    DB::transaction(function () use ($data) {
        $enrollment = Enrollment::create($data);
        $payment = Payment::create(['enrollment_id' => $enrollment->id]);
    });
} catch (QueryException $e) {
    Log::error('Enrollment creation failed: ' . $e->getMessage());
    return back()->with('error', 'Không thể tạo đăng ký. Vui lòng thử lại.');
}
```

### External API Errors (Gemini)

**Strategy**: Timeout, Retry, Fallback

**Implementation**:
- Set timeout: 5 seconds for Gemini API calls
- Retry: 2 attempts with exponential backoff
- Fallback: Use rule-based response if Gemini fails
- Log all API errors

**Example**:
```php
try {
    $response = $this->geminiClient->sendRequest($prompt, $timeout = 5);
} catch (RequestException $e) {
    Log::warning('Gemini API failed: ' . $e->getMessage());
    // Fallback to rule-based response
    return $this->ruleBasedEngine->getResponse($message);
}
```

### Business Logic Errors

**Strategy**: Custom Exceptions with Specific Messages

**Implementation**:
- Create custom exceptions: EnrollmentCapacityException, PaymentRequiredException, etc.
- Throw exceptions in service layer
- Catch in controller and return appropriate responses

**Example**:
```php
// Service
if ($class->current_enrollment >= $class->max_capacity) {
    throw new EnrollmentCapacityException('Lớp học đã đầy');
}

// Controller
try {
    $this->enrollmentService->enroll($studentId, $classId);
} catch (EnrollmentCapacityException $e) {
    return back()->with('error', $e->getMessage());
}
```

### File Operation Errors

**Strategy**: Check Permissions and Disk Space

**Implementation**:
- Validate file uploads (size, type)
- Check storage permissions before writing
- Handle disk full errors gracefully

**Example**:
```php
if (!Storage::exists('certificates')) {
    Storage::makeDirectory('certificates');
}

try {
    $pdf->save(storage_path('app/certificates/' . $filename));
} catch (Exception $e) {
    Log::error('Certificate PDF save failed: ' . $e->getMessage());
    throw new CertificateGenerationException('Không thể tạo chứng chỉ');
}
```

### Authentication & Authorization Errors

**Strategy**: Middleware with Redirect

**Implementation**:
- Use Laravel auth middleware
- Custom role middleware for authorization
- Redirect to login or show 403 Forbidden

**Example**:
```php
// Middleware
if (!auth()->check()) {
    return redirect()->route('login');
}

if (!auth()->user()->hasRole('admin')) {
    abort(403, 'Bạn không có quyền truy cập');
}
```

### Queue Job Errors

**Strategy**: Retry with Exponential Backoff

**Implementation**:
- Set max attempts: 3
- Use exponential backoff: 1s, 2s, 4s
- Log failed jobs to database
- Admin dashboard to view failed jobs

**Example**:
```php
class SendEmailNotification implements ShouldQueue
{
    public $tries = 3;
    public $backoff = [1, 2, 4];
    
    public function failed(Exception $exception)
    {
        Log::error('Email notification failed: ' . $exception->getMessage());
    }
}
```

## Testing Strategy

### Dual Testing Approach

This system requires both unit testing and property-based testing to ensure comprehensive coverage:

**Unit Tests**: Focus on specific examples, edge cases, and integration points
- Specific enrollment scenarios (first student, last available slot)
- Edge cases (empty course list, zero capacity class)
- Error conditions (invalid dates, missing required fields)
- Integration between components (enrollment → payment → notification)

**Property-Based Tests**: Verify universal properties across all inputs
- Generate random valid data (courses, students, enrollments)
- Test properties hold for all generated inputs
- Catch edge cases that manual tests might miss
- Ensure business rules are consistently enforced

Together, unit tests catch concrete bugs while property tests verify general correctness.

### Property-Based Testing Configuration

**Library**: Laravel + PestPHP with Pest Property Plugin

**Installation**:
```bash
composer require pestphp/pest --dev
composer require pestphp/pest-plugin-faker --dev
```

**Configuration**:
- Minimum 100 iterations per property test
- Each test references its design document property
- Tag format: `Feature: language-center-management-system, Property {number}: {property_text}`

**Example Property Test**:
```php
use function Pest\Faker\fake;

it('validates course creation preserves data integrity', function () {
    // Feature: language-center-management-system, Property 1: Course CRUD Operations Preserve Data Integrity
    
    $courseData = [
        'name' => fake()->sentence(3),
        'description' => fake()->paragraph(),
        'language' => fake()->randomElement(['English', 'Japanese', 'Korean']),
        'level' => fake()->randomElement(['beginner', 'elementary', 'intermediate', 'advanced']),
        'duration_weeks' => fake()->numberBetween(4, 52),
        'price' => fake()->randomFloat(2, 1000000, 10000000),
    ];
    
    $course = Course::create($courseData);
    
    expect($course->name)->toBe($courseData['name'])
        ->and($course->description)->toBe($courseData['description'])
        ->and($course->language)->toBe($courseData['language'])
        ->and($course->level)->toBe($courseData['level'])
        ->and($course->duration_weeks)->toBe($courseData['duration_weeks'])
        ->and($course->price)->toBe($courseData['price']);
})->repeat(100);
```

### Unit Testing Strategy

**Framework**: PestPHP (Laravel default)

**Test Organization**:
```
tests/
├── Unit/
│   ├── Models/
│   │   ├── CourseTest.php
│   │   ├── EnrollmentTest.php
│   │   └── ...
│   ├── Services/
│   │   ├── EnrollmentServiceTest.php
│   │   ├── AIAssistantServiceTest.php
│   │   └── ...
│   └── Helpers/
│       └── ValidationTest.php
├── Feature/
│   ├── CourseManagementTest.php
│   ├── EnrollmentFlowTest.php
│   ├── VirtualAssistantTest.php
│   └── ...
└── Property/
    ├── CoursePropertiesTest.php
    ├── EnrollmentPropertiesTest.php
    └── ...
```

**Coverage Goals**:
- Unit tests: 80% code coverage
- Property tests: All 50 properties implemented
- Feature tests: All critical user flows

**Example Unit Test**:
```php
it('rejects enrollment when class is at capacity', function () {
    $class = ClassModel::factory()->create(['max_capacity' => 2, 'current_enrollment' => 2]);
    $student = Student::factory()->create();
    
    expect(fn() => app(EnrollmentService::class)->enroll($student->id, $class->id))
        ->toThrow(EnrollmentCapacityException::class, 'Lớp học đã đầy');
});
```

### Integration Testing

**Focus Areas**:
- Enrollment flow: Student selects class → Enrollment created → Payment generated → Email sent
- Schedule notification: Teacher creates schedule → All enrolled students notified
- Certificate generation: Student completes course → Certificate generated → PDF created → Verification available
- Virtual assistant: Student asks question → Rule engine or Gemini processes → Response saved → Context maintained

**Example Integration Test**:
```php
it('completes full enrollment flow', function () {
    $student = Student::factory()->create();
    $class = ClassModel::factory()->create(['max_capacity' => 10, 'current_enrollment' => 5]);
    
    // Act
    $enrollment = app(EnrollmentService::class)->enroll($student->id, $class->id);
    
    // Assert
    expect($enrollment->status)->toBe('pending')
        ->and($class->fresh()->current_enrollment)->toBe(6)
        ->and(Payment::where('enrollment_id', $enrollment->id)->exists())->toBeTrue()
        ->and(Notification::where('user_id', $student->user_id)->where('type', 'enrollment_confirmation')->exists())->toBeTrue();
});
```

### Mocking External Services

**Gemini API Mock**:
```php
// In tests
Http::fake([
    'generativelanguage.googleapis.com/*' => Http::response([
        'candidates' => [
            ['content' => ['parts' => [['text' => 'Mocked AI response']]]]
        ]
    ], 200)
]);
```

**Email Mock**:
```php
Mail::fake();

// After action
Mail::assertSent(EnrollmentConfirmation::class, function ($mail) use ($student) {
    return $mail->hasTo($student->user->email);
});
```

### Performance Testing

**Tools**: Laravel Telescope + Apache Bench

**Metrics**:
- Page load time: < 500ms for course listing
- API response time: < 200ms for virtual assistant (rule-based), < 3s (Gemini)
- Database query count: < 10 queries per page
- N+1 query detection: Use Telescope to identify and fix

**Example**:
```bash
# Test course listing endpoint
ab -n 1000 -c 10 http://localhost/courses
```

### Test Data Management

**Strategy**: Factory + Seeder

**Implementation**:
- Use Laravel factories for all models
- Create realistic test data with Faker
- Seed database for manual testing
- Reset database between tests

**Example Factory**:
```php
class CourseFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'language' => $this->faker->randomElement(['English', 'Japanese', 'Korean', 'Chinese']),
            'level' => $this->faker->randomElement(['beginner', 'elementary', 'intermediate', 'advanced']),
            'duration_weeks' => $this->faker->numberBetween(4, 52),
            'price' => $this->faker->randomFloat(2, 1000000, 10000000),
            'is_active' => true,
        ];
    }
}
```

### Continuous Integration

**Platform**: GitHub Actions

**Pipeline**:
1. Install dependencies (composer install)
2. Copy .env.testing
3. Generate application key
4. Run migrations
5. Run unit tests
6. Run property tests (100 iterations each)
7. Run feature tests
8. Generate coverage report
9. Fail if coverage < 80%

**Example .github/workflows/tests.yml**:
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test --coverage --min=80
```

