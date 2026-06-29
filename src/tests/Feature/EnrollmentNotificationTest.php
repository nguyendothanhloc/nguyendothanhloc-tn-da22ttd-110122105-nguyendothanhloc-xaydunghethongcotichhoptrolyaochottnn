<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\ClassModel;
use App\Models\Teacher;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Jobs\SendEnrollmentConfirmationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class EnrollmentNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function enrollment_creation_sends_notification_and_queues_email()
    {
        Queue::fake();

        // Create a student user and authenticate
        $studentUser = User::factory()->create(['role' => 'student']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'level' => 'beginner',
        ]);

        // Create a course and class
        $course = Course::create([
            'name' => 'English for Beginners',
            'description' => 'Learn basic English',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 12,
            'price' => 1000.00,
            'is_active' => true,
        ]);

        $teacherUser = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'specialization' => 'English',
        ]);

        $class = ClassModel::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'Morning Class',
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(90)->toDateString(),
            'max_capacity' => 20,
            'current_enrollment' => 0,
            'status' => 'upcoming',
        ]);

        // Authenticate as student
        $this->actingAs($studentUser);

        // Post enrollment request
        $response = $this->post(route('enrollments.store'), [
            'class_id' => $class->id,
        ]);

        // Assert enrollment was created
        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'status' => 'pending',
        ]);

        // Assert in-app notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $studentUser->id,
            'type' => 'enrollment_confirmation',
        ]);

        // Assert email job was queued
        Queue::assertPushed(SendEnrollmentConfirmationEmail::class);

        // Verify notification message contains course and class name
        $notification = Notification::where('user_id', $studentUser->id)
            ->where('type', 'enrollment_confirmation')
            ->first();

        $this->assertStringContainsString($course->name, $notification->message);
        $this->assertStringContainsString($class->name, $notification->message);
    }

    /** @test */
    public function notification_is_not_sent_when_enrollment_fails_due_to_capacity()
    {
        Queue::fake();

        // Create a student user
        $studentUser = User::factory()->create(['role' => 'student']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'level' => 'beginner',
        ]);

        // Create a course and class at max capacity
        $course = Course::create([
            'name' => 'English for Beginners',
            'description' => 'Learn basic English',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 12,
            'price' => 1000.00,
            'is_active' => true,
        ]);

        $teacherUser = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'specialization' => 'English',
        ]);

        $class = ClassModel::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'Morning Class',
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(90)->toDateString(),
            'max_capacity' => 1,
            'current_enrollment' => 1, // Already at max capacity
            'status' => 'upcoming',
        ]);

        // Authenticate as student
        $this->actingAs($studentUser);

        // Try to enroll
        $response = $this->post(route('enrollments.store'), [
            'class_id' => $class->id,
        ]);

        // Assert enrollment was not created
        $this->assertDatabaseMissing('enrollments', [
            'student_id' => $student->id,
            'class_id' => $class->id,
        ]);

        // Assert no notification was created
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $studentUser->id,
            'type' => 'enrollment_confirmation',
        ]);

        // Assert no email job was queued
        Queue::assertNotPushed(SendEnrollmentConfirmationEmail::class);
    }

    /** @test */
    public function notification_contains_all_required_information()
    {
        Queue::fake();

        // Create test data
        $studentUser = User::factory()->create(['role' => 'student']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'level' => 'beginner',
        ]);

        $course = Course::create([
            'name' => 'Spanish Intermediate',
            'description' => 'Intermediate Spanish course',
            'language' => 'Spanish',
            'level' => 'intermediate',
            'duration_weeks' => 10,
            'price' => 1500.00,
            'is_active' => true,
        ]);

        $teacherUser = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'specialization' => 'Spanish',
        ]);

        $class = ClassModel::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'Evening Class',
            'start_date' => now()->addDays(14)->toDateString(),
            'end_date' => now()->addDays(100)->toDateString(),
            'max_capacity' => 15,
            'current_enrollment' => 5,
            'status' => 'upcoming',
        ]);

        // Authenticate and enroll
        $this->actingAs($studentUser);
        $this->post(route('enrollments.store'), [
            'class_id' => $class->id,
        ]);

        // Get the notification
        $notification = Notification::where('user_id', $studentUser->id)
            ->where('type', 'enrollment_confirmation')
            ->first();

        // Assert notification has all required information
        $this->assertNotNull($notification);
        $this->assertEquals('Enrollment Confirmation', $notification->title);
        $this->assertStringContainsString($course->name, $notification->message);
        $this->assertStringContainsString($class->name, $notification->message);
        $this->assertStringContainsString('successfully enrolled', $notification->message);
        $this->assertStringContainsString('payment', $notification->message);
        $this->assertFalse($notification->is_read);
        $this->assertNotNull($notification->sent_at);
    }
}
