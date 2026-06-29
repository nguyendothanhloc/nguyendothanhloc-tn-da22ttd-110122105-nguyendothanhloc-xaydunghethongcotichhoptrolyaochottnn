<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the student registration view.
     */
    public function createStudent(): View
    {
        return view('auth.register-student');
    }

    /**
     * Display the teacher registration view.
     */
    public function createTeacher(): View
    {
        // If admin is logged in, show admin teacher creation form
        if (Auth::check() && Auth::user()->role === 'admin') {
            return view('teachers.create');
        }
        
        // Otherwise show public registration form
        return view('auth.register-teacher');
    }

    /**
     * Display the admin registration view.
     */
    public function createAdmin(): View
    {
        return view('auth.register-admin');
    }

    /**
     * Display the default registration view (redirects to student).
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle student registration request.
     *
     * @throws ValidationException
     */
    public function storeStudent(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'level' => ['required', 'in:beginner,elementary,intermediate,advanced'],
            'interests' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'student',
                'is_active' => true,
            ]);

            // Automatically create Student profile
            Student::create([
                'user_id' => $user->id,
                'level' => $request->level,
                'interests' => $request->interests,
            ]);

            DB::commit();

            event(new Registered($user));
            Auth::login($user);

            return redirect(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle teacher registration request.
     *
     * @throws ValidationException
     */
    public function storeTeacher(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'qualifications' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'teacher',
                'is_active' => true,
            ]);

            // Automatically create Teacher profile
            Teacher::create([
                'user_id' => $user->id,
                'specialization' => $request->specialization,
                'qualifications' => $request->qualifications,
                'bio' => $request->bio,
            ]);

            DB::commit();

            event(new Registered($user));
            
            // If admin is creating teacher, redirect to courses page
            // Otherwise (public registration), login the teacher
            if (Auth::check() && Auth::user()->role === 'admin') {
                return redirect()->route('courses.index')
                    ->with('success', 'Tài khoản giáo viên đã được tạo thành công');
            }
            
            Auth::login($user);
            return redirect(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle admin registration request.
     *
     * @throws ValidationException
     */
    public function storeAdmin(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'admin',
            'is_active' => true,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Handle an incoming registration request (default - redirects to student).
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        return $this->storeStudent($request);
    }
}
