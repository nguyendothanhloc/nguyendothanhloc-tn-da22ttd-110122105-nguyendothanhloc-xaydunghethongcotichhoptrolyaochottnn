<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules (Admin only)
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['class.course', 'class.teacher']);

        // Filter by class if provided
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('date', 'desc')
                          ->orderBy('start_time', 'asc')
                          ->paginate(20);

        // Get all classes for filter dropdown
        $classes = ClassModel::with('course')->orderBy('name')->get();

        return view('schedules.index', compact('schedules', 'classes'));
    }

    /**
     * Show the form for creating a new schedule
     */
    public function create()
    {
        $classes = ClassModel::with(['course', 'teacher'])
                            ->orderBy('name')
                            ->get();

        return view('schedules.create', compact('classes'));
    }

    /**
     * Store a newly created schedule in storage
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'topic' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,completed,cancelled',
        ], [
            'class_id.required' => 'Vui lòng chọn lớp học',
            'class_id.exists' => 'Lớp học không tồn tại',
            'date.required' => 'Vui lòng chọn ngày học',
            'date.after_or_equal' => 'Ngày học không được trong quá khứ',
            'start_time.required' => 'Vui lòng nhập giờ bắt đầu',
            'start_time.date_format' => 'Định dạng giờ không hợp lệ (HH:MM)',
            'end_time.required' => 'Vui lòng nhập giờ kết thúc',
            'end_time.date_format' => 'Định dạng giờ không hợp lệ (HH:MM)',
            'end_time.after' => 'Giờ kết thúc phải sau giờ bắt đầu',
            'status.required' => 'Vui lòng chọn trạng thái',
            'status.in' => 'Trạng thái không hợp lệ',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        Schedule::create([
            'class_id' => $request->class_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location' => $request->location,
            'topic' => $request->topic,
            'status' => $request->status ?? 'scheduled',
        ]);

        return redirect()->route('schedules.index')
                       ->with('success', 'Lịch học đã được tạo thành công!');
    }

    /**
     * Display the specified schedule
     */
    public function show($id)
    {
        $schedule = Schedule::with(['class.course', 'class.teacher', 'attendances.student'])
                           ->findOrFail($id);

        return view('schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified schedule
     */
    public function edit($id)
    {
        $schedule = Schedule::with('class')->findOrFail($id);
        
        $classes = ClassModel::with(['course', 'teacher'])
                            ->orderBy('name')
                            ->get();

        return view('schedules.edit', compact('schedule', 'classes'));
    }

    /**
     * Update the specified schedule in storage
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'topic' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,completed,cancelled',
        ], [
            'class_id.required' => 'Vui lòng chọn lớp học',
            'class_id.exists' => 'Lớp học không tồn tại',
            'date.required' => 'Vui lòng chọn ngày học',
            'start_time.required' => 'Vui lòng nhập giờ bắt đầu',
            'start_time.date_format' => 'Định dạng giờ không hợp lệ (HH:MM)',
            'end_time.required' => 'Vui lòng nhập giờ kết thúc',
            'end_time.date_format' => 'Định dạng giờ không hợp lệ (HH:MM)',
            'end_time.after' => 'Giờ kết thúc phải sau giờ bắt đầu',
            'status.required' => 'Vui lòng chọn trạng thái',
            'status.in' => 'Trạng thái không hợp lệ',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $schedule->update([
            'class_id' => $request->class_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location' => $request->location,
            'topic' => $request->topic,
            'status' => $request->status,
        ]);

        return redirect()->route('schedules.index')
                       ->with('success', 'Lịch học đã được cập nhật thành công!');
    }

    /**
     * Remove the specified schedule from storage
     */
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        
        // Check if attendance has been taken
        if ($schedule->attendances()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Không thể xóa lịch học đã có điểm danh!');
        }

        $schedule->delete();

        return redirect()->route('schedules.index')
                       ->with('success', 'Lịch học đã được xóa thành công!');
    }
}
