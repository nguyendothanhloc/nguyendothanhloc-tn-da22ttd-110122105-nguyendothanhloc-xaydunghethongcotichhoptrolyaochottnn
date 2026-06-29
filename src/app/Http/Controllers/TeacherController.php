<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers
     */
    public function index(): View
    {
        $teachers = Teacher::with('user')->get();
        return view('teachers.index', compact('teachers'));
    }

    /**
     * Show the form for editing a teacher
     */
    public function edit(int $id): View
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'qualifications' => 'nullable|string',
            'bio' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
        ]);

        // Update user info
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update teacher profile
        $teacher->update([
            'specialization' => $request->specialization,
            'qualifications' => $request->qualifications,
            'bio' => $request->bio,
        ]);

        return redirect()->route('teachers.index')
            ->with('success', 'Thông tin giáo viên đã được cập nhật thành công');
    }

    /**
     * Remove the specified teacher
     */
    public function destroy(int $id): RedirectResponse
    {
        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;
        
        // Delete teacher profile first
        $teacher->delete();
        
        // Then delete user account
        $user->delete();

        return redirect()->route('teachers.index')
            ->with('success', 'Tài khoản giáo viên đã được xóa thành công');
    }

    /**
     * Toggle teacher active status
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;
        
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        
        return redirect()->route('teachers.index')
            ->with('success', "Tài khoản giáo viên đã được {$status} thành công");
    }

    /**
     * Display teacher's schedule/timetable
     */
    public function schedule(): View
    {
        $teacher = auth()->user()->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher profile not found');
        }

        // Get all schedules for teacher's classes
        $schedules = \App\Models\Schedule::whereHas('class', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with(['class.course'])
            ->where('date', '>=', now()->startOfMonth())
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('teacher.schedule', compact('schedules'));
    }
}
