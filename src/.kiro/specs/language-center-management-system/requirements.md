# Requirements Document

## Introduction

Hệ thống quản lý khóa học tích hợp trợ lý ảo cho Trung tâm Ngoại ngữ là một giải pháp toàn diện giúp quản lý các khóa học, học viên, giáo viên, lịch học và cung cấp hỗ trợ tự động thông qua trợ lý ảo AI. Hệ thống cho phép học viên đăng ký khóa học, theo dõi tiến độ học tập, và nhận tư vấn từ trợ lý ảo. Giáo viên có thể quản lý lớp học, điểm danh, chấm điểm. Quản trị viên có thể quản lý toàn bộ hoạt động của trung tâm.

## Glossary

- **System**: Hệ thống quản lý khóa học tích hợp trợ lý ảo
- **Student**: Học viên đăng ký học tại trung tâm
- **Teacher**: Giáo viên giảng dạy các khóa học
- **Administrator**: Quản trị viên quản lý toàn bộ hệ thống
- **Course**: Khóa học ngoại ngữ với chương trình học cụ thể
- **Class**: Lớp học cụ thể của một khóa học với lịch học và danh sách học viên
- **Virtual_Assistant**: Trợ lý ảo AI hỗ trợ tư vấn và trả lời câu hỏi
- **Enrollment**: Đăng ký tham gia một lớp học
- **Attendance**: Điểm danh học viên trong buổi học
- **Schedule**: Lịch học của lớp học
- **Payment**: Thanh toán học phí
- **Progress_Report**: Báo cáo tiến độ học tập của học viên
- **Assessment**: Đánh giá, bài kiểm tra
- **Certificate**: Chứng chỉ hoàn thành khóa học

## Requirements

### Requirement 1: Quản lý thông tin khóa học

**User Story:** As an Administrator, I want to manage course information, so that students can view and enroll in available courses

#### Acceptance Criteria

1. THE System SHALL allow Administrator to create a new Course with name, description, level, duration, and price
2. THE System SHALL allow Administrator to update Course information
3. THE System SHALL allow Administrator to deactivate a Course
4. WHEN a Course is created or updated, THE System SHALL validate that all required fields are provided
5. THE System SHALL display all active Courses to Students

### Requirement 2: Quản lý lớp học

**User Story:** As an Administrator, I want to manage classes, so that courses can be organized into specific teaching sessions

#### Acceptance Criteria

1. THE System SHALL allow Administrator to create a Class for a Course with start date, end date, maximum capacity, and assigned Teacher
2. THE System SHALL allow Administrator to update Class information
3. WHEN a Class is created, THE System SHALL validate that start date is before end date
4. WHEN a Class is created, THE System SHALL validate that maximum capacity is greater than zero
5. THE System SHALL prevent creating a Class without an assigned Teacher

### Requirement 3: Đăng ký khóa học

**User Story:** As a Student, I want to enroll in classes, so that I can participate in language courses

#### Acceptance Criteria

1. WHEN a Student selects a Class, THE System SHALL display Class details including schedule, teacher, and available slots
2. WHEN a Student requests enrollment, THE System SHALL create an Enrollment if the Class has available capacity
3. IF a Class is at maximum capacity, THEN THE System SHALL reject the enrollment request and display a message
4. WHEN an Enrollment is created, THE System SHALL send a confirmation notification to the Student
5. THE System SHALL allow Student to view all their active Enrollments

### Requirement 4: Quản lý lịch học

**User Story:** As a Teacher, I want to manage class schedules, so that students know when to attend classes

#### Acceptance Criteria

1. THE System SHALL allow Teacher to create a Schedule for their Class with date, start time, end time, and location
2. WHEN a Schedule is created, THE System SHALL validate that start time is before end time
3. WHEN a Schedule is created, THE System SHALL notify all enrolled Students
4. THE System SHALL allow Teacher to update or cancel a Schedule
5. WHEN a Schedule is cancelled, THE System SHALL notify all enrolled Students within 5 minutes

### Requirement 5: Điểm danh học viên

**User Story:** As a Teacher, I want to take attendance, so that I can track student participation

#### Acceptance Criteria

1. WHEN a Teacher opens a scheduled class session, THE System SHALL display the list of enrolled Students
2. THE System SHALL allow Teacher to mark each Student as present, absent, or late
3. WHEN attendance is submitted, THE System SHALL save the Attendance record with timestamp
4. THE System SHALL allow Teacher to view attendance history for a Class
5. THE System SHALL calculate attendance rate for each Student in a Class

### Requirement 6: Quản lý điểm số và đánh giá

**User Story:** As a Teacher, I want to record student assessments, so that students can track their learning progress

#### Acceptance Criteria

1. THE System SHALL allow Teacher to create an Assessment for a Class with name, type, maximum score, and date
2. THE System SHALL allow Teacher to enter scores for each Student in an Assessment
3. WHEN a score is entered, THE System SHALL validate that the score does not exceed maximum score
4. WHEN scores are saved, THE System SHALL notify affected Students
5. THE System SHALL allow Student to view all their Assessment scores

### Requirement 7: Trợ lý ảo tư vấn khóa học

**User Story:** As a Student, I want to interact with a virtual assistant, so that I can get course recommendations and answers to my questions

#### Acceptance Criteria

1. THE System SHALL provide a Virtual_Assistant interface accessible to Students
2. WHEN a Student asks a question, THE Virtual_Assistant SHALL respond within 3 seconds
3. WHEN a Student asks about course recommendations, THE Virtual_Assistant SHALL suggest Courses based on the Student's level and interests
4. WHEN a Student asks about class schedules, THE Virtual_Assistant SHALL provide accurate Schedule information
5. THE Virtual_Assistant SHALL maintain conversation context for at least 10 message exchanges

### Requirement 8: Trợ lý ảo hỗ trợ học tập

**User Story:** As a Student, I want the virtual assistant to help with learning questions, so that I can get immediate support outside of class time

#### Acceptance Criteria

1. WHEN a Student asks a language learning question, THE Virtual_Assistant SHALL provide an explanation with examples
2. WHEN a Student requests practice exercises, THE Virtual_Assistant SHALL generate relevant exercises based on the Student's current Course level
3. THE Virtual_Assistant SHALL support text and voice input from Students
4. WHEN voice input is received, THE System SHALL transcribe it within 2 seconds
5. THE Virtual_Assistant SHALL log all interactions for quality improvement

### Requirement 9: Thanh toán học phí

**User Story:** As a Student, I want to pay for courses online, so that I can complete enrollment conveniently

#### Acceptance Criteria

1. WHEN a Student completes enrollment, THE System SHALL generate a Payment request with amount and due date
2. THE System SHALL support multiple payment methods including credit card, bank transfer, and e-wallet
3. WHEN a Payment is completed, THE System SHALL update the Enrollment status to paid
4. WHEN a Payment is completed, THE System SHALL send a receipt to the Student
5. IF a Payment is not completed within 7 days, THEN THE System SHALL send a reminder notification to the Student

### Requirement 10: Báo cáo tiến độ học tập

**User Story:** As a Student, I want to view my learning progress, so that I can track my improvement

#### Acceptance Criteria

1. THE System SHALL generate a Progress_Report for each Student showing attendance rate, assessment scores, and completion percentage
2. THE System SHALL update Progress_Report automatically when new Attendance or Assessment data is recorded
3. THE System SHALL allow Student to view their Progress_Report at any time
4. THE System SHALL visualize progress using charts and graphs
5. WHEN a Student completes 80% of a Course with passing scores, THE System SHALL notify them of Certificate eligibility

### Requirement 11: Cấp chứng chỉ hoàn thành

**User Story:** As a Student, I want to receive a certificate upon course completion, so that I can prove my language proficiency

#### Acceptance Criteria

1. WHEN a Student completes all requirements of a Course, THE System SHALL generate a Certificate with Student name, Course name, completion date, and unique certificate number
2. THE System SHALL allow Student to download the Certificate in PDF format
3. THE System SHALL store all issued Certificates for verification purposes
4. THE System SHALL provide a public verification interface where anyone can verify a Certificate using its unique number
5. THE Certificate SHALL include a QR code linking to the verification page

### Requirement 12: Quản lý giáo viên

**User Story:** As an Administrator, I want to manage teacher information, so that I can assign teachers to classes effectively

#### Acceptance Criteria

1. THE System SHALL allow Administrator to create a Teacher profile with name, email, phone, specialization, and qualifications
2. THE System SHALL allow Administrator to update Teacher information
3. THE System SHALL allow Administrator to deactivate a Teacher account
4. THE System SHALL display Teacher's assigned Classes and Schedule
5. WHEN a Teacher is deactivated, THE System SHALL prevent assigning them to new Classes

### Requirement 13: Thông báo và nhắc nhở

**User Story:** As a Student, I want to receive notifications about important events, so that I don't miss classes or deadlines

#### Acceptance Criteria

1. WHEN a Schedule is created or updated, THE System SHALL send notification to all enrolled Students
2. THE System SHALL send reminder notification to Students 24 hours before a scheduled class
3. WHEN a Payment deadline is approaching, THE System SHALL send reminder notification 3 days before due date
4. WHEN a Teacher posts new Assessment scores, THE System SHALL notify affected Students
5. THE System SHALL support notification delivery via email and in-app notifications

### Requirement 14: Tìm kiếm và lọc khóa học

**User Story:** As a Student, I want to search and filter courses, so that I can find courses that match my needs

#### Acceptance Criteria

1. THE System SHALL allow Student to search Courses by name or description
2. THE System SHALL allow Student to filter Courses by language, level, and price range
3. WHEN search or filter is applied, THE System SHALL display matching Courses within 1 second
4. THE System SHALL display search results sorted by relevance
5. THE System SHALL show the number of available Classes for each Course in search results

### Requirement 15: Phân tích và báo cáo cho quản trị viên

**User Story:** As an Administrator, I want to view analytics and reports, so that I can make informed decisions about the center's operations

#### Acceptance Criteria

1. THE System SHALL generate reports showing total Students, active Courses, and revenue by month
2. THE System SHALL display Teacher performance metrics including average student satisfaction and class completion rates
3. THE System SHALL show Course popularity based on enrollment numbers
4. THE System SHALL allow Administrator to export reports in PDF and Excel formats
5. THE System SHALL update dashboard metrics in real-time when new data is recorded

### Requirement 16: Phản hồi và đánh giá

**User Story:** As a Student, I want to provide feedback on courses and teachers, so that the center can improve quality

#### Acceptance Criteria

1. WHEN a Student completes a Course, THE System SHALL prompt them to provide a rating and written feedback
2. THE System SHALL allow Student to rate a Course and Teacher on a scale of 1 to 5 stars
3. THE System SHALL allow Student to submit anonymous feedback
4. THE System SHALL display average ratings for each Course and Teacher
5. THE System SHALL allow Administrator to view all feedback and ratings

### Requirement 17: Xử lý dữ liệu trợ lý ảo

**User Story:** As a system, I want to parse and format virtual assistant conversations, so that interactions are properly stored and retrieved

#### Acceptance Criteria

1. WHEN a Virtual_Assistant conversation occurs, THE System SHALL parse the conversation into structured message objects with sender, content, and timestamp
2. WHEN conversation data is stored, THE System SHALL format it in JSON format
3. THE System SHALL provide a formatter that converts conversation objects back into human-readable text
4. FOR ALL valid conversation objects, parsing then formatting then parsing SHALL produce an equivalent object (round-trip property)
5. WHEN invalid conversation data is encountered, THE System SHALL return a descriptive error message

