# Eloquent Models Summary - Language Center Management System

## Task 2.2 Completion Report

All 16 Eloquent models have been created with complete relationships, fillable fields, and casts.

## Models Overview

### 1. User Model
**File**: `app/Models/User.php`
**Extends**: `Authenticatable`
**Traits**: `HasFactory`, `Notifiable`

**Fillable Fields**:
- name, email, password, role, phone, avatar, is_active

**Casts**:
- email_verified_at: datetime
- password: hashed
- is_active: boolean

**Relationships**:
- `hasOne(Teacher)` - Teacher profile
- `hasOne(Student)` - Student profile
- `hasMany(Notification)` - User notifications

---

### 2. Teacher Model
**File**: `app/Models/Teacher.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- user_id, specialization, qualifications, bio

**Relationships**:
- `belongsTo(User)` - Associated user account
- `hasMany(ClassModel)` - Classes taught by teacher

---

### 3. Student Model
**File**: `app/Models/Student.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- user_id, level, interests

**Relationships**:
- `belongsTo(User)` - Associated user account
- `hasMany(Enrollment)` - Student enrollments
- `belongsToMany(ClassModel)` through enrollments - Enrolled classes
- `hasMany(Attendance)` - Attendance records
- `hasMany(AssessmentScore)` - Assessment scores
- `hasMany(Certificate)` - Earned certificates
- `hasMany(Conversation)` - Virtual assistant conversations
- `hasMany(Feedback)` - Submitted feedback

---

### 4. Course Model
**File**: `app/Models/Course.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- name, description, language, level, duration_weeks, price, is_active

**Casts**:
- price: decimal:2
- is_active: boolean

**Relationships**:
- `hasMany(ClassModel)` - Course classes
- `hasMany(Certificate)` - Issued certificates

---

### 5. ClassModel
**File**: `app/Models/ClassModel.php`
**Table**: `classes`
**Traits**: `HasFactory`

**Fillable Fields**:
- course_id, teacher_id, name, start_date, end_date, max_capacity, current_enrollment, status

**Casts**:
- start_date: date
- end_date: date

**Relationships**:
- `belongsTo(Course)` - Parent course
- `belongsTo(Teacher)` - Assigned teacher
- `hasMany(Enrollment)` - Class enrollments
- `hasMany(Schedule)` - Class schedules
- `hasMany(Assessment)` - Class assessments
- `belongsToMany(Student)` through enrollments - Enrolled students
- `hasMany(Feedback)` - Class feedback

---

### 6. Enrollment Model
**File**: `app/Models/Enrollment.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- student_id, class_id, enrollment_date, status, completion_percentage

**Casts**:
- enrollment_date: date
- completion_percentage: decimal:2

**Relationships**:
- `belongsTo(Student)` - Enrolled student
- `belongsTo(ClassModel)` - Enrolled class
- `hasOne(Payment)` - Associated payment

---

### 7. Schedule Model
**File**: `app/Models/Schedule.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- class_id, date, start_time, end_time, location, topic, status

**Casts**:
- date: date

**Relationships**:
- `belongsTo(ClassModel)` - Parent class
- `hasMany(Attendance)` - Attendance records

---

### 8. Attendance Model
**File**: `app/Models/Attendance.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- schedule_id, student_id, status, note, recorded_at

**Casts**:
- recorded_at: datetime

**Relationships**:
- `belongsTo(Schedule)` - Associated schedule
- `belongsTo(Student)` - Student attendance

---

### 9. Assessment Model
**File**: `app/Models/Assessment.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- class_id, name, type, max_score, assessment_date, description

**Casts**:
- max_score: decimal:2
- assessment_date: date

**Relationships**:
- `belongsTo(ClassModel)` - Parent class
- `hasMany(AssessmentScore)` - Student scores

---

### 10. AssessmentScore Model
**File**: `app/Models/AssessmentScore.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- assessment_id, student_id, score, feedback

**Casts**:
- score: decimal:2

**Relationships**:
- `belongsTo(Assessment)` - Parent assessment
- `belongsTo(Student)` - Student who received score

---

### 11. Payment Model
**File**: `app/Models/Payment.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- enrollment_id, amount, payment_method, status, due_date, paid_date, proof_image, note

**Casts**:
- amount: decimal:2
- due_date: date
- paid_date: date

**Relationships**:
- `belongsTo(Enrollment)` - Associated enrollment

---

### 12. Certificate Model
**File**: `app/Models/Certificate.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- student_id, course_id, certificate_number, issue_date, pdf_path

**Casts**:
- issue_date: date

**Relationships**:
- `belongsTo(Student)` - Certificate recipient
- `belongsTo(Course)` - Completed course

---

### 13. Conversation Model
**File**: `app/Models/Conversation.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- student_id, started_at, last_message_at, message_count

**Casts**:
- started_at: datetime
- last_message_at: datetime

**Relationships**:
- `belongsTo(Student)` - Student in conversation
- `hasMany(Message)` - Conversation messages

---

### 14. Message Model
**File**: `app/Models/Message.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- conversation_id, sender_type, content

**Relationships**:
- `belongsTo(Conversation)` - Parent conversation

---

### 15. Notification Model
**File**: `app/Models/Notification.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- user_id, type, title, message, is_read, sent_at

**Casts**:
- is_read: boolean
- sent_at: datetime

**Relationships**:
- `belongsTo(User)` - Notification recipient

---

### 16. Feedback Model
**File**: `app/Models/Feedback.php`
**Traits**: `HasFactory`

**Fillable Fields**:
- student_id, class_id, course_rating, teacher_rating, comment, is_anonymous

**Casts**:
- is_anonymous: boolean

**Relationships**:
- `belongsTo(Student)` - Feedback author
- `belongsTo(ClassModel)` - Feedback target class

---

## Relationship Summary

### One-to-One Relationships
- User → Teacher
- User → Student
- Enrollment → Payment

### One-to-Many Relationships
- User → Notifications
- Teacher → Classes
- Student → Enrollments, Attendances, AssessmentScores, Certificates, Conversations, Feedbacks
- Course → Classes, Certificates
- ClassModel → Enrollments, Schedules, Assessments, Feedbacks
- Schedule → Attendances
- Assessment → AssessmentScores
- Conversation → Messages

### Many-to-Many Relationships
- Student ↔ ClassModel (through enrollments table)

---

## Key Features Implemented

✅ All 16 models created with proper namespace and imports
✅ HasFactory trait added to all models for testing support
✅ All fillable fields defined matching migration schemas
✅ Type casting configured for dates, decimals, and booleans
✅ Complete relationship definitions (hasOne, hasMany, belongsTo, belongsToMany)
✅ Proper foreign key specifications in relationships
✅ Table name override for ClassModel (uses 'classes' table)
✅ All models follow Laravel naming conventions
✅ PHP syntax validated - no errors

---

## Validation Status

All model files passed PHP syntax validation:
```
✓ Assessment.php
✓ AssessmentScore.php
✓ Attendance.php
✓ Certificate.php
✓ ClassModel.php
✓ Conversation.php
✓ Course.php
✓ Enrollment.php
✓ Feedback.php
✓ Message.php
✓ Notification.php
✓ Payment.php
✓ Schedule.php
✓ Student.php
✓ Teacher.php
✓ User.php
```

---

## Next Steps

The data layer foundation is now complete. The models are ready for:
- Service layer implementation (Task 2.3)
- Controller development
- Factory and seeder creation for testing
- Property-based testing implementation

All models align with the design document specifications and database schema from Task 2.1.
