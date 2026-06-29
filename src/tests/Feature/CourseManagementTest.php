<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->student = User::factory()->create(['role' => 'student']);
    }

    /**
     * Test admin can view courses index
     */
    public function test_admin_can_view_courses_index(): void
    {
        Course::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('courses.index'));

        $response->assertStatus(200);
        $response->assertViewIs('courses.index');
        $response->assertViewHas('courses');
    }

    /**
     * Test non-admin cannot access courses index
     */
    public function test_non_admin_cannot_access_courses_index(): void
    {
        $response = $this->actingAs($this->student)->get(route('courses.index'));

        $response->assertStatus(403);
    }

    /**
     * Test admin can view create course form
     */
    public function test_admin_can_view_create_course_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('courses.create'));

        $response->assertStatus(200);
        $response->assertViewIs('courses.create');
    }

    /**
     * Test admin can create a course with valid data
     */
    public function test_admin_can_create_course_with_valid_data(): void
    {
        $courseData = [
            'name' => 'English for Beginners',
            'description' => 'A comprehensive English course',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 12,
            'price' => 2000000,
        ];

        $response = $this->actingAs($this->admin)->post(route('courses.store'), $courseData);

        $response->assertRedirect(route('courses.index'));
        $response->assertSessionHas('success', 'Khóa học đã được tạo thành công');

        $this->assertDatabaseHas('courses', [
            'name' => 'English for Beginners',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 12,
            'price' => 2000000,
            'is_active' => true,
        ]);
    }

    /**
     * Test course creation fails without required fields
     */
    public function test_course_creation_fails_without_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('courses.store'), []);

        $response->assertSessionHasErrors(['name', 'language', 'level', 'duration_weeks', 'price']);
    }

    /**
     * Test course creation fails with invalid level
     */
    public function test_course_creation_fails_with_invalid_level(): void
    {
        $courseData = [
            'name' => 'Test Course',
            'language' => 'English',
            'level' => 'invalid_level',
            'duration_weeks' => 12,
            'price' => 2000000,
        ];

        $response = $this->actingAs($this->admin)->post(route('courses.store'), $courseData);

        $response->assertSessionHasErrors(['level']);
    }

    /**
     * Test course creation fails with negative price
     */
    public function test_course_creation_fails_with_negative_price(): void
    {
        $courseData = [
            'name' => 'Test Course',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 12,
            'price' => -1000,
        ];

        $response = $this->actingAs($this->admin)->post(route('courses.store'), $courseData);

        $response->assertSessionHasErrors(['price']);
    }

    /**
     * Test course creation fails with zero duration
     */
    public function test_course_creation_fails_with_zero_duration(): void
    {
        $courseData = [
            'name' => 'Test Course',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 0,
            'price' => 2000000,
        ];

        $response = $this->actingAs($this->admin)->post(route('courses.store'), $courseData);

        $response->assertSessionHasErrors(['duration_weeks']);
    }

    /**
     * Test admin can view edit course form
     */
    public function test_admin_can_view_edit_course_form(): void
    {
        $course = Course::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('courses.edit', $course->id));

        $response->assertStatus(200);
        $response->assertViewIs('courses.edit');
        $response->assertViewHas('course', $course);
    }

    /**
     * Test admin can update a course
     */
    public function test_admin_can_update_course(): void
    {
        $course = Course::factory()->create([
            'name' => 'Original Name',
            'price' => 1000000,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'language' => 'English',
            'level' => 'intermediate',
            'duration_weeks' => 10,
            'price' => 1500000,
        ];

        $response = $this->actingAs($this->admin)->put(route('courses.update', $course->id), $updateData);

        $response->assertRedirect(route('courses.index'));
        $response->assertSessionHas('success', 'Khóa học đã được cập nhật thành công');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Updated Name',
            'price' => 1500000,
        ]);
    }

    /**
     * Test course update preserves ID
     */
    public function test_course_update_preserves_id(): void
    {
        $course = Course::factory()->create();
        $originalId = $course->id;

        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'language' => 'English',
            'level' => 'intermediate',
            'duration_weeks' => 10,
            'price' => 1500000,
        ];

        $this->actingAs($this->admin)->put(route('courses.update', $course->id), $updateData);

        $updatedCourse = Course::where('name', 'Updated Name')->first();
        $this->assertEquals($originalId, $updatedCourse->id);
    }

    /**
     * Test admin can deactivate a course
     */
    public function test_admin_can_deactivate_course(): void
    {
        $course = Course::factory()->create([
            'is_active' => true,
            'name' => 'Active Course',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('courses.deactivate', $course->id));

        $response->assertRedirect(route('courses.index'));
        $response->assertSessionHas('success', 'Khóa học đã được vô hiệu hóa');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'is_active' => false,
            'name' => 'Active Course', // Other fields should remain unchanged
        ]);
    }

    /**
     * Test deactivation only changes is_active flag
     */
    public function test_deactivation_only_changes_is_active_flag(): void
    {
        $course = Course::factory()->create([
            'is_active' => true,
            'name' => 'Test Course',
            'price' => 2000000,
            'level' => 'beginner',
        ]);

        $this->actingAs($this->admin)->patch(route('courses.deactivate', $course->id));

        $deactivatedCourse = Course::find($course->id);
        
        $this->assertFalse($deactivatedCourse->is_active);
        $this->assertEquals('Test Course', $deactivatedCourse->name);
        $this->assertEquals(2000000, $deactivatedCourse->price);
        $this->assertEquals('beginner', $deactivatedCourse->level);
    }

    /**
     * Test course creation accepts price of zero
     */
    public function test_course_creation_accepts_zero_price(): void
    {
        $courseData = [
            'name' => 'Free Course',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 4,
            'price' => 0,
        ];

        $response = $this->actingAs($this->admin)->post(route('courses.store'), $courseData);

        $response->assertRedirect(route('courses.index'));
        $this->assertDatabaseHas('courses', [
            'name' => 'Free Course',
            'price' => 0,
        ]);
    }

    /**
     * Test course creation with minimum duration
     */
    public function test_course_creation_with_minimum_duration(): void
    {
        $courseData = [
            'name' => 'Short Course',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 1,
            'price' => 500000,
        ];

        $response = $this->actingAs($this->admin)->post(route('courses.store'), $courseData);

        $response->assertRedirect(route('courses.index'));
        $this->assertDatabaseHas('courses', [
            'name' => 'Short Course',
            'duration_weeks' => 1,
        ]);
    }
}
