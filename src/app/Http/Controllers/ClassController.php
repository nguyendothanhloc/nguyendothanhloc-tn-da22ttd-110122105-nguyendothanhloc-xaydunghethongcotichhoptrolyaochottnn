<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Teacher;
use App\Services\ClassService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ClassController extends Controller
{
    protected $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }
    /**
     * Display a listing of classes (Admin view - all classes)
     */
    public function index()
    {
        $classes = $this->classService->getAllClasses(10);
        return view('classes.index', compact('classes'));
    }

    /**
     * Display classes for the authenticated teacher
     */
    public function teacherClasses()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(403, 'Unauthorized');
        }

        $classes = $this->classService->getTeacherClasses($teacher->id);
        return view('teachers.classes', compact('classes'));
    }

    /**
     * Show the form for creating a new class (Admin only)
     */
    public function create()
    {
        $courses = $this->classService->getActiveCourses();
        $teachers = $this->classService->getActiveTeachers();

        return view('classes.create', compact('courses', 'teachers'));
    }

    /**
     * Store a newly created class in storage (Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_capacity' => 'required|integer|min:1',
            'shift' => 'nullable|in:morning,afternoon,evening',
            'weekdays' => 'nullable|array',
            'weekdays.*' => 'in:2,3,4,5,6,7,8',
        ]);

        $validated['status'] = 'upcoming';
        $validated['current_enrollment'] = 0;
        
        // Convert weekdays array to comma-separated string
        if (isset($validated['weekdays']) && is_array($validated['weekdays'])) {
            $validated['weekdays'] = implode(',', $validated['weekdays']);
        }

        ClassModel::create($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Lớp học đã được tạo thành công!');
    }

    /**
     * Display the specified class
     */
    public function show($id)
    {
        $class = ClassModel::with(['course', 'teacher.user', 'enrollments.student.user'])
            ->findOrFail($id);

        // Check authorization for teachers
        if (Auth::user()->role === 'teacher') {
            $teacher = Auth::user()->teacher;
            if ($class->teacher_id !== $teacher->id) {
                abort(403, 'Unauthorized');
            }
        }

        return view('classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified class (Admin only)
     */
    public function edit($id)
    {
        $class = ClassModel::findOrFail($id);
        $courses = Course::where('is_active', true)->get();
        $teachers = Teacher::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->get();

        return view('classes.edit', compact('class', 'courses', 'teachers'));
    }

    /**
     * Update the specified class in storage (Admin only)
     */
    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_capacity' => 'required|integer|min:1',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'shift' => 'nullable|in:morning,afternoon,evening',
            'weekdays' => 'nullable|array',
            'weekdays.*' => 'in:2,3,4,5,6,7,8',
        ]);
        
        // Convert weekdays array to comma-separated string
        if (isset($validated['weekdays']) && is_array($validated['weekdays'])) {
            $validated['weekdays'] = implode(',', $validated['weekdays']);
        } elseif (!isset($validated['weekdays'])) {
            $validated['weekdays'] = null;
        }

        $class->update($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Lớp học đã được cập nhật!');
    }

    /**
     * Remove the specified class from storage (Admin only)
     */
    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);
        
        // Check if class has enrollments
        if ($class->current_enrollment > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Không thể xóa lớp học đã có học viên đăng ký!');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Lớp học đã được xóa!');
    }
}
