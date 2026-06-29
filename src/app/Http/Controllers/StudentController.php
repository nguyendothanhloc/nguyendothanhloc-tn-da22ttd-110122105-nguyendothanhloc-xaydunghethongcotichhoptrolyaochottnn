<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display student dashboard
     */
    public function dashboard()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->route('courses.browse')
                ->with('error', 'Student profile not found');
        }

        // Get active enrollments (exclude cancelled classes)
        $enrollments = $student->enrollments()
            ->whereIn('status', ['paid', 'approved', 'pending']) // Include paid, approved, and pending enrollments
            ->whereHas('class', function($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->with(['class.course', 'class.teacher.user'])
            ->get();

        // Get upcoming schedules (exclude cancelled classes)
        $upcomingSchedules = \App\Models\Schedule::whereIn('class_id', $enrollments->pluck('class_id'))
            ->whereHas('class', function($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(5)
            ->with('class.course')
            ->get();

        // Get attendance statistics
        $totalAttendances = $student->attendances()->count();
        $presentCount = $student->attendances()->where('status', 'present')->count();
        $absentCount = $student->attendances()->where('status', 'absent')->count();

        // Get recent assessment scores
        $recentScores = $student->assessmentScores()
            ->with(['assessment.class.course'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get available courses (active courses with available classes)
        $availableCourses = \App\Models\Course::where('is_active', true)
            ->withCount(['classes' => function ($query) {
                $query->where('status', '!=', 'cancelled')
                      ->whereRaw('current_enrollment < max_capacity');
            }])
            ->having('classes_count', '>', 0)
            ->with(['classes' => function ($query) {
                $query->where('status', '!=', 'cancelled')
                      ->whereRaw('current_enrollment < max_capacity')
                      ->with('teacher.user')
                      ->orderBy('start_date', 'asc');
            }])
            ->limit(6)
            ->get();

        return view('student.dashboard', compact(
            'student',
            'enrollments',
            'upcomingSchedules',
            'totalAttendances',
            'presentCount',
            'absentCount',
            'recentScores',
            'availableCourses'
        ));
    }

    /**
     * Display student's attendance history
     */
    public function attendance()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->route('courses.browse')
                ->with('error', 'Student profile not found');
        }

        // Get attendance records with schedule and class info
        $attendances = $student->attendances()
            ->with(['schedule.class.course'])
            ->orderBy('recorded_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $stats = [
            'total' => $student->attendances()->count(),
            'present' => $student->attendances()->where('status', 'present')->count(),
            'absent' => $student->attendances()->where('status', 'absent')->count(),
            'late' => $student->attendances()->where('status', 'late')->count(),
            'excused' => $student->attendances()->where('status', 'excused')->count(),
        ];

        return view('student.attendance', compact('attendances', 'stats'));
    }

    /**
     * Display student's schedule/timetable
     */
    public function schedule()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->route('courses.browse')
                ->with('error', 'Student profile not found');
        }

        // Get all schedules for student's enrolled classes (exclude cancelled classes)
        $classIds = $student->enrollments()
            ->whereIn('status', ['paid', 'approved', 'pending']) // Include all active enrollments
            ->whereHas('class', function($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->pluck('class_id');

        $schedules = \App\Models\Schedule::whereIn('class_id', $classIds)
            ->whereHas('class', function($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->with(['class.course', 'class.teacher.user', 'attendances' => function($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->where('date', '>=', now()->startOfMonth())
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
        
        // Get attendance statistics by class
        $attendanceStats = [];
        foreach ($classIds as $classId) {
            $totalSchedules = \App\Models\Schedule::where('class_id', $classId)->count();
            $presentCount = $student->attendances()
                ->whereHas('schedule', function($query) use ($classId) {
                    $query->where('class_id', $classId);
                })
                ->whereIn('status', ['present', 'late'])
                ->count();
            
            $attendanceStats[$classId] = [
                'total' => $totalSchedules,
                'present' => $presentCount,
                'percentage' => $totalSchedules > 0 ? round(($presentCount / $totalSchedules) * 100, 1) : 0
            ];
        }

        return view('student.schedule', compact('schedules', 'attendanceStats'));
    }

    /**
     * Display student's assessment scores
     */
    public function assessments()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->route('courses.browse')
                ->with('error', 'Student profile not found');
        }

        // Get all assessment scores grouped by class
        $classIds = $student->enrollments()
            ->whereIn('status', ['paid', 'approved', 'pending']) // Include all active enrollments
            ->pluck('class_id');

        // Get classes with assessments
        $classes = \App\Models\ClassModel::whereIn('id', $classIds)
            ->with([
                'course',
                'teacher.user',
                'assessments' => function($query) {
                    $query->orderBy('date', 'desc');
                }
            ])
            ->get();

        // Get scores for each assessment
        $scoresByClass = [];
        foreach ($classes as $class) {
            $assessmentsWithScores = [];
            foreach ($class->assessments as $assessment) {
                $score = $student->assessmentScores()
                    ->where('assessment_id', $assessment->id)
                    ->first();
                
                $assessmentsWithScores[] = [
                    'assessment' => $assessment,
                    'score' => $score
                ];
            }
            $scoresByClass[$class->id] = $assessmentsWithScores;
        }

        // Calculate overall statistics
        $totalScores = $student->assessmentScores()->count();
        $averageScore = $totalScores > 0 ? $student->assessmentScores()->avg('score') : 0;
        $averagePercentage = $totalScores > 0 
            ? DB::table('assessment_scores')
                ->join('assessments', 'assessment_scores.assessment_id', '=', 'assessments.id')
                ->where('assessment_scores.student_id', $student->id)
                ->selectRaw('AVG((assessment_scores.score / assessments.max_score) * 100) as percentage')
                ->value('percentage')
            : 0;

        return view('student.assessments', compact('classes', 'scoresByClass', 'totalScores', 'averageScore', 'averagePercentage'));
    }

    /**
     * Display student's progress report
     */
    public function progress()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->route('courses.browse')
                ->with('error', 'Student profile not found');
        }

        // Get enrolled classes
        $enrollments = $student->enrollments()
            ->whereIn('status', ['paid', 'approved', 'pending']) // Include all active enrollments
            ->with(['class.course', 'class.teacher.user'])
            ->get();

        $classIds = $enrollments->pluck('class_id');

        // === ATTENDANCE STATISTICS ===
        $totalAttendances = $student->attendances()->count();
        $presentCount = $student->attendances()->whereIn('status', ['present', 'late'])->count();
        $absentCount = $student->attendances()->where('status', 'absent')->count();
        $lateCount = $student->attendances()->where('status', 'late')->count();
        $attendanceRate = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0;

        // === ASSESSMENT STATISTICS ===
        $totalAssessments = $student->assessmentScores()->count();
        $averageScore = $totalAssessments > 0 ? $student->assessmentScores()->avg('score') : 0;
        
        // Calculate average percentage
        $averagePercentage = $totalAssessments > 0 
            ? DB::table('assessment_scores')
                ->join('assessments', 'assessment_scores.assessment_id', '=', 'assessments.id')
                ->where('assessment_scores.student_id', $student->id)
                ->selectRaw('AVG((assessment_scores.score / assessments.max_score) * 100) as percentage')
                ->value('percentage')
            : 0;

        // Get highest and lowest scores
        $highestScore = $student->assessmentScores()
            ->join('assessments', 'assessment_scores.assessment_id', '=', 'assessments.id')
            ->selectRaw('assessment_scores.*, (assessment_scores.score / assessments.max_score * 100) as percentage')
            ->orderBy('percentage', 'desc')
            ->first();

        $lowestScore = $student->assessmentScores()
            ->join('assessments', 'assessment_scores.assessment_id', '=', 'assessments.id')
            ->selectRaw('assessment_scores.*, (assessment_scores.score / assessments.max_score * 100) as percentage')
            ->orderBy('percentage', 'asc')
            ->first();

        // === SCHEDULE COMPLETION ===
        $totalSchedules = \App\Models\Schedule::whereIn('class_id', $classIds)
            ->where('date', '<=', now())
            ->count();
        $completedSchedules = $student->attendances()
            ->whereIn('status', ['present', 'late'])
            ->count();
        $completionPercentage = $totalSchedules > 0 ? round(($completedSchedules / $totalSchedules) * 100, 1) : 0;

        // === CERTIFICATE ELIGIBILITY ===
        $attendanceThreshold = 80; // 80% attendance required
        $scoreThreshold = 70; // 70% average score required
        
        $isAttendanceEligible = $attendanceRate >= $attendanceThreshold;
        $isScoreEligible = $averagePercentage >= $scoreThreshold;
        $isCertificateEligible = $isAttendanceEligible && $isScoreEligible;

        // === PROGRESS BY CLASS ===
        $progressByClass = [];
        foreach ($enrollments as $enrollment) {
            $classId = $enrollment->class_id;
            
            // Attendance for this class
            $classSchedules = \App\Models\Schedule::where('class_id', $classId)
                ->where('date', '<=', now())
                ->count();
            $classAttendances = $student->attendances()
                ->whereHas('schedule', function($q) use ($classId) {
                    $q->where('class_id', $classId);
                })
                ->whereIn('status', ['present', 'late'])
                ->count();
            $classAttendanceRate = $classSchedules > 0 ? round(($classAttendances / $classSchedules) * 100, 1) : 0;

            // Scores for this class
            $classAssessmentIds = \App\Models\Assessment::where('class_id', $classId)->pluck('id');
            $classScores = $student->assessmentScores()->whereIn('assessment_id', $classAssessmentIds)->count();
            $classAveragePercentage = $classScores > 0
                ? DB::table('assessment_scores')
                    ->join('assessments', 'assessment_scores.assessment_id', '=', 'assessments.id')
                    ->where('assessment_scores.student_id', $student->id)
                    ->whereIn('assessment_scores.assessment_id', $classAssessmentIds)
                    ->selectRaw('AVG((assessment_scores.score / assessments.max_score) * 100) as percentage')
                    ->value('percentage')
                : 0;

            $progressByClass[] = [
                'class' => $enrollment->class,
                'attendance_rate' => $classAttendanceRate,
                'average_score' => $classAveragePercentage,
                'assessments_count' => $classScores,
            ];
        }

        return view('student.progress', compact(
            'student',
            'enrollments',
            'totalAttendances',
            'presentCount',
            'absentCount',
            'lateCount',
            'attendanceRate',
            'totalAssessments',
            'averageScore',
            'averagePercentage',
            'highestScore',
            'lowestScore',
            'totalSchedules',
            'completedSchedules',
            'completionPercentage',
            'isAttendanceEligible',
            'isScoreEligible',
            'isCertificateEligible',
            'attendanceThreshold',
            'scoreThreshold',
            'progressByClass'
        ));
    }

    // ============================================
    // ADMIN METHODS
    // ============================================

    /**
     * Display a listing of students (Admin only)
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'enrollments.class.course']);

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        $students = $query->paginate(15);

        return view('admin.students.index', compact('students'));
    }

    /**
     * Display the specified student (Admin only)
     */
    public function show($id)
    {
        $student = Student::with([
            'user',
            'enrollments.class.course',
            'attendances.schedule.class.course',
            'assessmentScores.assessment.class.course'
        ])->findOrFail($id);

        // Calculate statistics
        $stats = [
            'total_enrollments' => $student->enrollments()->count(),
            'active_enrollments' => $student->enrollments()->whereIn('status', ['paid', 'approved', 'pending'])->count(),
            'total_attendances' => $student->attendances()->count(),
            'present_count' => $student->attendances()->where('status', 'present')->count(),
            'absent_count' => $student->attendances()->where('status', 'absent')->count(),
            'late_count' => $student->attendances()->where('status', 'late')->count(),
            'attendance_rate' => $student->attendances()->count() > 0 
                ? round(($student->attendances()->where('status', 'present')->count() / $student->attendances()->count()) * 100, 2)
                : 0,
            'average_score' => $student->assessmentScores()->avg('score') ?? 0,
        ];

        return view('admin.students.show', compact('student', 'stats'));
    }

    /**
     * Show the form for editing the specified student (Admin only)
     */
    public function edit($id)
    {
        $student = Student::with('user')->findOrFail($id);
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified student (Admin only)
     */
    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->user->id . ',id',
            'level' => 'nullable|string|max:255',
            'interests' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update user information
            $student->user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ]);

            // Update student information
            $student->update([
                'level' => $request->input('level'),
                'interests' => $request->input('interests'),
            ]);

            DB::commit();
            return redirect()->route('admin.students.show', $student->id)
                ->with('success', 'Cập nhật thông tin học viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified student (Admin only)
     */
    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        // Check if student has active enrollments
        $activeEnrollments = $student->enrollments()
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        if ($activeEnrollments > 0) {
            return back()->with('error', 'Không thể xóa học viên có đăng ký đang hoạt động!');
        }

        DB::beginTransaction();
        try {
            $user = $student->user;
            $student->delete();
            $user->delete();

            DB::commit();
            return redirect()->route('admin.students.index')
                ->with('success', 'Xóa học viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
