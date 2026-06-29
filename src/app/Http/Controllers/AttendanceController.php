<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display attendance form for a class
     */
    public function index($classId)
    {
        $class = ClassModel::with(['course', 'enrollments.student.user', 'schedules'])
            ->findOrFail($classId);
        
        // Check if teacher owns this class
        $teacher = Auth::user()->teacher;
        if ($class->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.classes')
                ->with('error', 'Bạn không có quyền truy cập lớp học này');
        }

        // Get all schedules for this class
        $schedules = $class->schedules()->orderBy('date', 'desc')->get();
        
        // Get students enrolled in this class
        $students = $class->enrollments()
            ->whereIn('status', ['paid', 'approved', 'pending']) // Include all active enrollments
            ->with('student.user')
            ->get()
            ->pluck('student');

        return view('teacher.attendance.index', compact('class', 'schedules', 'students'));
    }

    /**
     * Show attendance for a specific schedule
     */
    public function show($classId, $scheduleId)
    {
        $class = ClassModel::with('course')->findOrFail($classId);
        $schedule = Schedule::findOrFail($scheduleId);
        
        // Check if teacher owns this class
        $teacher = Auth::user()->teacher;
        if ($class->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.classes')
                ->with('error', 'Bạn không có quyền truy cập lớp học này');
        }

        // Get students with their attendance for this schedule
        $students = $class->enrollments()
            ->whereIn('status', ['paid', 'approved', 'pending']) // Include all active enrollments
            ->with(['student.user', 'student.attendances' => function($query) use ($scheduleId) {
                $query->where('schedule_id', $scheduleId);
            }])
            ->get()
            ->pluck('student');

        // Get absence history for this class (only absent status)
        $absentHistory = Attendance::where('status', 'absent')
            ->whereHas('schedule', function($query) use ($classId) {
                $query->where('class_id', $classId);
            })
            ->with(['student.user', 'schedule'])
            ->orderBy('recorded_at', 'desc')
            ->get();

        return view('teacher.attendance.show', compact('class', 'schedule', 'students', 'absentHistory'));
    }

    /**
     * Store or update attendance records
     */
    public function store(Request $request, $classId, $scheduleId)
    {
        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.note' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['attendance'] as $record) {
                Attendance::updateOrCreate(
                    [
                        'schedule_id' => $scheduleId,
                        'student_id' => $record['student_id'],
                    ],
                    [
                        'status' => $record['status'],
                        'note' => $record['note'] ?? null,
                        'recorded_at' => now(),
                    ]
                );
            }

            DB::commit();
            return redirect()->route('teacher.attendance.show', [$classId, $scheduleId])
                ->with('success', 'Điểm danh đã được lưu thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Reset all attendance records for a specific schedule
     */
    public function reset($classId, $scheduleId)
    {
        $class = ClassModel::findOrFail($classId);
        $schedule = Schedule::findOrFail($scheduleId);
        
        // Check if teacher owns this class
        $teacher = Auth::user()->teacher;
        if ($class->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.classes')
                ->with('error', 'Bạn không có quyền truy cập lớp học này');
        }

        DB::beginTransaction();
        try {
            // Delete all attendance records for this schedule
            Attendance::where('schedule_id', $scheduleId)->delete();

            DB::commit();
            return redirect()->route('teacher.attendance.show', [$classId, $scheduleId])
                ->with('success', 'Đã xóa tất cả điểm danh của buổi học này');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
