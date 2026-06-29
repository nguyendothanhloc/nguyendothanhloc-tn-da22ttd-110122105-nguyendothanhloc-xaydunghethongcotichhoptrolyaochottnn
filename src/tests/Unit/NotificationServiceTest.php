<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\NotificationService;
use App\Models\Notification;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Teacher;
use App\Jobs\SendEnrollmentConfirmationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = new NotificationService();
    }

    /** @test */
    public function it_can_create_in_app_notification()
    {
        $user = $this->createUser();

        $notification = $this->notificationService->createInAppNotification(
            $user,
            'test_notification',
            'Test Title',
            'Test message content'
        );

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals($user->id, $notification->user_id);
        $this->assertEquals('test_notification', $notification->type);
        $this->assertEquals('Test Title', $notification->title);
        $this->assertEquals('Test message content', $notification->message);
        $this->assertFalse($notification->is_read);
        $this->assertNotNull($notification->sent_at);
    }

    /** @test */
    public function it_can_send_enrollment_confirmation()
    {
        Queue::fake();

        $enrollment = $this->createEnrollment();

        $this->notificationService->sendEnrollmentConfirmation($enrollment);

        // Check that in-app notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $enrollment->student->user->id,
            'type' => 'enrollment_confirmation',
        ]);

        // Check that email job was queued
        Queue::assertPushed(SendEnrollmentConfirmationEmail::class, function ($job) use ($enrollment) {
            return $job->enrollment->id === $enrollment->id;
        });
    }

    /** @test */
    public function it_can_mark_notification_as_read()
    {
        $user = $this->createUser();
        $notification = $this->notificationService->createInAppNotification(
            $user,
            'test',
            'Test',
            'Test message'
        );

        $this->assertFalse($notification->is_read);

        $result = $this->notificationService->markAsRead($notification->id);

        $this->assertTrue($result);
        $notification->refresh();
        $this->assertTrue($notification->is_read);
    }

    /** @test */
    public function it_returns_false_when_marking_nonexistent_notification_as_read()
    {
        $result = $this->notificationService->markAsRead(99999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_get_unread_notifications()
    {
        $user = $this->createUser();
        
        // Create 3 unread notifications
        $this->notificationService->createInAppNotification($user, 'type1', 'Title 1', 'Message 1');
        $this->notificationService->createInAppNotification($user, 'type2', 'Title 2', 'Message 2');
        $this->notificationService->createInAppNotification($user, 'type3', 'Title 3', 'Message 3');

        // Create 1 read notification
        $readNotification = $this->notificationService->createInAppNotification($user, 'type4', 'Title 4', 'Message 4');
        $readNotification->update(['is_read' => true]);

        $unreadNotifications = $this->notificationService->getUnreadNotifications($user->id);

        $this->assertCount(3, $unreadNotifications);
        $this->assertTrue($unreadNotifications->every(fn($n) => !$n->is_read));
    }

    /** @test */
    public function it_can_get_all_user_notifications_with_limit()
    {
        $user = $this->createUser();
        
        // Create 10 notifications
        for ($i = 0; $i < 10; $i++) {
            $this->notificationService->createInAppNotification(
                $user,
                "type{$i}",
                "Title {$i}",
                "Message {$i}"
            );
        }

        // Get notifications with limit of 5
        $notifications = $this->notificationService->getUserNotifications($user->id, 5);

        $this->assertCount(5, $notifications);
    }

    /** @test */
    public function it_sends_notification_with_correct_message_for_enrollment()
    {
        Queue::fake();

        $enrollment = $this->createEnrollment();
        $courseName = $enrollment->class->course->name;
        $className = $enrollment->class->name;

        $this->notificationService->sendEnrollmentConfirmation($enrollment);

        $notification = Notification::where('user_id', $enrollment->student->user->id)->first();

        $this->assertStringContainsString($courseName, $notification->message);
        $this->assertStringContainsString($className, $notification->message);
        $this->assertStringContainsString('successfully enrolled', $notification->message);
    }

    // Helper methods

    protected function createUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Test User',
            'email' => 'user' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ], $attributes));
    }

    protected function createEnrollment(): Enrollment
    {
        // Create course
        $course = Course::create([
            'name' => 'Test Course',
            'description' => 'Test Description',
            'language' => 'English',
            'level' => 'beginner',
            'duration_weeks' => 12,
            'price' => 1000.00,
            'is_active' => true,
        ]);

        // Create teacher
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

        // Create class
        $class = ClassModel::create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'Test Class',
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(90)->toDateString(),
            'max_capacity' => 20,
            'current_enrollment' => 0,
            'status' => 'upcoming',
        ]);

        // Create student
        $studentUser = User::create([
            'name' => 'Test Student',
            'email' => 'student' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'level' => 'beginner',
        ]);

        // Create enrollment
        return Enrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
            'completion_percentage' => 0,
        ]);
    }
}
