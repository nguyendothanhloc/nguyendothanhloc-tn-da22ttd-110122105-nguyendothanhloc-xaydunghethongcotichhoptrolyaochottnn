<?php

namespace App\Http\Controllers;

use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseController extends Controller
{
    protected CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Display a listing of courses
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['language', 'level', 'status']);
        $courses = $this->courseService->getAllCourses($filters);
        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course
     *
     * @return View
     */
    public function create(): View
    {
        return view('courses.create');
    }

    /**
     * Store a newly created course in storage
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'language' => 'required|string|max:100',
            'level' => 'required|in:beginner,elementary,intermediate,advanced',
            'duration_weeks' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Tên khóa học là bắt buộc',
            'language.required' => 'Ngôn ngữ là bắt buộc',
            'level.required' => 'Trình độ là bắt buộc',
            'level.in' => 'Trình độ phải là beginner, elementary, intermediate hoặc advanced',
            'duration_weeks.required' => 'Thời lượng là bắt buộc',
            'duration_weeks.min' => 'Thời lượng phải lớn hơn 0',
            'price.required' => 'Giá là bắt buộc',
            'price.min' => 'Giá phải lớn hơn hoặc bằng 0',
        ]);

        $course = $this->courseService->createCourse($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Khóa học đã được tạo thành công');
    }

    /**
     * Show the form for editing the specified course
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $course = $this->courseService->getCourseById($id);
        
        if (!$course) {
            abort(404, 'Không tìm thấy khóa học');
        }

        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified course in storage
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'language' => 'required|string|max:100',
            'level' => 'required|in:beginner,elementary,intermediate,advanced',
            'duration_weeks' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Tên khóa học là bắt buộc',
            'language.required' => 'Ngôn ngữ là bắt buộc',
            'level.required' => 'Trình độ là bắt buộc',
            'level.in' => 'Trình độ phải là beginner, elementary, intermediate hoặc advanced',
            'duration_weeks.required' => 'Thời lượng là bắt buộc',
            'duration_weeks.min' => 'Thời lượng phải lớn hơn 0',
            'price.required' => 'Giá là bắt buộc',
            'price.min' => 'Giá phải lớn hơn hoặc bằng 0',
        ]);

        $course = $this->courseService->updateCourse($id, $validated);

        return redirect()->route('courses.index')
            ->with('success', 'Khóa học đã được cập nhật thành công');
    }

    /**
     * Deactivate the specified course (soft delete)
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function deactivate(int $id): RedirectResponse
    {
        $course = $this->courseService->deactivateCourse($id);

        return redirect()->route('courses.index')
            ->with('success', 'Khóa học đã được vô hiệu hóa');
    }

    /**
     * Display active courses for students to browse
     *
     * @param Request $request
     * @return View
     */
    public function browse(Request $request): View
    {
        $filters = $request->only(['language', 'level']);
        $filters['status'] = 'active'; // Only show active courses
        $courses = $this->courseService->getAllCourses($filters);
        return view('courses.browse', compact('courses'));
    }

    /**
     * Display course details with available classes
     *
     * @param int $id
     * @return View
     */
    public function detail(int $id): View
    {
        $course = $this->courseService->getCourseById($id);
        
        if (!$course || !$course->is_active) {
            abort(404, 'Không tìm thấy khóa học');
        }

        // Load classes that are upcoming or ongoing (not cancelled or completed)
        $course->load(['classes' => function ($query) {
            $query->whereIn('status', ['upcoming', 'ongoing'])
                  ->with('teacher.user')
                  ->orderBy('start_date', 'asc');
        }]);

        return view('courses.detail', compact('course'));
    }

    /**
     * Display class details with schedule, teacher, and available slots
     *
     * @param int $id
     * @return View
     */
    public function classDetail(int $id): View
    {
        $class = \App\Models\ClassModel::with(['course', 'teacher.user', 'schedules'])
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->findOrFail($id);

        $availableSlots = $class->max_capacity - $class->current_enrollment;

        return view('classes.detail', compact('class', 'availableSlots'));
    }
}
