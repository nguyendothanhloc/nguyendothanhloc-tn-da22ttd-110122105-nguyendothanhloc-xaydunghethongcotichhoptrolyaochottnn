# Entity Relationship Diagram (ERD) - Language Center Management System

## Database: `language_center`

### Mermaid ERD Diagram

```mermaid
erDiagram
    %% Core User Management
    users ||--o| students : "has profile"
    users ||--o| teachers : "has profile"
    users ||--o{ notifications : "receives"
    
    %% Course and Class Management
    courses ||--o{ classes : "has"
    teachers ||--o{ classes : "teaches"
    
    %% Enrollment System
    students ||--o{ enrollments : "enrolls in"
    classes ||--o{ enrollments : "has"
    enrollments ||--|| payments : "has payment"
    
    %% Schedule and Attendance
    classes ||--o{ schedules : "has"
    schedules ||--o{ attendances : "tracks"
    students ||--o{ attendances : "attends"
    
    %% Assessment System
    classes ||--o{ assessments : "has"
    assessments ||--o{ assessment_scores : "has scores"
    students ||--o{ assessment_scores : "receives"
    
    %% Certificate System
    students ||--o{ certificates : "earns"
    courses ||--o{ certificates : "awards"
    
    %% Feedback System
    students ||--o{ feedbacks : "submits"
    classes ||--o{ feedbacks : "receives"
    
    %% Chatbot System
    students ||--o{ conversations : "has"
    conversations ||--o{ messages : "contains"
    
    %% Entity Details
    
    users {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin, teacher, student"
        string phone
        string avatar
        boolean is_active
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }
    
    students {
        bigint id PK
        bigint user_id FK
        string level
        text interests
        timestamp created_at
        timestamp updated_at
    }
    
    teachers {
        bigint id PK
        bigint user_id FK
        string specialization
        text qualifications
        text bio
        timestamp created_at
        timestamp updated_at
    }
    
    courses {
        bigint id PK
        string name
        text description
        string language
        enum level "beginner, elementary, intermediate, advanced"
        integer duration_weeks
        decimal price
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }
    
    classes {
        bigint id PK
        bigint course_id FK
        bigint teacher_id FK
        string name
        date start_date
        date end_date
        integer max_capacity
        integer current_enrollment
        enum status "upcoming, ongoing, completed, cancelled"
        string shift "morning, afternoon, evening"
        string weekdays
        timestamp created_at
        timestamp updated_at
    }
    
    enrollments {
        bigint id PK
        bigint student_id FK
        bigint class_id FK
        date enrollment_date
        enum status "pending, approved, rejected, cancelled"
        decimal completion_percentage
        timestamp created_at
        timestamp updated_at
    }
    
    payments {
        bigint id PK
        bigint enrollment_id FK
        decimal amount
        string payment_method
        enum status "pending, paid, overdue, cancelled"
        date due_date
        date paid_date
        string proof_image
        text note
        timestamp created_at
        timestamp updated_at
    }
    
    schedules {
        bigint id PK
        bigint class_id FK
        date date
        time start_time
        time end_time
        string location
        text topic
        enum status "scheduled, completed, cancelled"
        timestamp created_at
        timestamp updated_at
    }
    
    attendances {
        bigint id PK
        bigint schedule_id FK
        bigint student_id FK
        enum status "present, absent, late, excused"
        text note
        timestamp recorded_at
        timestamp created_at
        timestamp updated_at
    }
    
    assessments {
        bigint id PK
        bigint class_id FK
        string name
        enum type "quiz, midterm, final, assignment, speaking, listening"
        decimal max_score
        date assessment_date
        text description
        timestamp created_at
        timestamp updated_at
    }
    
    assessment_scores {
        bigint id PK
        bigint assessment_id FK
        bigint student_id FK
        decimal score
        text feedback
        timestamp created_at
        timestamp updated_at
    }
    
    certificates {
        bigint id PK
        bigint student_id FK
        bigint course_id FK
        string certificate_number UK
        date issue_date
        string pdf_path
        timestamp created_at
        timestamp updated_at
    }
    
    feedbacks {
        bigint id PK
        bigint student_id FK
        bigint class_id FK
        integer course_rating "1-5"
        integer teacher_rating "1-5"
        text comment
        boolean is_anonymous
        timestamp created_at
        timestamp updated_at
    }
    
    notifications {
        bigint id PK
        bigint user_id FK
        string type
        string title
        text message
        boolean is_read
        timestamp sent_at
        timestamp created_at
        timestamp updated_at
    }
    
    conversations {
        bigint id PK
        bigint student_id FK
        timestamp started_at
        timestamp last_message_at
        integer message_count
        timestamp created_at
        timestamp updated_at
    }
    
    messages {
        bigint id PK
        bigint conversation_id FK
        enum sender_type "student, bot"
        text content
        timestamp created_at
        timestamp updated_at
    }
    
    chatbot_knowledge {
        bigint id PK
        string category
        text question
        text answer
        text keywords
        integer priority "1-100"
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }
```

## Key Relationships

### 1. User Management
- **users → students** (1:1): Each user can have one student profile
- **users → teachers** (1:1): Each user can have one teacher profile
- **users → notifications** (1:N): Each user can receive multiple notifications

### 2. Course & Class Structure
- **courses → classes** (1:N): Each course can have multiple classes
- **teachers → classes** (1:N): Each teacher can teach multiple classes
- **classes → enrollments** (1:N): Each class can have multiple enrollments

### 3. Enrollment & Payment
- **students → enrollments** (1:N): Each student can enroll in multiple classes
- **enrollments → payments** (1:1): Each enrollment has one payment record

### 4. Schedule & Attendance
- **classes → schedules** (1:N): Each class has multiple scheduled sessions
- **schedules → attendances** (1:N): Each session tracks multiple student attendances
- **students → attendances** (1:N): Each student has multiple attendance records

### 5. Assessment System
- **classes → assessments** (1:N): Each class can have multiple assessments
- **assessments → assessment_scores** (1:N): Each assessment has multiple student scores
- **students → assessment_scores** (1:N): Each student receives multiple scores

### 6. Certificate System
- **students → certificates** (1:N): Each student can earn multiple certificates
- **courses → certificates** (1:N): Each course can award multiple certificates

### 7. Feedback System
- **students → feedbacks** (1:N): Each student can submit multiple feedbacks
- **classes → feedbacks** (1:N): Each class can receive multiple feedbacks

### 8. Chatbot System
- **students → conversations** (1:N): Each student can have multiple chatbot conversations
- **conversations → messages** (1:N): Each conversation contains multiple messages
- **chatbot_knowledge**: Standalone table for FAQ/knowledge base (no direct relationships)

## Database Statistics

**Total Tables:** 17

### Entity Categories:
1. **Core Entities (3):** users, students, teachers
2. **Academic Entities (4):** courses, classes, enrollments, schedules
3. **Assessment Entities (3):** assessments, assessment_scores, certificates
4. **Attendance Entity (1):** attendances
5. **Payment Entity (1):** payments
6. **Feedback Entity (1):** feedbacks
7. **Notification Entity (1):** notifications
8. **Chatbot Entities (3):** conversations, messages, chatbot_knowledge

## Cardinality Summary

### One-to-One (1:1) Relationships: 3
- users → students
- users → teachers
- enrollments → payments

### One-to-Many (1:N) Relationships: 21
- users → notifications
- courses → classes
- courses → certificates
- teachers → classes
- students → enrollments
- students → attendances
- students → assessment_scores
- students → certificates
- students → feedbacks
- students → conversations
- classes → enrollments
- classes → schedules
- classes → assessments
- classes → feedbacks
- schedules → attendances
- assessments → assessment_scores
- conversations → messages
- (and more through class relationships)

### Many-to-Many (M:N) Relationships: 1
- students ↔ classes (through enrollments pivot table)

## Notes

1. **Cascade Deletes**: Consider implementing ON DELETE CASCADE for critical relationships
2. **Indexes**: Ensure foreign keys and frequently queried fields are indexed
3. **Soft Deletes**: Laravel supports soft deletes - consider enabling for critical entities
4. **Timestamps**: All tables have `created_at` and `updated_at` automatically managed by Laravel
5. **Unique Constraints**: 
   - users.email (unique)
   - certificates.certificate_number (unique)
