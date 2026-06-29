<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\ClassModel;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseDetailGuestTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_course_detail()
    {
        // Create a course
        $course = Course::factory()->create([
            'name' => 'Test Course',
            'is_active' => true,
        ]);

        // Create a teacher
        $teacherUser = User::factory()->create([
            'role' => 'teacher',
            'name' => 'Test Teacher',
        ]);
        $teacher = Teacher::factory()->create([
            'user_id' => $teacherUser->id,
        ]);

        // Create a class for the course
        ClassModel::factory()->create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'status' => 'upcoming',
        ]);

        // Guest user visits course detail page
        $response = $this->get("/courses/{$course->id}/detail");

        // Should return 200 OK
        $response->assertStatus(200);

        // Should see course name
        $response->assertSee($course->name);

        // Should see test message
        $response->assertSee('TEST');
        $response->assertSee('Trang đang được hiển thị');
    }

    public function test_course_detail_contains_classes()
    {
        // Create a course
        $course = Course::factory()->create([
            'name' => 'English Course',
            'is_active' => true,
        ]);

        // Create a teacher
        $teacherUser = User::factory()->create([
            'role' => 'teacher',
            'name' => 'John Teacher',
        ]);
        $teacher = Teacher::factory()->create([
            'user_id' => $teacherUser->id,
        ]);

        // Create classes
        $class1 = ClassModel::factory()->create([
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'name' => 'Class A',
            'status' => 'upcoming',
        ]);

        $response = $this->get("/courses/{$course->id}/detail");

        $response->assertStatus(200);
        $response->assertSee($class1->name);
        $response->assertSee($teacherUser->name);
    }
}
