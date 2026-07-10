# Requirements Document

## Introduction

Hệ thống quản lý trung tâm ngoại ngữ hiện tại sử dụng điểm số (score) để đánh giá học viên. Tính năng này thay đổi phương pháp đánh giá từ điểm số sang phần trăm hoàn thành (progress completion percentage), cho phép giáo viên đánh giá tiến độ học tập của học viên theo tỷ lệ hoàn thành từ 0-100%, thay vì điểm số tuyệt đối. Điều này phù hợp hơn với các khóa học thực hành, kỹ năng và dự án, nơi mà việc đo lường tiến độ hoàn thành quan trọng hơn điểm số.

## Glossary

- **Assessment**: Bài kiểm tra, bài tập, hoặc dự án dùng để đánh giá học viên
- **Progress_Percentage**: Phần trăm hoàn thành của học viên cho một Assessment, giá trị từ 0 đến 100
- **Assessment_Score**: Bảng lưu trữ kết quả đánh giá của học viên cho Assessment
- **Teacher**: Giáo viên - người nhập phần trăm hoàn thành cho học viên
- **Student**: Học viên - người được đánh giá và xem tiến độ hoàn thành
- **Completion_Threshold**: Ngưỡng phần trăm hoàn thành tối thiểu để đạt điều kiện tốt nghiệp (ví dụ: 80%)
- **Teacher_Interface**: Giao diện giáo viên dùng để nhập Progress_Percentage
- **Student_Interface**: Giao diện học viên dùng để xem Progress_Percentage và tiến độ
- **Progress_Bar**: Thanh tiến độ hiển thị trực quan phần trăm hoàn thành
- **Database_Schema**: Cấu trúc bảng dữ liệu trong hệ thống
- **Enrollment**: Đăng ký học của học viên trong một lớp
- **Graduation_Status**: Trạng thái tốt nghiệp của học viên dựa trên Progress_Percentage

## Requirements

### Requirement 1: Database Schema Migration

**User Story:** As a system administrator, I want the database schema to support progress percentage instead of score values, so that the system can store completion-based assessments.

#### Acceptance Criteria

1. THE Database_Schema SHALL store Progress_Percentage as a decimal value between 0.00 and 100.00 in the assessment_scores table
2. THE Database_Schema SHALL remove the max_score column from the assessments table
3. WHEN a migration is executed, THE Database_Schema SHALL preserve existing score data by converting it to Progress_Percentage using the formula: (score / max_score) * 100
4. THE Database_Schema SHALL maintain referential integrity constraints between assessment_scores, assessments, students, and classes tables
5. WHEN the migration is rolled back, THE Database_Schema SHALL restore the original schema structure

### Requirement 2: Teacher Progress Input Interface

**User Story:** As a teacher, I want to input progress percentage (0-100%) for each student instead of score values, so that I can evaluate student completion more accurately.

#### Acceptance Criteria

1. WHEN a Teacher accesses the assessment score entry page, THE Teacher_Interface SHALL display input fields accepting values from 0 to 100
2. WHEN a Teacher enters a Progress_Percentage value, THE Teacher_Interface SHALL validate that the value is between 0 and 100
3. WHEN a Teacher enters an invalid value (negative, greater than 100, or non-numeric), THE Teacher_Interface SHALL display an error message and prevent form submission
4. THE Teacher_Interface SHALL display the label "Phần trăm hoàn thành (%)" instead of "Điểm"
5. WHEN a Teacher saves Progress_Percentage values, THE Assessment_Score SHALL update the progress_percentage column in the database
6. WHEN a Teacher has previously entered a Progress_Percentage for a Student, THE Teacher_Interface SHALL display the existing value in the input field

### Requirement 3: Student Progress Display Interface

**User Story:** As a student, I want to view my progress percentage with a visual progress bar instead of numeric scores, so that I can understand my completion status more intuitively.

#### Acceptance Criteria

1. WHEN a Student accesses the grades page, THE Student_Interface SHALL display Progress_Percentage values instead of score values
2. WHEN a Student views an Assessment result, THE Student_Interface SHALL display a Progress_Bar showing the Progress_Percentage visually
3. THE Progress_Bar SHALL use color coding: green for Progress_Percentage >= 80%, yellow for 50% <= Progress_Percentage < 80%, red for Progress_Percentage < 50%
4. THE Student_Interface SHALL display the column header "Tiến độ hoàn thành" instead of "Điểm"
5. THE Student_Interface SHALL remove the "Điểm tối đa" column from the grades table
6. WHEN calculating average progress, THE Student_Interface SHALL compute the arithmetic mean of all Progress_Percentage values for a class

### Requirement 4: Graduation Eligibility Validation

**User Story:** As a student, I want the system to determine my graduation eligibility based on my progress percentage, so that I know if I have completed the course requirements.

#### Acceptance Criteria

1. THE Graduation_Status SHALL be determined by comparing the average Progress_Percentage against the Completion_Threshold
2. WHEN a Student's average Progress_Percentage is greater than or equal to the Completion_Threshold, THE Graduation_Status SHALL be set to "eligible"
3. WHEN a Student's average Progress_Percentage is less than the Completion_Threshold, THE Graduation_Status SHALL be set to "not eligible"
4. THE Completion_Threshold SHALL be configurable and default to 80%
5. WHEN calculating Graduation_Status, THE System SHALL only include Assessment records that have a Progress_Percentage value (exclude ungraded assessments)
6. THE Student_Interface SHALL display the Graduation_Status on the grades page

### Requirement 5: Data Model Update

**User Story:** As a developer, I want the Assessment and AssessmentScore models to reflect progress-based evaluation, so that the application logic is consistent with the new schema.

#### Acceptance Criteria

1. THE Assessment model SHALL remove the max_score attribute from the fillable and casts arrays
2. THE AssessmentScore model SHALL rename the score attribute to progress_percentage in the fillable and casts arrays
3. THE AssessmentScore model SHALL cast progress_percentage as a decimal with 2 decimal places
4. WHEN an Assessment is created, THE Teacher_Interface SHALL not require a max_score input
5. WHEN an AssessmentScore is saved, THE System SHALL ensure the progress_percentage value is between 0 and 100

### Requirement 6: Backward Compatibility and Data Migration

**User Story:** As a system administrator, I want existing assessment data to be automatically converted to progress percentages, so that historical records remain accessible and accurate.

#### Acceptance Criteria

1. WHEN the migration is executed on existing data, THE System SHALL convert all existing score values to Progress_Percentage using the formula: (score / max_score) * 100
2. WHEN an existing Assessment has a max_score of 0, THE System SHALL set the Progress_Percentage to 0
3. THE System SHALL log all data conversion operations for audit purposes
4. WHEN the migration completes, THE System SHALL verify that all AssessmentScore records have valid Progress_Percentage values
5. IF any data conversion fails, THE System SHALL rollback the migration and report the error

### Requirement 7: User Interface Label Updates

**User Story:** As a user, I want all interface labels and text to reflect progress-based terminology, so that the system is clear and consistent.

#### Acceptance Criteria

1. THE Teacher_Interface SHALL display "Phần trăm hoàn thành (0-100%)" as the input field label
2. THE Student_Interface SHALL display "Tiến độ hoàn thành" as the column header
3. THE Student_Interface SHALL display "Điểm trung bình" as "Tiến độ trung bình" with percentage notation
4. THE Teacher_Interface SHALL remove all references to "Điểm tối đa" and "max_score"
5. THE Student_Interface SHALL display "Chưa đánh giá" when Progress_Percentage is null

### Requirement 8: Validation Rules Update

**User Story:** As a developer, I want validation rules to enforce progress percentage constraints, so that data integrity is maintained.

#### Acceptance Criteria

1. WHEN a Teacher submits Progress_Percentage values, THE System SHALL validate that each value is numeric
2. WHEN a Teacher submits Progress_Percentage values, THE System SHALL validate that each value is between 0 and 100 inclusive
3. WHEN a Teacher submits Progress_Percentage values, THE System SHALL allow decimal values with up to 2 decimal places
4. IF validation fails, THE System SHALL return an error message specifying which student record failed validation
5. THE System SHALL prevent storing Progress_Percentage values outside the 0-100 range at the database level

## Notes

### Migration Strategy

Việc chuyển đổi từ hệ thống điểm số sang phần trăm hoàn thành yêu cầu:
1. Migration database để thay đổi cấu trúc bảng
2. Chuyển đổi dữ liệu hiện có từ điểm số sang phần trăm
3. Cập nhật models, controllers, và validation rules
4. Cập nhật giao diện người dùng (teacher và student)
5. Testing để đảm bảo tính toàn vẹn dữ liệu

### Color Coding Standards

Progress Bar colors:
- **Green (bg-success)**: >= 80% - Đạt yêu cầu tốt nghiệp
- **Yellow (bg-warning)**: 50-79% - Cần cải thiện
- **Red (bg-danger)**: < 50% - Cần nỗ lực nhiều hơn

### Completion Threshold

Ngưỡng tốt nghiệp mặc định là 80%, nhưng có thể được cấu hình trong tương lai thông qua system settings hoặc per-class configuration.
