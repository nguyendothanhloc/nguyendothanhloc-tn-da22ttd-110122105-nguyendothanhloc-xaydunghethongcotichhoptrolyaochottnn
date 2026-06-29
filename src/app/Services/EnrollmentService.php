<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all enrollments for a student
     *
     * @param int $studentId
     * @return Collection
     */
    public function getStudentEnrollments(int $studentId): Collection
    {
        return Enrollment::where('student_id', $studentId)
            ->with(['class.course', 'class.teacher.user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all enrollments for a class
     *
     * @param int $classId
     * @return Collection
     */
    public function getClassEnrollments(int $classId): Collection
    {
        return Enrollment::where('class_id', $classId)
            ->with(['student.user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if a class has available capacity
     *
     * @param int $classId
     * @return bool
     */
    public function checkCapacity(int $classId): bool
    {
        $class = ClassModel::findOrFail($classId);
        return $class->current_enrollment < $class->max_capacity;
    }

    /**
     * Check if a student is already enrolled in a class
     *
     * @param int $studentId
     * @param int $classId
     * @return bool
     */
    public function isAlreadyEnrolled(int $studentId, int $classId): bool
    {
        return Enrollment::where('student_id', $studentId)
            ->where('class_id', $classId)
            ->exists();
    }

    /**
     * Create a new enrollment
     *
     * @param array $data
     * @return Enrollment
     * @throws \Exception
     */
    public function createEnrollment(array $data): Enrollment
    {
        return DB::transaction(function () use ($data) {
            $studentId = $data['student_id'];
            $classId = $data['class_id'];

            // Check if student is already enrolled
            if ($this->isAlreadyEnrolled($studentId, $classId)) {
                throw new \Exception('Student is already enrolled in this class');
            }

            // Check capacity
            if (!$this->checkCapacity($classId)) {
                throw new \Exception('Class is at maximum capacity');
            }

            // Create enrollment with 'paid' status (auto-approved, no admin approval needed)
            $enrollment = Enrollment::create([
                'student_id' => $studentId,
                'class_id' => $classId,
                'enrollment_date' => $data['enrollment_date'] ?? now()->toDateString(),
                'status' => $data['status'] ?? 'paid', // Changed from 'pending' to 'paid' for auto-approval
                'completion_percentage' => 0,
            ]);

            // Increment current_enrollment
            $class = ClassModel::findOrFail($classId);
            $class->increment('current_enrollment');

            // Send enrollment confirmation notification
            $enrollment->load(['student.user', 'class.course', 'class.teacher.user']);
            $this->notificationService->sendEnrollmentConfirmation($enrollment);

            return $enrollment->fresh();
        });
    }

    /**
     * Update an enrollment
     *
     * @param int $id
     * @param array $data
     * @return Enrollment
     */
    public function updateEnrollment(int $id, array $data): Enrollment
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update($data);
        return $enrollment->fresh();
    }

    /**
     * Cancel an enrollment
     *
     * @param int $id
     * @return Enrollment
     */
    public function cancelEnrollment(int $id): Enrollment
    {
        return DB::transaction(function () use ($id) {
            $enrollment = Enrollment::findOrFail($id);
            
            // Update enrollment status
            $enrollment->update(['status' => 'cancelled']);

            // Decrement current_enrollment
            $class = ClassModel::findOrFail($enrollment->class_id);
            $class->decrement('current_enrollment');

            return $enrollment->fresh();
        });
    }

    /**
     * Get enrollment by ID
     *
     * @param int $id
     * @return Enrollment|null
     */
    public function getEnrollmentById(int $id): ?Enrollment
    {
        return Enrollment::with(['student.user', 'class.course', 'class.teacher.user'])
            ->find($id);
    }
}
