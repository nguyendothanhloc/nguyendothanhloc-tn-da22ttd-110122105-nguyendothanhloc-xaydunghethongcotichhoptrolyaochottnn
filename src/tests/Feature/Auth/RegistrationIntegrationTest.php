<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_student_registration_flow(): void
    {
        // Step 1: Visit registration page
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Choose Your Role');

        // Step 2: Navigate to student registration
        $response = $this->get('/register/student');
        $response->assertStatus(200);
        $response->assertSee('Student Registration');

        // Step 3: Submit registration form
        $response = $this->post('/register/student', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123',
            'phone' => '1234567890',
            'level' => 'intermediate',
            'interests' => 'Business English and IELTS preparation',
        ]);

        // Step 4: Verify redirect to dashboard
        $response->assertRedirect(route('dashboard'));

        // Step 5: Verify user is authenticated
        $this->assertAuthenticated();

        // Step 6: Verify user data in database
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('student', $user->role);
        $this->assertTrue($user->is_active);

        // Step 7: Verify student profile was created
        $student = $user->student;
        $this->assertNotNull($student);
        $this->assertEquals('intermediate', $student->level);
        $this->assertEquals('Business English and IELTS preparation', $student->interests);

        // Step 8: Verify relationships work
        $this->assertEquals($user->id, $student->user_id);
        $this->assertEquals($student->id, $user->student->id);
    }

    public function test_complete_teacher_registration_flow(): void
    {
        // Step 1: Visit teacher registration
        $response = $this->get('/register/teacher');
        $response->assertStatus(200);
        $response->assertSee('Teacher Registration');

        // Step 2: Submit registration form
        $response = $this->post('/register/teacher', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => 'TeacherPass456',
            'password_confirmation' => 'TeacherPass456',
            'phone' => '0987654321',
            'specialization' => 'English Language Teaching',
            'qualifications' => 'MA in TESOL, CELTA Certified',
            'bio' => '15 years of teaching experience in ESL',
        ]);

        // Step 3: Verify redirect and authentication
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        // Step 4: Verify user and teacher profile
        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('teacher', $user->role);

        $teacher = $user->teacher;
        $this->assertNotNull($teacher);
        $this->assertEquals('English Language Teaching', $teacher->specialization);
        $this->assertEquals('MA in TESOL, CELTA Certified', $teacher->qualifications);
        $this->assertEquals('15 years of teaching experience in ESL', $teacher->bio);
    }

    public function test_multiple_users_can_register_with_different_roles(): void
    {
        // Register a student
        $this->post('/register/student', [
            'name' => 'Student One',
            'email' => 'student1@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'level' => 'beginner',
        ]);

        $this->post('/logout');

        // Register a teacher
        $this->post('/register/teacher', [
            'name' => 'Teacher One',
            'email' => 'teacher1@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->post('/logout');

        // Register an admin
        $this->post('/register/admin', [
            'name' => 'Admin One',
            'email' => 'admin1@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Verify all users exist with correct roles
        $this->assertEquals(3, User::count());
        $this->assertEquals(1, Student::count());
        $this->assertEquals(1, Teacher::count());

        $student = User::where('email', 'student1@example.com')->first();
        $teacher = User::where('email', 'teacher1@example.com')->first();
        $admin = User::where('email', 'admin1@example.com')->first();

        $this->assertEquals('student', $student->role);
        $this->assertEquals('teacher', $teacher->role);
        $this->assertEquals('admin', $admin->role);

        $this->assertNotNull($student->student);
        $this->assertNotNull($teacher->teacher);
        $this->assertNull($admin->student);
        $this->assertNull($admin->teacher);
    }

    public function test_registration_form_cross_links_work(): void
    {
        // Student form should link to teacher registration
        $response = $this->get('/register/student');
        $response->assertSee('Register as Teacher');
        $response->assertSee(route('register.teacher'));

        // Teacher form should link to student registration
        $response = $this->get('/register/teacher');
        $response->assertSee('Register as Student');
        $response->assertSee(route('register.student'));
    }

    public function test_user_relationships_are_properly_established(): void
    {
        // Create a student
        $this->post('/register/student', [
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'level' => 'advanced',
        ]);

        $user = User::where('email', 'student@test.com')->first();
        
        // Test User -> Student relationship
        $this->assertInstanceOf(Student::class, $user->student);
        
        // Test Student -> User relationship
        $this->assertInstanceOf(User::class, $user->student->user);
        
        // Test bidirectional consistency
        $this->assertEquals($user->id, $user->student->user->id);
    }
}
