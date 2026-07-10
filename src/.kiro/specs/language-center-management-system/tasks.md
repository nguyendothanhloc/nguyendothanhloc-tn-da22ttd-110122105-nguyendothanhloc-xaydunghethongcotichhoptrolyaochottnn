# Implementation Plan: Language Center Management System

## Overview

Hệ thống quản lý khóa học trung tâm ngoại ngữ với trợ lý ảo được xây dựng trên Laravel 11, MySQL 8.0, và tích hợp Google Gemini API. Implementation được chia thành các giai đoạn: setup cơ bản, core features (quản lý khóa học, đăng ký, lịch học), advanced features (trợ lý ảo, thanh toán, chứng chỉ), và testing.

## Tasks

- [x] 1. Setup project structure and core infrastructure
  - Tạo Laravel 11 project với PHP 8.2+
  - Cấu hình MySQL 8.0 database connection
  - Cài đặt Laravel Breeze cho authentication
  - Setup Bootstrap 5 và jQuery trong Blade templates
  - Cấu hình queue driver (database) cho email notifications
  - Cài đặt DomPDF cho PDF generation
  - _Requirements: All (infrastructure foundation)_

- [ ] 2. Implement database schema and models
  - [x] 2.1 Create migrations cho tất cả tables
    - Tạo migrations: users, teachers, students, courses, classes, enrollments, schedules, attendances, assessments, assessment_scores, payments, certificates, conversations, messages, notifications, feedbacks
    - Thêm indexes và foreign keys theo design document
    - Thêm validation constraints (CHECK clauses)
    - _Requirements: 1.1, 2.1, 3.2, 4.1, 5.1, 6.1, 9.1, 11.1, 12.1, 16.1_

  - [x] 2.2 Create Eloquent models với relationships
    - Tạo models: User, Teacher, Student, Course, ClassModel, Enrollment, Schedule, Attendance, Assessment, AssessmentScore, Payment, Certificate, Conversation, Message, Notification, Feedback
    - Define relationships (hasMany, belongsTo, belongsToMany)
    - Thêm accessors/mutators nếu cần
    - _Requirements: All (data layer foundation)_

  - [ ]* 2.3 Write property test for database constraints
    - **Property 6: Class Creation Validates Date Ordering**
    - **Property 7: Class Creation Validates Positive Capacity**
    - **Property 13: Schedule Creation Validates Time Ordering**
    - **Validates: Requirements 2.3, 2.4, 4.2**


- [x] 3. Implement authentication and authorization
  - [x] 3.1 Setup role-based middleware
    - Thêm role field vào User model (enum: admin, teacher, student)
    - Tạo middleware: RoleMiddleware cho role checking
    - Register middleware trong Kernel
    - _Requirements: 1.1, 2.1, 12.1 (authorization foundation)_

  - [x] 3.2 Create registration flows cho từng role
    - Tạo separate registration forms cho Student, Teacher, Admin
    - Tự động tạo Student/Teacher record khi User được tạo
    - _Requirements: 3.1, 12.1_

  - [ ]* 3.3 Write unit tests for authentication
    - Test login, logout, registration flows
    - Test role-based access control
    - _Requirements: All (security foundation)_

- [x] 4. Implement Course Management (Admin)
  - [x] 4.1 Create CourseController và CourseService
    - Implement CRUD operations: index, create, store, edit, update
    - Implement deactivate method (soft delete via is_active flag)
    - Validation: required fields, price >= 0, duration_weeks > 0
    - _Requirements: 1.1, 1.2, 1.3, 1.4_

  - [x] 4.2 Create Blade views cho course management
    - courses/index.blade.php: danh sách khóa học với filter
    - courses/create.blade.php: form tạo khóa học
    - courses/edit.blade.php: form cập nhật khóa học
    - _Requirements: 1.1, 1.2, 1.3_

  - [ ]* 4.3 Write property tests for course operations
    - **Property 1: Course CRUD Operations Preserve Data Integrity**
    - **Property 2: Course Update Preserves Identity**
    - **Property 3: Course Deactivation Changes Status Only**
    - **Property 4: Required Field Validation Rejects Incomplete Data**
    - **Validates: Requirements 1.1, 1.2, 1.3, 1.4**

- [x] 5. Implement Class Management (Admin)
  - [x] 5.1 Create ClassController và related services
    - Implement CRUD operations cho classes
    - Validation: start_date < end_date, max_capacity > 0, teacher_id required
    - Auto-increment current_enrollment khi có enrollment mới
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

  - [x] 5.2 Create Blade views cho class management
    - classes/index.blade.php: danh sách lớp học
    - classes/create.blade.php: form tạo lớp học với dropdown chọn course và teacher
    - classes/show.blade.php: chi tiết lớp học với enrolled students
    - _Requirements: 2.1, 2.2, 3.1_

  - [ ]* 5.3 Write property tests for class validation
    - **Property 8: Class Creation Requires Teacher Assignment**
    - **Validates: Requirements 2.5**

- [ ] 6. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.


- [x] 7. Implement Student Enrollment Flow
  - [x] 7.1 Create EnrollmentController và EnrollmentService
    - Implement enrollment creation với capacity checking
    - Check: current_enrollment < max_capacity trước khi enroll
    - Reject nếu student đã enrolled trong class đó (unique constraint)
    - Increment class.current_enrollment sau khi enroll thành công
    - _Requirements: 3.2, 3.3_

  - [x] 7.2 Create course browsing views cho students
    - courses/browse.blade.php: hiển thị active courses với filter
    - courses/detail.blade.php: chi tiết course với available classes
    - classes/detail.blade.php: chi tiết class với schedule, teacher, available slots
    - _Requirements: 1.5, 3.1, 14.1, 14.2_

  - [x] 7.3 Implement enrollment confirmation notification
    - Tạo NotificationService với method sendEnrollmentConfirmation
    - Tạo in-app notification record
    - Queue email job cho enrollment confirmation
    - _Requirements: 3.4, 13.1_

  - [ ]* 7.4 Write property tests for enrollment
    - **Property 10: Enrollment Creation Respects Capacity Limits**
    - **Property 11: Enrollment Rejection at Maximum Capacity**
    - **Property 12: Enrollment Creation Triggers Notification**
    - **Validates: Requirements 3.2, 3.3, 3.4**

- [ ] 8. Implement Payment Management
  - [ ] 8.1 Create PaymentController và PaymentService
    - Auto-create payment record khi enrollment được tạo
    - Calculate due_date = enrollment_date + 7 days
    - Implement manual payment confirmation (Admin marks as completed)
    - Implement upload payment proof (Student)
    - _Requirements: 9.1, 9.2, 9.3_

  - [ ] 8.2 Implement payment completion flow
    - Khi payment status → 'completed', update enrollment status → 'paid'
    - Send payment receipt notification
    - _Requirements: 9.3, 9.4_

  - [ ] 8.3 Create payment views
    - payments/show.blade.php: thông tin payment với upload proof form
    - payments/index.blade.php (Admin): danh sách payments cần confirm
    - _Requirements: 9.1, 9.2_

  - [ ]* 8.4 Write property tests for payment flow
    - **Property 24: Enrollment Creates Payment Request**
    - **Property 25: Payment Method Storage**
    - **Property 26: Payment Completion Updates Enrollment Status**
    - **Property 27: Payment Completion Triggers Receipt Notification**
    - **Validates: Requirements 9.1, 9.2, 9.3, 9.4**

- [ ] 9. Implement Schedule Management (Teacher)
  - [ ] 9.1 Create ScheduleController và ScheduleService
    - Implement CRUD operations cho schedules
    - Validation: start_time < end_time, date not in past
    - Notify all enrolled students khi schedule created/updated
    - _Requirements: 4.1, 4.2, 4.3, 4.4_

  - [ ] 9.2 Create schedule views
    - schedules/index.blade.php (Teacher): danh sách schedules của teacher
    - schedules/create.blade.php: form tạo schedule
    - schedules/calendar.blade.php (Student): calendar view của student schedules
    - _Requirements: 4.1, 7.4_

  - [ ]* 9.3 Write property tests for schedule operations
    - **Property 14: Schedule Creation Notifies All Enrolled Students**
    - **Validates: Requirements 4.3**


- [ ] 10. Implement Attendance Management (Teacher)
  - [ ] 10.1 Create AttendanceController và AttendanceService
    - Display enrolled students cho scheduled class session
    - Allow teacher mark status: present, absent, late
    - Save attendance với recorded_at timestamp
    - Calculate attendance rate: (present_count / total_schedules) * 100
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

  - [ ] 10.2 Create attendance views
    - attendances/take.blade.php: form điểm danh với student list
    - attendances/history.blade.php: lịch sử điểm danh của class
    - attendances/student.blade.php (Student): xem attendance của bản thân
    - _Requirements: 5.1, 5.4_

  - [ ]* 10.3 Write property tests for attendance
    - **Property 15: Attendance Record Includes Timestamp**
    - **Property 16: Attendance Rate Calculation Accuracy**
    - **Validates: Requirements 5.3, 5.5**

- [ ] 11. Implement Assessment Management (Teacher)
  - [ ] 11.1 Create AssessmentController và AssessmentService
    - Create assessment với name, type, max_score, date
    - Enter scores cho students với validation: score <= max_score
    - Notify students khi scores are posted
    - _Requirements: 6.1, 6.2, 6.3, 6.4_

  - [ ] 11.2 Create assessment views
    - assessments/index.blade.php (Teacher): danh sách assessments
    - assessments/create.blade.php: form tạo assessment
    - assessments/scores.blade.php: form nhập điểm cho students
    - assessments/student.blade.php (Student): xem điểm của bản thân
    - _Requirements: 6.1, 6.2, 6.5_

  - [ ]* 11.3 Write property tests for assessment
    - **Property 17: Assessment Score Validation Against Maximum**
    - **Property 18: Score Posting Triggers Student Notifications**
    - **Validates: Requirements 6.3, 6.4**

- [ ] 12. Checkpoint - Ensure core features work end-to-end
  - Ensure all tests pass, ask the user if questions arise.

- [x] 13. Implement Virtual Assistant - Rule-Based Engine
  - [x] 13.1 Create RuleBasedEngine service
    - Pattern matching cho simple queries
    - "Khóa học nào phù hợp?" → query courses by student level
    - "Lịch học hôm nay?" → query schedules for today
    - "Điểm của tôi?" → query assessment scores
    - Return structured response với data
    - _Requirements: 7.1, 7.3, 7.4_

  - [x] 13.2 Create conversation storage
    - Save all messages to conversations và messages tables
    - Maintain conversation context (last 10 messages)
    - _Requirements: 7.5, 8.5_

  - [ ]* 13.3 Write property tests for rule-based responses
    - **Property 19: Course Recommendation Matches Student Level**
    - **Property 20: Schedule Query Returns Accurate Data**
    - **Property 21: Conversation Context Maintains Message History**
    - **Property 23: Virtual Assistant Interactions Are Logged**
    - **Validates: Requirements 7.3, 7.4, 7.5, 8.5**


- [ ] 14. Implement Virtual Assistant - Gemini API Integration
  - [ ] 14.1 Create GeminiAPIClient service
    - Setup Guzzle HTTP client
    - Implement sendRequest method với timeout 5s
    - Handle API errors với retry logic (2 attempts)
    - Fallback to rule-based nếu Gemini fails
    - _Requirements: 7.2, 8.1, 8.2_

  - [ ] 14.2 Create AIAssistantService orchestrator
    - Entry point: processMessage($userId, $message)
    - Route simple queries → RuleBasedEngine
    - Route complex queries → GeminiAPIClient
    - Pass conversation context (last 10 messages) to Gemini
    - Save response to database
    - _Requirements: 7.1, 7.2, 7.5, 8.1_

  - [ ] 14.3 Create VirtualAssistantController
    - API endpoint: POST /api/chat
    - Accept JSON: {message: string}
    - Return JSON: {response: string, sender: 'assistant'}
    - Handle authentication
    - _Requirements: 7.1, 7.2_

  - [ ] 14.4 Create chat interface (Frontend)
    - chat.blade.php: chat UI với message history
    - jQuery AJAX calls to /api/chat
    - Display typing indicator khi waiting for response
    - Auto-scroll to latest message
    - _Requirements: 7.1, 8.1_

  - [ ]* 14.5 Write property tests for AI assistant
    - **Property 22: Exercise Generation Matches Student Level**
    - **Validates: Requirements 8.2**

  - [ ]* 14.6 Write integration tests for chat flow
    - Test rule-based routing
    - Test Gemini API integration (with mocking)
    - Test conversation context passing
    - _Requirements: 7.1, 7.2, 7.5_

- [ ] 15. Implement Progress Report (Student)
  - [ ] 15.1 Create ReportService
    - Method: getStudentProgress($studentId)
    - Calculate attendance_rate từ attendance records
    - Collect assessment_scores từ assessment_scores table
    - Calculate completion_percentage based on schedules attended
    - Return structured report data
    - _Requirements: 10.1, 10.2_

  - [ ] 15.2 Create progress report view
    - reports/progress.blade.php: hiển thị attendance rate, scores, completion %
    - Visualize progress với Bootstrap progress bars và charts (Chart.js)
    - _Requirements: 10.1, 10.3, 10.4_

  - [ ] 15.3 Implement certificate eligibility notification
    - Check: completion_percentage >= 80 AND average_score >= passing_score
    - Create notification với type 'certificate_eligible'
    - _Requirements: 10.5_

  - [ ]* 15.4 Write property tests for progress report
    - **Property 28: Progress Report Contains Required Metrics**
    - **Property 29: Progress Report Updates With New Data**
    - **Property 30: Certificate Eligibility Notification**
    - **Validates: Requirements 10.1, 10.2, 10.5**


- [ ] 16. Implement Certificate Generation
  - [ ] 16.1 Create CertificateService
    - Method: generate($studentId, $courseId)
    - Generate unique certificate_number (UUID)
    - Create certificate record in database
    - Generate PDF using DomPDF với template
    - Store PDF path in certificate.pdf_path
    - _Requirements: 11.1, 11.2, 11.3_

  - [ ] 16.2 Create certificate views
    - certificates/index.blade.php (Student): danh sách certificates
    - certificates/download.blade.php: download PDF
    - certificates/verify.blade.php: public verification page
    - certificates/template.blade.php: PDF template
    - _Requirements: 11.1, 11.2, 11.4_

  - [ ] 16.3 Implement public verification endpoint
    - Route: GET /certificates/verify/{certificateNumber}
    - Display certificate details: student_name, course_name, issue_date
    - No authentication required
    - _Requirements: 11.4_

  - [ ]* 16.4 Write property tests for certificate
    - **Property 31: Certificate Generation Includes Required Fields**
    - **Property 32: Certificate PDF Generation**
    - **Property 33: Certificate Persistence**
    - **Property 34: Certificate Verification by Number**
    - **Validates: Requirements 11.1, 11.2, 11.3, 11.4**

- [ ] 17. Implement Teacher Management (Admin)
  - [ ] 17.1 Create TeacherController
    - CRUD operations cho teacher profiles
    - Display assigned classes và schedules
    - Deactivate teacher account
    - Prevent assigning deactivated teachers to new classes
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5_

  - [ ] 17.2 Create teacher management views
    - teachers/index.blade.php: danh sách teachers
    - teachers/create.blade.php: form tạo teacher
    - teachers/show.blade.php: chi tiết teacher với assigned classes
    - _Requirements: 12.1, 12.4_

  - [ ]* 17.3 Write property tests for teacher management
    - **Property 35: Teacher Deactivation Prevents Class Assignment**
    - **Validates: Requirements 12.5**

- [ ] 18. Implement Notification System
  - [ ] 18.1 Create NotificationService với multi-channel delivery
    - Method: send($user, $type, $data)
    - Create in-app notification record
    - Queue email job for delivery
    - Support notification types: enrollment_confirmation, schedule_update, payment_reminder, score_posted, certificate_eligible
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5_

  - [ ] 18.2 Create notification views
    - notifications/index.blade.php: danh sách notifications với mark as read
    - Email templates cho từng notification type
    - _Requirements: 13.5_

  - [ ] 18.3 Implement scheduled reminders
    - Create scheduled job: SendClassReminders (24 hours before class)
    - Create scheduled job: SendPaymentReminders (3 days before due date)
    - Register jobs trong Kernel schedule
    - _Requirements: 13.2, 13.3_

  - [ ]* 18.4 Write property tests for notifications
    - **Property 36: Notification Multi-Channel Delivery**
    - **Validates: Requirements 13.5**


- [ ] 19. Implement Course Search and Filter (Student)
  - [ ] 19.1 Create CourseSearchService
    - Search by name or description (case-insensitive)
    - Filter by language, level, price range
    - Return courses với count of active classes
    - _Requirements: 14.1, 14.2, 14.5_

  - [ ] 19.2 Update course browsing view với search/filter
    - Add search input và filter dropdowns
    - AJAX-based search với jQuery
    - Display results với class count
    - Sort by relevance
    - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5_

  - [ ]* 19.3 Write property tests for search/filter
    - **Property 37: Course Search Returns Matching Results**
    - **Property 38: Course Filter Applies All Criteria**
    - **Property 39: Course Search Results Include Class Count**
    - **Validates: Requirements 14.1, 14.2, 14.5**

- [ ] 20. Implement Admin Reports and Analytics
  - [ ] 20.1 Create ReportService cho admin
    - Method: getMonthlyReport($month)
    - Calculate: total_students, active_courses, revenue
    - Method: getTeacherPerformance($teacherId)
    - Calculate: average_rating, completion_rate
    - Method: getCoursePopularity()
    - Sort courses by enrollment count
    - _Requirements: 15.1, 15.2, 15.3_

  - [ ] 20.2 Create admin dashboard
    - dashboard.blade.php: overview với key metrics
    - reports/monthly.blade.php: monthly report
    - reports/teachers.blade.php: teacher performance
    - reports/courses.blade.php: course popularity
    - _Requirements: 15.1, 15.2, 15.3, 15.5_

  - [ ] 20.3 Implement report export
    - Export to PDF using DomPDF
    - Export to Excel using Laravel Excel
    - _Requirements: 15.4_

  - [ ]* 20.4 Write property tests for reports
    - **Property 40: Admin Report Calculations Accuracy**
    - **Property 41: Teacher Performance Metrics Calculation**
    - **Property 42: Course Popularity Ranking**
    - **Property 43: Report Export Format Validation**
    - **Validates: Requirements 15.1, 15.2, 15.3, 15.4**

- [ ] 21. Implement Feedback System
  - [ ] 21.1 Create FeedbackController
    - Prompt student to provide feedback after course completion
    - Validate ratings: 1-5 for course_rating và teacher_rating
    - Support anonymous feedback
    - Calculate average ratings
    - _Requirements: 16.1, 16.2, 16.3, 16.4_

  - [ ] 21.2 Create feedback views
    - feedbacks/create.blade.php: feedback form với rating stars
    - feedbacks/index.blade.php (Admin): view all feedbacks
    - Display average ratings on course và teacher pages
    - _Requirements: 16.1, 16.4, 16.5_

  - [ ]* 21.3 Write property tests for feedback
    - **Property 44: Feedback Rating Range Validation**
    - **Property 45: Anonymous Feedback Flag Storage**
    - **Property 46: Average Rating Calculation**
    - **Validates: Requirements 16.2, 16.3, 16.4**



## Task Dependency Graph

```json
{
  "1. Setup project structure and core infrastructure": [],
  "2.1 Create migrations cho tất cả tables": ["1. Setup project structure and core infrastructure"],
  "2.2 Create Eloquent models với relationships": ["2.1 Create migrations cho tất cả tables"],
  "2.3 Write property test for database constraints": ["2.2 Create Eloquent models với relationships"],
  "3.1 Setup role-based middleware": ["2.2 Create Eloquent models với relationships"],
  "3.2 Create registration flows cho từng role": ["3.1 Setup role-based middleware"],
  "3.3 Write unit tests for authentication": ["3.2 Create registration flows cho từng role"],
  "4.1 Create CourseController và CourseService": ["2.2 Create Eloquent models với relationships"],
  "4.2 Create Blade views cho course management": ["4.1 Create CourseController và CourseService"],
  "4.3 Write property tests for course operations": ["4.2 Create Blade views cho course management"],
  "5.1 Create ClassController và related services": ["4.1 Create CourseController và CourseService"],
  "5.2 Create Blade views cho class management": ["5.1 Create ClassController và related services"],
  "5.3 Write property tests for class validation": ["5.2 Create Blade views cho class management"],
  "6. Checkpoint - Ensure all tests pass": ["2.3 Write property test for database constraints", "3.3 Write unit tests for authentication", "4.3 Write property tests for course operations", "5.3 Write property tests for class validation"],
  "7.1 Create EnrollmentController và EnrollmentService": ["5.1 Create ClassController và related services"],
  "7.2 Create course browsing views cho students": ["7.1 Create EnrollmentController và EnrollmentService"],
  "7.3 Implement enrollment confirmation notification": ["7.1 Create EnrollmentController và EnrollmentService"],
  "7.4 Write property tests for enrollment": ["7.2 Create course browsing views cho students", "7.3 Implement enrollment confirmation notification"],
  "8.1 Create PaymentController và PaymentService": ["7.1 Create EnrollmentController và EnrollmentService"],
  "8.2 Implement payment completion flow": ["8.1 Create PaymentController và PaymentService"],
  "8.3 Create payment views": ["8.1 Create PaymentController và PaymentService"],
  "8.4 Write property tests for payment flow": ["8.2 Implement payment completion flow", "8.3 Create payment views"],
  "9.1 Create ScheduleController và ScheduleService": ["5.1 Create ClassController và related services"],
  "9.2 Create schedule views": ["9.1 Create ScheduleController và ScheduleService"],
  "9.3 Write property tests for schedule operations": ["9.2 Create schedule views"],
  "10.1 Create AttendanceController và AttendanceService": ["9.1 Create ScheduleController và ScheduleService"],
  "10.2 Create attendance views": ["10.1 Create AttendanceController và AttendanceService"],
  "10.3 Write property tests for attendance": ["10.2 Create attendance views"],
  "11.1 Create AssessmentController và AssessmentService": ["5.1 Create ClassController và related services"],
  "11.2 Create assessment views": ["11.1 Create AssessmentController và AssessmentService"],
  "11.3 Write property tests for assessment": ["11.2 Create assessment views"],
  "12. Checkpoint - Ensure core features work end-to-end": ["7.4 Write property tests for enrollment", "8.4 Write property tests for payment flow", "9.3 Write property tests for schedule operations", "10.3 Write property tests for attendance", "11.3 Write property tests for assessment"],
  "13.1 Create RuleBasedEngine service": ["2.2 Create Eloquent models với relationships"],
  "13.2 Create conversation storage": ["13.1 Create RuleBasedEngine service"],
  "13.3 Write property tests for rule-based responses": ["13.2 Create conversation storage"],
  "14.1 Create GeminiAPIClient service": ["13.1 Create RuleBasedEngine service"],
  "14.2 Create AIAssistantService orchestrator": ["13.2 Create conversation storage", "14.1 Create GeminiAPIClient service"],
  "14.3 Create VirtualAssistantController": ["14.2 Create AIAssistantService orchestrator"],
  "14.4 Create chat interface (Frontend)": ["14.3 Create VirtualAssistantController"],
  "14.5 Write property tests for AI assistant": ["14.4 Create chat interface (Frontend)"],
  "14.6 Write integration tests for chat flow": ["14.4 Create chat interface (Frontend)"],
  "15.1 Create ReportService": ["10.1 Create AttendanceController và AttendanceService", "11.1 Create AssessmentController và AssessmentService"],
  "15.2 Create progress report view": ["15.1 Create ReportService"],
  "15.3 Implement certificate eligibility notification": ["15.1 Create ReportService"],
  "15.4 Write property tests for progress report": ["15.2 Create progress report view", "15.3 Implement certificate eligibility notification"],
  "16.1 Create CertificateService": ["15.1 Create ReportService"],
  "16.2 Create certificate views": ["16.1 Create CertificateService"],
  "16.3 Implement public verification endpoint": ["16.1 Create CertificateService"],
  "16.4 Write property tests for certificate": ["16.2 Create certificate views", "16.3 Implement public verification endpoint"],
  "17.1 Create TeacherController": ["2.2 Create Eloquent models với relationships"],
  "17.2 Create teacher management views": ["17.1 Create TeacherController"],
  "17.3 Write property tests for teacher management": ["17.2 Create teacher management views"],
  "18.1 Create NotificationService với multi-channel delivery": ["7.3 Implement enrollment confirmation notification"],
  "18.2 Create notification views": ["18.1 Create NotificationService với multi-channel delivery"],
  "18.3 Implement scheduled reminders": ["18.1 Create NotificationService với multi-channel delivery"],
  "18.4 Write property tests for notifications": ["18.2 Create notification views", "18.3 Implement scheduled reminders"],
  "19.1 Create CourseSearchService": ["4.1 Create CourseController và CourseService"],
  "19.2 Update course browsing view với search/filter": ["19.1 Create CourseSearchService", "7.2 Create course browsing views cho students"],
  "19.3 Write property tests for search/filter": ["19.2 Update course browsing view với search/filter"],
  "20.1 Create ReportService cho admin": ["7.1 Create EnrollmentController và EnrollmentService", "8.1 Create PaymentController và PaymentService"],
  "20.2 Create admin dashboard": ["20.1 Create ReportService cho admin"],
  "20.3 Implement report export": ["20.1 Create ReportService cho admin"],
  "20.4 Write property tests for reports": ["20.2 Create admin dashboard", "20.3 Implement report export"],
  "21.1 Create FeedbackController": ["7.1 Create EnrollmentController và EnrollmentService"],
  "21.2 Create feedback views": ["21.1 Create FeedbackController"],
  "21.3 Write property tests for feedback": ["21.2 Create feedback views"]
}
```

## Notes

- All property tests (tasks marked with *) are optional but recommended for production quality
- Checkpoint tasks (6, 12) are manual review points - orchestrator will ask user before proceeding
- Tasks marked with [~] are partially completed and need implementation
- Tasks 8, 9, 10, 11 have been executed outside spec workflow and need verification
- Tasks 14.1-14.4 (Gemini integration) have been partially completed through separate bugfix workflow
