<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\EnrollmentService;
use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Notification;
use App\Jobs\SendEnrollmentConfirmationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class EnrollmentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $enrollmentService;

    protected function setUp(): void
    {
        parent::setUp();
        $notificationService = app(\App\Services\NotificationService::class);
        $this->enrollmentService = new EnrollmentService($notificationService);
    }

    /** @test */
    public function it_can_check_if_class_has_available_capacity()
    {
        // Create a class with capacity
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 5]);

        $hasCapacity = $this->enrollmentService->checkCapacity($class->id);

        $this->assertTrue($hasCapacity);
    }

    /** @test */
    public function it_returns_false_when_class_is_at_maximum_capacity()
    {
        // Create a class at maximum capacity
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 10]);

        $hasCapacity = $this->enrollmentService->checkCapacity($class->id);

        $this->assertFalse($hasCapacity);
    }

    /** @test */
    public function it_can_check_if_student_is_already_enrolled()
    {
        $student = $this->createStudent();
        $class = $this->createClass();
        
        // Create an enrollment
        Enrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $isEnrolled = $this->enrollmentService->isAlreadyEnrolled($student->id, $class->id);

        $this->assertTrue($isEnrolled);
    }

    /** @test */
    public function it_can_create_enrollment_with_available_capacity()
    {
        $student = $this->createStudent();
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 5]);

        $enrollment = $this->enrollmentService->createEnrollment([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(Enrollment::class, $enrollment);
        $this->assertEquals($student->id, $enrollment->student_id);
        $this->assertEquals($class->id, $enrollment->class_id);
        $this->assertEquals('pending', $enrollment->status);

        // Check that current_enrollment was incremented
        $class->refresh();
        $this->assertEquals(6, $class->current_enrollment);
    }

    /** @test */
    public function it_throws_exception_when_creating_enrollment_at_maximum_capacity()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Class is at maximum capacity');

        $student = $this->createStudent();
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 10]);

        $this->enrollmentService->createEnrollment([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_throws_exception_when_student_already_enrolled()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Student is already enrolled in this class');

        $student = $this->createStudent();
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 5]);

        // Create first enrollment
        Enrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // Try to create duplicate enrollment
        $this->enrollmentService->createEnrollment([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_can_cancel_enrollment_and_decrement_current_enrollment()
    {
        $student = $this->createStudent();
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 6]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $cancelledEnrollment = $this->enrollmentService->cancelEnrollment($enrollment->id);

        $this->assertEquals('cancelled', $cancelledEnrollment->status);

        // Check that current_enrollment was decremented
        $class->refresh();
        $this->assertEquals(5, $class->current_enrollment);
    }

    /** @test */
    public function it_can_get_student_enrollments()
    {
        $student = $this->createStudent();
        $class1 = $this->createClass();
        $class2 = $this->createClass();

        Enrollment::create([
            'student_id' => $student->id,
            'class_id' => $class1->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        Enrollment::create([
            'student_id' => $student->id,
            'class_id' => $class2->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'paid',
        ]);

        $enrollments = $this->enrollmentService->getStudentEnrollments($student->id);

        $this->assertCount(2, $enrollments);
    }

    /** @test */
    public function it_can_get_class_enrollments()
    {
        $student1 = $this->createStudent();
        $student2 = $this->createStudent();
        $class = $this->createClass();

        Enrollment::create([
            'student_id' => $student1->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        Enrollment::create([
            'student_id' => $student2->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'paid',
        ]);

        $enrollments = $this->enrollmentService->getClassEnrollments($class->id);

        $this->assertCount(2, $enrollments);
    }

    /** @test */
    public function it_sends_notification_when_enrollment_is_created()
    {
        Queue::fake();

        $student = $this->createStudent();
        $class = $this->createClass(['max_capacity' => 10, 'current_enrollment' => 5]);

        $enrollment = $this->enrollmentService->createEnrollment([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // Check that in-app notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->user->id,
            'type' => 'enrollment_confirmation',
        ]);

        // Check that email job was queued
        Queue::assertPushed(SendEnrollmentConfirmationEmail::class, function ($job) use ($enrollment) {
            return $job->enrollment->id === $enrollment->id;
        });
    }

    // Helper methods

    protected function createStudent(array $attributes = []): Student
    {
        $user = User::create([
            'name' => 'Test Student',
            'email' => 'student' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        return Student::create(array_merge([
            'user_id' => $user->id,
            'level' => 'beginner',
        ], $attributes));
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
