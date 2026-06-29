<?php

namespace App\Http\Controllers;

use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Display all enrollments for admin
     */
    public function adminIndex(Request $request)
    {
        $query = \App\Models\Enrollment::with(['student.user', 'class.course', 'class.teacher.user']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by class
        if ($request->has('class_id') && $request->class_id !== '') {
            $query->where('class_id', $request->class_id);
        }

        // Order by newest first
        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get all classes for filter
        $classes = \App\Models\ClassModel::with('course')->get();
        
        // Count pending enrollments for notification badge
        $pendingCount = \App\Models\Enrollment::where('status', 'pending')->count();

        return view('enrollments.admin-index', compact('enrollments', 'classes', 'pendingCount'));
    }

    /**
     * Display a listing of enrollments
     */
    public function index(Request $request)
    {
        // If user is admin, redirect to admin enrollments page
        if (Auth::user()->role === 'admin') {
            return redirect()->route('enrollments.admin');
        }
        
        // Get current user's student profile
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found');
        }

        $enrollments = $this->enrollmentService->getStudentEnrollments($student->id);
        
        // Load schedules for each enrollment's class
        $enrollments->load(['class.schedules' => function($query) {
            $query->where('date', '>=', now()->toDateString())
                  ->orderBy('date', 'asc')
                  ->orderBy('start_time', 'asc');
        }]);
        
        // Load assessment scores for the student
        $assessmentScores = \App\Models\AssessmentScore::where('student_id', $student->id)
            ->with(['assessment' => function($query) {
                $query->orderBy('assessment_date', 'desc');
            }])
            ->get()
            ->groupBy(function($score) {
                return $score->assessment->class_id;
            });

        return view('enrollments.index', compact('enrollments', 'assessmentScores'));
    }

    /**
     * Show the form for creating a new enrollment
     */
    public function create(Request $request)
    {
        $classId = $request->query('class_id');
        
        if (!$classId) {
            return redirect()->back()->with('error', 'Class ID is required');
        }

        // Check capacity before showing form
        if (!$this->enrollmentService->checkCapacity($classId)) {
            return redirect()->back()->with('error', 'This class is at maximum capacity');
        }

        return view('enrollments.create', compact('classId'));
    }

    /**
     * Store a newly created enrollment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        // Get current user's student profile
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found');
        }

        try {
            $enrollment = $this->enrollmentService->createEnrollment([
                'student_id' => $student->id,
                'class_id' => $validated['class_id'],
                'enrollment_date' => now()->toDateString(),
                'status' => 'approved', // TỰ ĐỘNG DUYỆT - không cần admin phê duyệt
            ]);

            return redirect()->route('enrollments.show', $enrollment->id)
                ->with('success', 'Đăng ký thành công! Bạn đã được ghi danh vào lớp học.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified enrollment
     */
    public function show(int $id)
    {
        $enrollment = $this->enrollmentService->getEnrollmentById($id);

        if (!$enrollment) {
            return redirect()->route('enrollments.index')
                ->with('error', 'Enrollment not found');
        }

        // Check if the enrollment belongs to the current user
        $student = Auth::user()->student;
        if ($enrollment->student_id !== $student->id) {
            return redirect()->route('enrollments.index')
                ->with('error', 'Unauthorized access');
        }

        return view('enrollments.show', compact('enrollment'));
    }

    /**
     * Cancel an enrollment
     */
    public function destroy(int $id)
    {
        try {
            $enrollment = $this->enrollmentService->getEnrollmentById($id);

            if (!$enrollment) {
                return redirect()->route('enrollments.index')
                    ->with('error', 'Enrollment not found');
            }

            // Check if the enrollment belongs to the current user
            $student = Auth::user()->student;
            if ($enrollment->student_id !== $student->id) {
                return redirect()->route('enrollments.index')
                    ->with('error', 'Unauthorized access');
            }

            $this->enrollmentService->cancelEnrollment($id);

            return redirect()->route('enrollments.index')
                ->with('success', 'Enrollment cancelled successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Approve an enrollment (Admin only)
     */
    public function approve(int $id)
    {
        try {
            $enrollment = $this->enrollmentService->getEnrollmentById($id);

            if (!$enrollment) {
                return redirect()->route('enrollments.admin')
                    ->with('error', 'Enrollment not found');
            }

            // Update status to paid
            $this->enrollmentService->updateEnrollment($id, ['status' => 'paid']);

            return redirect()->route('enrollments.admin')
                ->with('success', 'Enrollment approved successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reject an enrollment (Admin only)
     */
    public function reject(int $id)
    {
        try {
            $enrollment = $this->enrollmentService->getEnrollmentById($id);

            if (!$enrollment) {
                return redirect()->route('enrollments.admin')
                    ->with('error', 'Enrollment not found');
            }

            // Cancel the enrollment
            $this->enrollmentService->cancelEnrollment($id);

            return redirect()->route('enrollments.admin')
                ->with('success', 'Enrollment rejected successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
