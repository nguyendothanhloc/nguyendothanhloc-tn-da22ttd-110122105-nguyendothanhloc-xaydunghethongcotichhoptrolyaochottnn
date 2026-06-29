<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CourseService $courseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->courseService = new CourseService();
    }

    /**
     * Test getting all active courses
     */
    public function test_get_active_courses_returns_only_active_courses(): void
    {
        // Create active and inactive courses
        Course::factory()->create(['is_active' => true, 'name' => 'Active Course 1']);
        Course::factory()->create(['is_active' => true, 'name' => 'Active Course 2']);
        Course::factory()->create(['is_active' => false, 'name' => 'Inactive Course']);

        $activeCourses = $this->courseService->getActiveCourses();

        $this->assertCount(2, $activeCourses);
        $this->assertTrue($activeCourses->every(fn($course) => $course->is_active === true));
    }

    /**
     * Test getting all courses including inactive
     */
    public function test_get_all_courses_returns_all_courses(): void
    {
        Course::factory()->create(['is_active' => true]);
        Course::factory()->create(['is_active' => false]);

        $allCourses = $this->courseService->getAllCourses();

        $this->assertCount(2, $allCourses);
    }

    /**
     * Test getting a course by ID
     */
    public function test_get_course_by_id_returns_correct_course(): void
    {
        $course = Course::factory()->create(['name' => 'Test Course']);

        $foundCourse = $this->courseService->getCourseById($course->id);

        $this->assertNotNull($foundCourse);
        $this->assertEquals('Test Course', $foundCourse->name);
        $this->assertEquals($course->id, $foundCourse->id);
    }

    /**
     * Test getting a non-existent course returns null
     */
    public function test_get_course_by_id_returns_null_for_non_existent_course(): void
    {
        $foundCourse = $this->courseService->getCourseById(999);

        $this->assertNull($foundCourse);
    }

    /**
     * Test creating a new course
     */
    public function test_create_course_stores_course_with_correct_data(): void
    {
        $courseData = [
            'name' => 'English for Beginners',
            'description' => 'A comprehensive English course',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 12,
            'price' => 2000000,
        ];

        $course = $this->courseService->createCourse($courseData);

        $this->assertDatabaseHas('courses', [
            'name' => 'English for Beginners',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 12,
            'price' => 2000000,
        ]);

        $this->assertEquals('English for Beginners', $course->name);
        $this->assertEquals('beginner', $course->level);
    }

    /**
     * Test updating a course
     */
    public function test_update_course_modifies_course_data(): void
    {
        $course = Course::factory()->create([
            'name' => 'Original Name',
            'price' => 1000000,
        ]);

        $updatedCourse = $this->courseService->updateCourse($course->id, [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'language' => 'English',
            'level' => 'intermediate',
            'duration_weeks' => 10,
            'price' => 1500000,
        ]);

        $this->assertEquals('Updated Name', $updatedCourse->name);
        $this->assertEquals(1500000, $updatedCourse->price);
        $this->assertEquals($course->id, $updatedCourse->id); // ID should remain the same
    }

    /**
     * Test deactivating a course
     */
    public function test_deactivate_course_sets_is_active_to_false(): void
    {
        $course = Course::factory()->create([
            'is_active' => true,
            'name' => 'Active Course',
        ]);

        $deactivatedCourse = $this->courseService->deactivateCourse($course->id);

        $this->assertFalse($deactivatedCourse->is_active);
        $this->assertEquals('Active Course', $deactivatedCourse->name); // Other fields unchanged
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test validate class capacity with positive value
     */
    public function test_validate_class_capacity_returns_true_for_positive_capacity(): void
    {
        $course = Course::factory()->create();

        $result = $this->courseService->validateClassCapacity($course->id, 20);

        $this->assertTrue($result);
    }

    /**
     * Test validate class capacity with zero value
     */
    public function test_validate_class_capacity_returns_false_for_zero_capacity(): void
    {
        $course = Course::factory()->create();

        $result = $this->courseService->validateClassCapacity($course->id, 0);

        $this->assertFalse($result);
    }

    /**
     * Test validate class capacity with negative value
     */
    public function test_validate_class_capacity_returns_false_for_negative_capacity(): void
    {
        $course = Course::factory()->create();

        $result = $this->courseService->validateClassCapacity($course->id, -5);

        $this->assertFalse($result);
    }
}
