<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Trang chủ học viên</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <!-- Welcome Message -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-success">
                        <h5>Xin chào, {{ Auth::user()->name }}!</h5>
                        <p class="mb-0">Chào mừng bạn đến với hệ thống quản lý học tập.</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1">Lớp đang học</h6>
                            <h3 class="mb-0 text-primary">{{ $enrollments->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1">Điểm danh</h6>
                            <h3 class="mb-0 text-success">{{ $presentCount }}/{{ $totalAttendances }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1">Bài kiểm tra</h6>
                            <h3 class="mb-0 text-info">{{ $recentScores->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Schedule -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-calendar-week"></i> Lịch học sắp tới</h5>
                        </div>
                        <div class="card-body">
                            @if($upcomingSchedules->isEmpty())
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle"></i> Chưa có lịch học sắp tới.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Ngày</th>
                                                <th>Thời gian</th>
                                                <th>Khóa học</th>
                                                <th>Phòng học</th>
                                                <th>Chủ đề</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($upcomingSchedules as $schedule)
                                                <tr>
                                                    <td>
                                                        <strong>{{ \Carbon\Carbon::parse($schedule->date)->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $schedule->class->course->name }}</td>
                                                    <td><i class="bi bi-geo-alt"></i> {{ $schedule->location }}</td>
                                                    <td>{{ $schedule->topic }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end mt-2">
                                    <a href="{{ route('student.schedule') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-calendar3"></i> Xem thời khóa biểu đầy đủ
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Classes -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Lớp học của tôi</h5>
                        </div>
                        <div class="card-body">
                            @if($enrollments->isEmpty())
                                <div class="alert alert-info mb-0 text-center">
                                    <p class="mb-2">Bạn chưa đăng ký lớp học nào.</p>
                                    <a href="{{ route('courses.browse') }}" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Duyệt khóa học
                                    </a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Khóa học</th>
                                                <th>Lớp</th>
                                                <th>Giáo viên</th>
                                                <th>Thời gian</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($enrollments as $enrollment)
                                                <tr>
                                                    <td><strong>{{ $enrollment->class->course->name }}</strong></td>
                                                    <td>{{ $enrollment->class->name }}</td>
                                                    <td>{{ $enrollment->class->teacher->user->name }}</td>
                                                    <td>
                                                        <small>{{ $enrollment->class->start_date->format('d/m/Y') }} - {{ $enrollment->class->end_date->format('d/m/Y') }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $enrollment->status === 'approved' ? 'success' : ($enrollment->status === 'pending' ? 'warning' : 'danger') }}">
                                                            {{ $enrollment->status }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Courses -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-book"></i> Khóa học có sẵn</h5>
                            <a href="{{ route('courses.browse') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-grid"></i> Xem tất cả
                            </a>
                        </div>
                        <div class="card-body">
                            @if($availableCourses->isEmpty())
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle"></i> Hiện chưa có khóa học nào có sẵn.
                                </div>
                            @else
                                <div class="row">
                                    @foreach($availableCourses as $course)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card h-100 border-success">
                                                <div class="card-body">
                                                    <h5 class="card-title text-success">{{ $course->name }}</h5>
                                                    <p class="card-text small text-muted">
                                                        <i class="bi bi-translate"></i> {{ $course->language }} - 
                                                        <span class="badge bg-info">{{ ucfirst($course->level) }}</span>
                                                    </p>
                                                    <p class="card-text">
                                                        <small>{{ Str::limit($course->description, 80) }}</small>
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="text-primary fw-bold">{{ number_format($course->price, 0, ',', '.') }} VNĐ</span>
                                                        <span class="badge bg-secondary">{{ $course->duration_weeks }} tuần</span>
                                                    </div>
                                                    <div class="mb-2">
                                                        <small class="text-muted">
                                                            <i class="bi bi-people"></i> {{ $course->classes->count() }} lớp có sẵn
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-white border-top-0">
                                                    <a href="{{ route('courses.detail', $course->id) }}" class="btn btn-outline-success btn-sm w-100">
                                                        <i class="bi bi-eye"></i> Xem chi tiết
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
