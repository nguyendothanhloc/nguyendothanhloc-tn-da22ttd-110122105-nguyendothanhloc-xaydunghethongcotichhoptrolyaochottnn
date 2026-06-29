<?php

namespace App\Services;

use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClassService
{
    /**
     * Get all classes with pagination
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllClasses(int $perPage = 10)
    {
        return ClassModel::with(['course', 'teacher.user'])
            ->orderBy('start_date', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get classes for a specific teacher
     *
     * @param int $teacherId
     * @return Collection
     */
    public function getTeacherClasses(int $teacherId): Collection
    {
        return ClassModel::with(['course', 'enrollments.student.user'])
            ->where('teacher_id', $teacherId)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Get a single class by ID with relationships
     *
     * @param int $id
     * @return ClassModel|null
     */
    public function getClassById(int $id): ?ClassModel
    {
        return ClassModel::with(['course', 'teacher.user', 'enrollments.student.user'])
            ->find($id);
    }

    /**
     * Validate class data
     *
     * @param array $data
     * @throws ValidationException
     */
    protected function validateClassData(array $data): void
    {
        // Validate start_date < end_date
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $startDate = is_string($data['start_date']) ? 
                \Carbon\Carbon::parse($data['start_date']) : $data['start_date'];
            $endDate = is_string($data['end_date']) ? 
                \Carbon\Carbon::parse($data['end_date']) : $data['end_date'];
            
            if ($startDate->greaterThanOrEqualTo($endDate)) {
                throw ValidationException::withMessages([
                    'end_date' => ['Ngày kết thúc phải sau ngày bắt đầu.']
                ]);
            }
        }

        // Validate max_capacity > 0
        if (isset($data['max_capacity']) && $data['max_capacity'] <= 0) {
            throw ValidationException::withMessages([
                'max_capacity' => ['Sức chứa tối đa phải lớn hơn 0.']
            ]);
        }

        // Validate teacher_id exists and is active
        if (isset($data['teacher_id'])) {
            $teacher = Teacher::with('user')->find($data['teacher_id']);
            if (!$teacher) {
                throw ValidationException::withMessages([
                    'teacher_id' => ['Giáo viên không tồn tại.']
                ]);
            }
            if (!$teacher->user || !$teacher->user->is_active) {
                throw ValidationException::withMessages([
                    'teacher_id' => ['Giáo viên này không khả dụng.']
                ]);
            }
        }

        // Validate course_id exists and is active
        if (isset($data['course_id'])) {
            $course = Course::find($data['course_id']);
            if (!$course) {
                throw ValidationException::withMessages([
                    'course_id' => ['Khóa học không tồn tại.']
                ]);
            }
            if (!$course->is_active) {
                throw ValidationException::withMessages([
                    'course_id' => ['Khóa học này không khả dụng.']
                ]);
            }
        }
    }

    /**
     * Create a new class
     *
     * @param array $data
     * @return ClassModel
     * @throws ValidationException
     */
    public function createClass(array $data): ClassModel
    {
        // Validate business rules
        $this->validateClassData($data);

        return DB::transaction(function () use ($data) {
            // Set default values
            $data['status'] = $data['status'] ?? 'upcoming';
            $data['current_enrollment'] = 0;

            return ClassModel::create($data);
        });
    }

    /**
     * Update an existing class
     *
     * @param int $id
     * @param array $data
     * @return ClassModel
     * @throws ValidationException
     */
    public function updateClass(int $id, array $data): ClassModel
    {
        // Validate business rules
        $this->validateClassData($data);

        return DB::transaction(function () use ($id, $data) {
            $class = ClassModel::findOrFail($id);
            
            // Prevent updating max_capacity below current_enrollment
            if (isset($data['max_capacity']) && $data['max_capacity'] < $class->current_enrollment) {
                throw ValidationException::withMessages([
                    'max_capacity' => ['Sức chứa tối đa không thể nhỏ hơn số học viên hiện tại (' . $class->current_enrollment . ').']
                ]);
            }

            $class->update($data);
            return $class->fresh();
        });
    }

    /**
     * Delete a class
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteClass(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $class = ClassModel::findOrFail($id);
            
            // Check if class has enrollments
            if ($class->current_enrollment > 0) {
                throw new \Exception('Không thể xóa lớp học đã có học viên đăng ký!');
            }

            return $class->delete();
        });
    }

    /**
     * Get available slots for a class
     *
     * @param int $classId
     * @return int
     */
    public function getAvailableSlots(int $classId): int
    {
        $class = ClassModel::findOrFail($classId);
        return max(0, $class->max_capacity - $class->current_enrollment);
    }

    /**
     * Check if a class is full
     *
     * @param int $classId
     * @return bool
     */
    public function isClassFull(int $classId): bool
    {
        $class = ClassModel::findOrFail($classId);
        return $class->current_enrollment >= $class->max_capacity;
    }

    /**
     * Get active courses for class creation
     *
     * @return Collection
     */
    public function getActiveCourses(): Collection
    {
        return Course::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active teachers for class assignment
     *
     * @return Collection
     */
    public function getActiveTeachers(): Collection
    {
        return Teacher::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->get();
    }

    /**
     * Update class status
     *
     * @param int $id
     * @param string $status
     * @return ClassModel
     */
    public function updateClassStatus(int $id, string $status): ClassModel
    {
        $validStatuses = ['upcoming', 'ongoing', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException('Trạng thái không hợp lệ.');
        }

        $class = ClassModel::findOrFail($id);
        $class->update(['status' => $status]);
        
        return $class->fresh();
    }
}
