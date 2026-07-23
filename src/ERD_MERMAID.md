# Sơ đồ ERD - Hệ thống Quản lý Trung tâm Ngoại ngữ

## Mermaid ERD Diagram

File này có thể xem trực tiếp trên GitHub hoặc VSCode với Mermaid extension.

```mermaid
erDiagram
    %% ===== NHÓM 1: NGƯỜI DÙNG =====
    users ||--o| students : "has profile"
    users ||--o| teachers : "has profile"
    users ||--o{ notifications : "receives"
    
    users {
        bigint id PK
        varchar name
        varchar email UK
        varchar password
        enum role "admin, teacher, student"
        varchar phone
        varchar avatar
        boolean is_active
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }
    
    students {
        bigint id PK
        bigint user_id FK
        varchar level "beginner, intermediate, advanced"
        text interests
        timestamp created_at
        timestamp updated_at
    }
    
    teachers {
        bigint id PK
        bigint user_id FK
        varchar specialization
        text qualifications
        text bio
        timestamp created_at
        timestamp updated_at
    }
    
    %% ===== NHÓM 2: KHÓA HỌC & LỚP HỌC =====
    courses ||--o{ classes : "has"
    courses ||--o{ certificates : "issues"
    
    teachers ||--o{ classes : "teaches"
    
    classes ||--o{ enrollments : "has"
    classes ||--o{ schedules : "has"
    classes ||--o{ assessments : "has"
    classes ||--o{ feedbacks : "receives"
    
    courses {
        bigint id PK
        varchar name
        text description
        varchar language "English, Japanese, Korean"
        enum level "beginner, elementary, intermediate, advanced"
        int duration_weeks
        decimal price
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }
    
    classes {
        bigint id PK
        bigint course_id FK
        bigint teacher_id FK
        varchar name
        date start_date
        date end_date
        int max_capacity
        int current_enrollment
        enum status "upcoming, ongoing, completed, cancelled"
        varchar shift "morning, afternoon, evening"
        varchar weekdays
        timestamp created_at
        timestamp updated_at
    }
    
    %% ===== NHÓM 3: ĐĂNG KÝ & THANH TOÁN =====
    students ||--o{ enrollments : "enrolls in"
    enrollments ||--o| payments : "has payment"
    
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
        varchar payment_method "bank_transfer, cash, credit_card"
        enum status "pending, paid, overdue, cancelled"
        date due_date
        date paid_date
        varchar proof_image
        text note
        timestamp created_at
        timestamp updated_at
    }
    
    %% ===== NHÓM 4: LỊCH HỌC & ĐIỂM DANH =====
    schedules ||--o{ attendances : "records"
    students ||--o{ attendances : "attends"
    
    schedules {
        bigint id PK
        bigint class_id FK
        date date
        time start_time
        time end_time
        varchar location
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
    
    %% ===== NHÓM 5: ĐÁNH GIÁ & KIỂM TRA =====
    assessments ||--o{ assessment_scores : "has scores"
    students ||--o{ assessment_scores : "receives"
    
    assessments {
        bigint id PK
        bigint class_id FK
        varchar name
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
    
    %% ===== NHÓM 6: CHỨNG CHỈ & PHẢN HỒI =====
    students ||--o{ certificates : "earns"
    students ||--o{ feedbacks : "gives"
    
    certificates {
        bigint id PK
        bigint student_id FK
        bigint course_id FK
        varchar certificate_number UK
        date issue_date
        varchar pdf_path
        timestamp created_at
        timestamp updated_at
    }
    
    feedbacks {
        bigint id PK
        bigint student_id FK
        bigint class_id FK
        int course_rating "1-5"
        int teacher_rating "1-5"
        text comment
        boolean is_anonymous
        timestamp created_at
        timestamp updated_at
    }
    
    %% ===== NHÓM 7: THÔNG BÁO =====
    notifications {
        bigint id PK
        bigint user_id FK
        varchar type "enrollment, payment, schedule, assessment"
        varchar title
        text message
        boolean is_read
        timestamp sent_at
        timestamp created_at
        timestamp updated_at
    }
    
    %% ===== NHÓM 8: CHATBOT =====
    students ||--o{ conversations : "chats with"
    conversations ||--o{ messages : "contains"
    
    conversations {
        bigint id PK
        bigint student_id FK
        timestamp started_at
        timestamp last_message_at
        int message_count
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
        varchar category
        text question
        text answer
        varchar keywords
        int priority
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }
```

## Hướng dẫn sử dụng:

1. **Xem trên GitHub**: Copy nội dung file này lên GitHub, sơ đồ sẽ tự động hiển thị
2. **Xem trên VSCode**: Cài extension "Markdown Preview Mermaid Support"
3. **Xuất ra hình**: Dùng https://mermaid.live/ để xuất ra PNG/SVG

## Chú thích ký hiệu:

- `||--o|` : One-to-One (0 hoặc 1)
- `||--o{` : One-to-Many (0 hoặc nhiều)
- `PK` : Primary Key (Khóa chính)
- `FK` : Foreign Key (Khóa ngoại)
- `UK` : Unique Key (Duy nhất)
