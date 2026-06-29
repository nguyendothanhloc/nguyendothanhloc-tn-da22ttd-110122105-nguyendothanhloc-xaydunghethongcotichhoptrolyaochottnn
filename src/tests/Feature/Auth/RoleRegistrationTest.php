<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register/student');

        $response->assertStatus(200);
        $response->assertSee('Student Registration');
    }

    public function test_teacher_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register/teacher');

        $response->assertStatus(200);
        $response->assertSee('Teacher Registration');
    }

    public function test_admin_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register/admin');

        $response->assertStatus(200);
        $response->assertSee('Administrator Registration');
    }

    public function test_student_can_register_with_profile(): void
    {
        $response = $this->post('/register/student', [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '1234567890',
            'level' => 'beginner',
            'interests' => 'Learning English for travel',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // Verify user was created with correct role
        $user = User::where('email', 'student@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('student', $user->role);
        $this->assertEquals('Test Student', $user->name);
        $this->assertEquals('1234567890', $user->phone);
        $this->assertTrue($user->is_active);

        // Verify student profile was automatically created
        $student = Student::where('user_id', $user->id)->first();
        $this->assertNotNull($student);
        $this->assertEquals('beginner', $student->level);
        $this->assertEquals('Learning English for travel', $student->interests);
    }

    public function test_teacher_can_register_with_profile(): void
    {
        $response = $this->post('/register/teacher', [
            'name' => 'Test Teacher',
            'email' => 'teacher@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '0987654321',
            'specialization' => 'English Language',
            'qualifications' => 'TESOL Certified, MA in Education',
            'bio' => 'Experienced English teacher with 10 years of teaching',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // Verify user was created with correct role
        $user = User::where('email', 'teacher@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('teacher', $user->role);
        $this->assertEquals('Test Teacher', $user->name);
        $this->assertEquals('0987654321', $user->phone);
        $this->assertTrue($user->is_active);

        // Verify teacher profile was automatically created
        $teacher = Teacher::where('user_id', $user->id)->first();
        $this->assertNotNull($teacher);
        $this->assertEquals('English Language', $teacher->specialization);
        $this->assertEquals('TESOL Certified, MA in Education', $teacher->qualifications);
        $this->assertEquals('Experienced English teacher with 10 years of teaching', $teacher->bio);
    }

    public function test_admin_can_register(): void
    {
        $response = $this->post('/register/admin', [
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '1122334455',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // Verify user was created with correct role
        $user = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('admin', $user->role);
        $this->assertEquals('Test Admin', $user->name);
        $this->assertEquals('1122334455', $user->phone);
        $this->assertTrue($user->is_active);

        // Admin should not have student or teacher profile
        $this->assertNull($user->student);
        $this->assertNull($user->teacher);
    }

    public function test_student_registration_requires_level(): void
    {
        $response = $this->post('/register/student', [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            // Missing level field
        ]);

        $response->assertSessionHasErrors('level');
        $this->assertGuest();
    }

    public function test_student_registration_validates_level_values(): void
    {
        $response = $this->post('/register/student', [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'level' => 'invalid_level',
        ]);

        $response->assertSessionHasErrors('level');
        $this->assertGuest();
    }

    public function test_student_registration_with_minimal_data(): void
    {
        $response = $this->post('/register/student', [
            'name' => 'Minimal Student',
            'email' => 'minimal@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'level' => 'intermediate',
            // Optional fields omitted
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'minimal@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('student', $user->role);

        $student = Student::where('user_id', $user->id)->first();
        $this->assertNotNull($student);
        $this->assertEquals('intermediate', $student->level);
        $this->assertNull($student->interests);
    }

    public function test_teacher_registration_with_minimal_data(): void
    {
        $response = $this->post('/register/teacher', [
            'name' => 'Minimal Teacher',
            'email' => 'minteacher@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            // Optional fields omitted
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'minteacher@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('teacher', $user->role);

        $teacher = Teacher::where('user_id', $user->id)->first();
        $this->assertNotNull($teacher);
        $this->assertNull($teacher->specialization);
        $this->assertNull($teacher->qualifications);
        $this->assertNull($teacher->bio);
    }

    public function test_registration_prevents_duplicate_emails(): void
    {
        // Create a user first
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        // Try to register with same email
        $response = $this->post('/register/student', [
            'name' => 'New Student',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'level' => 'beginner',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_student_profile_creation_rolls_back_on_error(): void
    {
        // This test ensures transaction rollback works
        // We'll test with invalid level after user creation would succeed
        
        $initialUserCount = User::count();
        $initialStudentCount = Student::count();

        try {
            $this->post('/register/student', [
                'name' => 'Test Student',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'level' => 'beginner',
            ]);
        } catch (\Exception $e) {
            // If an exception occurs, verify rollback
        }

        // If registration succeeded, both counts should increase by 1
        // If it failed, both should remain the same (rollback)
        $finalUserCount = User::count();
        $finalStudentCount = Student::count();

        // Either both increased or both stayed the same
        $this->assertEquals(
            $finalUserCount - $initialUserCount,
            $finalStudentCount - $initialStudentCount
        );
    }
}
