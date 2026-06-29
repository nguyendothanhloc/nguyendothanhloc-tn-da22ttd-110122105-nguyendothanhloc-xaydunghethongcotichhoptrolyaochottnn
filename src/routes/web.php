<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatbotKnowledgeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public course browsing routes (for guests and all users)
Route::get('/courses/browse', [CourseController::class, 'browse'])->name('courses.browse');
Route::get('/courses/{id}/detail', [CourseController::class, 'detail'])->name('courses.detail');
Route::get('/classes/{id}/detail', [CourseController::class, 'classDetail'])->name('classes.detail');

// Test route to debug
Route::get('/test-course-detail/{id}', function ($id) {
    $course = \App\Models\Course::with(['classes' => function ($query) {
        $query->whereIn('status', ['upcoming', 'ongoing'])
              ->with('teacher.user')
              ->orderBy('start_date', 'asc');
    }])->find($id);
    
    if (!$course) {
        return response()->json(['error' => 'Course not found', 'id' => $id], 404);
    }
    
    return response()->json([
        'course' => $course->toArray(),
        'classes_count' => $course->classes->count(),
        'is_active' => $course->is_active,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Redirect based on role
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'student') {
        return redirect()->route('student.dashboard');
    } elseif ($user->role === 'teacher') {
        return redirect()->route('teacher.classes');
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin Dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Course Management
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->name('courses.update');
    Route::patch('/courses/{id}/deactivate', [CourseController::class, 'deactivate'])->name('courses.deactivate');
    
    // Teacher Management
    Route::get('/teachers', [\App\Http\Controllers\TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'createTeacher'])
        ->name('teachers.create');
    Route::post('/teachers', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'storeTeacher'])
        ->name('teachers.store');
    Route::get('/teachers/{id}/edit', [\App\Http\Controllers\TeacherController::class, 'edit'])
        ->name('teachers.edit');
    Route::put('/teachers/{id}', [\App\Http\Controllers\TeacherController::class, 'update'])
        ->name('teachers.update');
    Route::delete('/teachers/{id}', [\App\Http\Controllers\TeacherController::class, 'destroy'])
        ->name('teachers.destroy');
    Route::patch('/teachers/{id}/toggle-status', [\App\Http\Controllers\TeacherController::class, 'toggleStatus'])
        ->name('teachers.toggle-status');
    
    // Class Management
    Route::get('/classes', [\App\Http\Controllers\ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [\App\Http\Controllers\ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [\App\Http\Controllers\ClassController::class, 'store'])->name('classes.store');
    Route::get('/admin/classes/{id}', [\App\Http\Controllers\ClassController::class, 'show'])->name('admin.classes.show');
    Route::get('/classes/{id}/edit', [\App\Http\Controllers\ClassController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{id}', [\App\Http\Controllers\ClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{id}', [\App\Http\Controllers\ClassController::class, 'destroy'])->name('classes.destroy');
    
    // Enrollment Management (Admin view)
    Route::get('/admin/enrollments', [EnrollmentController::class, 'adminIndex'])->name('enrollments.admin');
    Route::patch('/admin/enrollments/{id}/approve', [EnrollmentController::class, 'approve'])->name('enrollments.approve');
    Route::patch('/admin/enrollments/{id}/reject', [EnrollmentController::class, 'reject'])->name('enrollments.reject');
    
    // Schedule Management (Admin)
    Route::get('/schedules', [\App\Http\Controllers\ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/create', [\App\Http\Controllers\ScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/schedules', [\App\Http\Controllers\ScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/schedules/{id}/edit', [\App\Http\Controllers\ScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{id}', [\App\Http\Controllers\ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{id}', [\App\Http\Controllers\ScheduleController::class, 'destroy'])->name('schedules.destroy');
    
    // FAQ Management (Chatbot Knowledge Base)
    Route::get('/admin/chatbot-knowledge', [ChatbotKnowledgeController::class, 'index'])->name('admin.faq.index');
    Route::get('/admin/chatbot-knowledge/create', [ChatbotKnowledgeController::class, 'create'])->name('admin.faq.create');
    Route::post('/admin/chatbot-knowledge', [ChatbotKnowledgeController::class, 'store'])->name('admin.faq.store');
    Route::get('/admin/chatbot-knowledge/{chatbotKnowledge}/edit', [ChatbotKnowledgeController::class, 'edit'])->name('admin.faq.edit');
    Route::put('/admin/chatbot-knowledge/{chatbotKnowledge}', [ChatbotKnowledgeController::class, 'update'])->name('admin.faq.update');
    Route::delete('/admin/chatbot-knowledge/{chatbotKnowledge}', [ChatbotKnowledgeController::class, 'destroy'])->name('admin.faq.destroy');
    Route::patch('/admin/chatbot-knowledge/{chatbotKnowledge}/toggle-status', [ChatbotKnowledgeController::class, 'toggleStatus'])->name('admin.faq.toggle-status');
    Route::get('/admin/chatbot-knowledge-debug', function() {
        return view('admin.chatbot-knowledge.debug');
    })->name('admin.faq.debug');
    
    // Auto-test FAQ creation
    Route::get('/admin/faq-auto-test', function() {
        try {
            $testData = [
                'category' => 'Khác',
                'question' => 'Test FAQ tự động - ' . date('Y-m-d H:i:s'),
                'answer' => 'Đây là câu trả lời test tự động được tạo để kiểm tra hệ thống.',
                'keywords' => 'test,auto,debug',
                'priority' => 75,
                'is_active' => true
            ];
            
            $faq = \App\Models\ChatbotKnowledge::create($testData);
            
            return response()->json([
                'status' => 'success',
                'message' => '✅ TẠO FAQ THÀNH CÔNG!',
                'faq_id' => $faq->id,
                'faq_data' => $faq->toArray(),
                'conclusion' => 'Backend hoạt động hoàn hảo. Vấn đề nằm ở FORM hoặc JAVASCRIPT.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ LỖI: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    })->name('admin.faq.autotest');
    
    // Student Management
    Route::get('/admin/students', [\App\Http\Controllers\StudentController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/{id}', [\App\Http\Controllers\StudentController::class, 'show'])->name('admin.students.show');
    Route::get('/admin/students/{id}/edit', [\App\Http\Controllers\StudentController::class, 'edit'])->name('admin.students.edit');
    Route::put('/admin/students/{id}', [\App\Http\Controllers\StudentController::class, 'update'])->name('admin.students.update');
    Route::delete('/admin/students/{id}', [\App\Http\Controllers\StudentController::class, 'destroy'])->name('admin.students.destroy');
});

// Enrollment Routes (Student only)
Route::middleware(['auth', 'role:student'])->group(function () {
    // Student Dashboard
    Route::get('/student/dashboard', [\App\Http\Controllers\StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/attendance', [\App\Http\Controllers\StudentController::class, 'attendance'])->name('student.attendance');
    Route::get('/student/schedule', [\App\Http\Controllers\StudentController::class, 'schedule'])->name('student.schedule');
    Route::get('/student/assessments', [\App\Http\Controllers\StudentController::class, 'assessments'])->name('student.assessments');
    Route::get('/student/progress', [\App\Http\Controllers\StudentController::class, 'progress'])->name('student.progress');
    
    // Enrollments
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('/enrollments/{id}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    Route::delete('/enrollments/{id}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
});

// Chatbot Routes (Student only)
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::post('/api/chat', [ChatbotController::class, 'chat'])->name('chat.send');
    Route::get('/api/chat/history', [ChatbotController::class, 'history'])->name('chat.history');
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teacher/classes', [\App\Http\Controllers\ClassController::class, 'teacherClasses'])->name('teacher.classes');
    Route::get('/teacher/schedule', [\App\Http\Controllers\TeacherController::class, 'schedule'])->name('teacher.schedule');
    Route::get('/classes/{id}', [\App\Http\Controllers\ClassController::class, 'show'])->name('classes.show');
    
    // Attendance Management
    Route::get('/teacher/classes/{classId}/attendance', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('teacher.attendance.index');
    Route::get('/teacher/classes/{classId}/attendance/{scheduleId}', [\App\Http\Controllers\AttendanceController::class, 'show'])->name('teacher.attendance.show');
    Route::post('/teacher/classes/{classId}/attendance/{scheduleId}', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('teacher.attendance.store');
    Route::delete('/teacher/classes/{classId}/attendance/{scheduleId}/reset', [\App\Http\Controllers\AttendanceController::class, 'reset'])->name('teacher.attendance.reset');
    
    // Assessment Management
    Route::get('/teacher/classes/{classId}/assessments', [\App\Http\Controllers\AssessmentController::class, 'index'])->name('teacher.assessments.index');
    Route::get('/teacher/classes/{classId}/assessments/create', [\App\Http\Controllers\AssessmentController::class, 'create'])->name('teacher.assessments.create');
    Route::post('/teacher/classes/{classId}/assessments', [\App\Http\Controllers\AssessmentController::class, 'store'])->name('teacher.assessments.store');
    Route::get('/teacher/classes/{classId}/assessments/{assessmentId}', [\App\Http\Controllers\AssessmentController::class, 'show'])->name('teacher.assessments.show');
    Route::post('/teacher/classes/{classId}/assessments/{assessmentId}/scores', [\App\Http\Controllers\AssessmentController::class, 'storeScores'])->name('teacher.assessments.storeScores');
});

require __DIR__.'/auth.php';
