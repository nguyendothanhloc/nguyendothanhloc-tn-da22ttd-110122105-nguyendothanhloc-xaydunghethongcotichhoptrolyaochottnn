<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /**
     * Display assessments for a class
     */
    public function index($classId)
    {
        $class = ClassModel::with(['course', 'assessments'])->findOrFail($classId);
        
        // Check if teacher owns this class
        $teacher = Auth::user()->teacher;
        if ($class->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.classes')
                ->with('error', 'Bạn không có quyền truy cập lớp học này');
        }

        $assessments = $class->assessments()->orderBy('assessment_date', 'desc')->get();

        return view('teacher.assessments.index', compact('class', 'assessments'));
    }

    /**
     * Show form to create new assessment
     */
    public function create($classId)
    {
        $class = ClassModel::with('course')->findOrFail($classId);
        
        // Check if teacher owns this class
        $teacher = Auth::user()->teacher;
        if ($class->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.classes')
                ->with('error', 'Bạn không có quyền truy cập lớp học này');
        }

        return view('teacher.assessments.create', compact('class'));
    }

    /**
     * Store new assessment
     */
    public function store(Request $request, $classId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:quiz,midterm,final,assignment,project',
            'max_score' => 'required|numeric|min:0|max:100',
            'assessment_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $validated['class_id'] = $classId;

        Assessment::create($validated);

        return redirect()->route('teacher.assessments.index', $classId)
            ->with('success', 'Bài kiểm tra đã được tạo thành công');
    }

    /**
     * Show assessment with student scores
     */
    public function show($classId, $assessmentId)
    {
        $class = ClassModel::with('course')->findOrFail($classId);
        $assessment = Assessment::with('scores.student.user')->findOrFail($assessmentId);
        
        // Check if teacher owns this class
        $teacher = Auth::user()->teacher;
        if ($class->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.classes')
                ->with('error', 'Bạn không có quyền truy cập lớp học này');
        }

        // Get students with their scores
        $students = $class->enrollments()
            ->whereIn('status', ['paid', 'approved', 'pending']) // Include all active enrollments
            ->with(['student.user', 'student.assessmentScores' => function($query) use ($assessmentId) {
                $query->where('assessment_id', $assessmentId);
            }])
            ->get()
            ->pluck('student');

        return view('teacher.assessments.show', compact('class', 'assessment', 'students'));
    }

    /**
     * Store or update assessment scores
     */
    public function storeScores(Request $request, $classId, $assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.score' => 'required|numeric|min:0|max:' . $assessment->max_score,
            'scores.*.feedback' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['scores'] as $record) {
                AssessmentScore::updateOrCreate(
                    [
                        'assessment_id' => $assessmentId,
                        'student_id' => $record['student_id'],
                    ],
                    [
                        'score' => $record['score'],
                        'feedback' => $record['feedback'] ?? null,
                    ]
                );
                
                // Auto-update completion_percentage based on assessment type
                if ($assessment->type === 'midterm') {
                    // Midterm exam = 50% completion
                    \App\Models\Enrollment::where('student_id', $record['student_id'])
                        ->where('class_id', $classId)
                        ->update(['completion_percentage' => 50]);
                } elseif ($assessment->type === 'final') {
                    // Final exam = 100% completion
                    \App\Models\Enrollment::where('student_id', $record['student_id'])
                        ->where('class_id', $classId)
                        ->update(['completion_percentage' => 100]);
                }
            }

            DB::commit();
            return redirect()->route('teacher.assessments.show', [$classId, $assessmentId])
                ->with('success', 'Điểm đã được lưu thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }
}
