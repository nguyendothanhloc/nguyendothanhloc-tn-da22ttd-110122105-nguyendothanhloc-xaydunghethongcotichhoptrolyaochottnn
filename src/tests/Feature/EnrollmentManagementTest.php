<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\ClassModel;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnrollmentManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function student_can_view_their_enrollments()
    {
        $student = $this->createStudentUser();
        $class = $this->createClass();

        // Create an enrollment
        Enrollment::create([
            'student_id' => $student->student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student)
            ->get(route('enrollments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('enrollments.index');
        $response->assertViewHas('enrollments');
    }

    /** @test */
    public function student_can_create_enrollment_with_available_capacity()
    {
        $student = $this->createStudentUser();
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 5]);

        $response = $this->actingAs($student)
            ->post(route('enrollments.store'), [
                'class_id' => $class->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->student->id,
            'class_id' => $class->id,
            'status' => 'pending',
        ]);

        // Verify current_enrollment was incremented
        $class->refresh();
        $this->assertEquals(6, $class->current_enrollment);
    }

    /** @test */
    public function student_cannot_enroll_when_class_is_at_maximum_capacity()
    {
        $student = $this->createStudentUser();
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 10]);

        $response = $this->actingAs($student)
            ->post(route('enrollments.store'), [
                'class_id' => $class->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Class is at maximum capacity');

        $this->assertDatabaseMissing('enrollments', [
            'student_id' => $student->student->id,
            'class_id' => $class->id,
        ]);

        // Verify current_enrollment was not changed
        $class->refresh();
        $this->assertEquals(10, $class->current_enrollment);
    }

    /** @test */
    public function student_cannot_enroll_twice_in_same_class()
    {
        $student = $this->createStudentUser();
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 5]);

        // Create first enrollment
        Enrollment::create([
            'student_id' => $student->student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // Try to create duplicate enrollment
        $response = $this->actingAs($student)
            ->post(route('enrollments.store'), [
                'class_id' => $class->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Student is already enrolled in this class');

        // Verify only one enrollment exists
        $this->assertEquals(1, Enrollment::where('student_id', $student->student->id)
            ->where('class_id', $class->id)
            ->count());
    }

    /** @test */
    public function student_can_view_enrollment_details()
    {
        $student = $this->createStudentUser();
        $class = $this->createClass();

        $enrollment = Enrollment::create([
            'student_id' => $student->student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student)
            ->get(route('enrollments.show', $enrollment->id));

        $response->assertStatus(200);
        $response->assertViewIs('enrollments.show');
        $response->assertViewHas('enrollment');
    }

    /** @test */
    public function student_cannot_view_other_students_enrollment()
    {
        $student1 = $this->createStudentUser();
        $student2 = $this->createStudentUser();
        $class = $this->createClass();

        $enrollment = Enrollment::create([
            'student_id' => $student2->student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student1)
            ->get(route('enrollments.show', $enrollment->id));

        $response->assertRedirect(route('enrollments.index'));
        $response->assertSessionHas('error', 'Unauthorized access');
    }

    /** @test */
    public function student_can_cancel_their_enrollment()
    {
        $student = $this->createStudentUser();
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 6]);

        $enrollment = Enrollment::create([
            'student_id' => $student->student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student)
            ->delete(route('enrollments.destroy', $enrollment->id));

        $response->assertRedirect(route('enrollments.index'));
        $response->assertSessionHas('success');

        // Verify enrollment status was updated
        $enrollment->refresh();
        $this->assertEquals('cancelled', $enrollment->status);

        // Verify current_enrollment was decremented
        $class->refresh();
        $this->assertEquals(5, $class->current_enrollment);
    }

    /** @test */
    public function student_cannot_cancel_other_students_enrollment()
    {
        $student1 = $this->createStudentUser();
        $student2 = $this->createStudentUser();
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 6]);

        $enrollment = Enrollment::create([
            'student_id' => $student2->student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student1)
            ->delete(route('enrollments.destroy', $enrollment->id));

        $response->assertRedirect(route('enrollments.index'));
        $response->assertSessionHas('error', 'Unauthorized access');

        // Verify enrollment was not cancelled
        $enrollment->refresh();
        $this->assertEquals('pending', $enrollment->status);
    }

    /** @test */
    public function non_student_cannot_access_enrollment_routes()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $class = $this->createClass();

        // Test index
        $response = $this->actingAs($admin)
            ->get(route('enrollments.index'));
        $response->assertStatus(403);

        // Test store
        $response = $this->actingAs($admin)
            ->post(route('enrollments.store'), [
                'class_id' => $class->id,
            ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function enrollment_requires_valid_class_id()
    {
        $student = $this->createStudentUser();

        $response = $this->actingAs($student)
            ->post(route('enrollments.store'), [
                'class_id' => 99999, // Non-existent class
            ]);

        $response->assertSessionHasErrors('class_id');
    }

    // Helper methods

    protected function createStudentUser(): User
    {
        $user = User::create([
            'name' => 'Test Student',
            'email' => 'student' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $user->id,
            'level' => 'beginner',
        ]);

        return $user->fresh();
    }

    protected function createClass(array $attributes = []): ClassModel
    {
        $course = Course::create([
            'name' => 'Test Course',
            'description' => 'Test Description',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 12,
            'price' => 1000.00,
            'is_active' => true,
        ]);

        $teacherUser = User::create([
            'name' => 'Test Teacher',
            'email' => 'teacher' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
        ]);

        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'specialization' => 'English',
        ]);

        return ClassModel::create(array_merge([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'Test Class',
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(90)->toDateString(),
            'max_capacity' => 20,
            'current_enrollment' => 0,
            'status' => 'upcoming',
        ], $attributes));
    }
}
