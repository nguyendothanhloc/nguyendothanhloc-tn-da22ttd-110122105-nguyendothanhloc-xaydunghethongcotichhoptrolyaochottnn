<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class CourseService
{
    /**
     * Get all active courses
     *
     * @return Collection
     */
    public function getActiveCourses(): Collection
    {
        return Course::where('is_active', true)->get();
    }

    /**
     * Get all courses (including inactive) with optional filters
     *
     * @param array $filters
     * @return Collection
     */
    public function getAllCourses(array $filters = []): Collection
    {
        $query = Course::query();

        // Filter by language
        if (!empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        // Filter by level
        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Load active classes count for student browsing
        $query->withCount(['classes' => function ($q) {
            $q->whereIn('status', ['upcoming', 'ongoing']);
        }]);

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get a single course by ID
     *
     * @param int $id
     * @return Course|null
     */
    public function getCourseById(int $id): ?Course
    {
        return Course::find($id);
    }

    /**
     * Create a new course
     *
     * @param array $data
     * @return Course
     */
    public function createCourse(array $data): Course
    {
        return Course::create($data);
    }

    /**
     * Update an existing course
     *
     * @param int $id
     * @param array $data
     * @return Course
     */
    public function updateCourse(int $id, array $data): Course
    {
        $course = Course::findOrFail($id);
        $course->update($data);
        return $course->fresh();
    }

    /**
     * Deactivate a course (soft delete via is_active flag)
     *
     * @param int $id
     * @return Course
     */
    public function deactivateCourse(int $id): Course
    {
        $course = Course::findOrFail($id);
        $course->update(['is_active' => false]);
        return $course->fresh();
    }

    /**
     * Validate class capacity for a course
     *
     * @param int $courseId
     * @param int $requestedCapacity
     * @return bool
     */
    public function validateClassCapacity(int $courseId, int $requestedCapacity): bool
    {
        return $requestedCapacity > 0;
    }
}
