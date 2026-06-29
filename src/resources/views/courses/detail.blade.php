<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($course) ? $course->name : 'Chi tiết khóa học' }} - Trung tâm ngoại ngữ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { padding-top: 20px; }
    </style>
</head>
<body class="bg-light">
    @if(!isset($course))
        <div class="container">
            <div class="alert alert-danger">
                <h4>Lỗi: Không tìm thấy dữ liệu khóa học</h4>
                <p>Biến $course không tồn tại trong view.</p>
            </div>
        </div>
    @endif

    <!-- TEST: This should always show -->
    <div class="container">
        <div class="alert alert-info">
            <strong>TEST:</strong> Trang đang được hiển thị. Template đang hoạt động.
        </div>
    </div>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">Trung tâm ngoại ngữ</a>
            <div class="d-flex">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary me-2">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Đăng ký</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <!-- Course Information -->
        <div class="card mb-4">
            <div class="card-body">
                <h3 class="card-title">{{ $course->name }}</h3>
                
                <div class="mb-3">
                    <span class="badge bg-primary me-2">
                        <i class="bi bi-translate"></i> {{ $course->language }}
                    </span>
                    <span class="badge bg-info">
                        {{ ucfirst($course->level) }}
                    </span>
                </div>

                <p class="card-text">{{ $course->description }}</p>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-clock fs-3 text-primary me-3"></i>
                            <div>
                                <strong>Thời lượng</strong><br>
                                {{ $course->duration_weeks }} tuần
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-currency-dollar fs-3 text-success me-3"></i>
                            <div>
                                <strong>Học phí</strong><br>
                                <span class="text-danger fw-bold">{{ number_format($course->price, 0, ',', '.') }} VNĐ</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-people fs-3 text-info me-3"></i>
                            <div>
                                <strong>Lớp đang mở</strong><br>
                                {{ $course->classes->count() }} lớp
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Classes -->
        <div class="card mb-5">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-door-open"></i> Lớp học đang mở</h5>
            </div>
            <div class="card-body">
                @if($course->classes->isEmpty())
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Hiện tại không có lớp học nào đang mở cho khóa học này.
                    </div>
                @else
                    <div class="row">
                        @foreach($course->classes as $class)
                            <div class="col-md-6 mb-3">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $class->name }}</h6>
                                        
                                        <ul class="list-unstyled mb-3">
                                            <li class="mb-2">
                                                <i class="bi bi-person-badge text-primary"></i>
                                                <strong>Giáo viên:</strong> {{ $class->teacher->user->name }}
                                            </li>
                                            <li class="mb-2">
                                                <i class="bi bi-calendar-range text-success"></i>
                                                <strong>Thời gian:</strong><br>
                                                <small>{{ $class->start_date->format('d/m/Y') }} - {{ $class->end_date->format('d/m/Y') }}</small>
                                            </li>
                                            <li class="mb-2">
                                                <i class="bi bi-people text-info"></i>
                                                <strong>Chỗ trống:</strong> 
                                                @php
                                                    $availableSlots = $class->max_capacity - $class->current_enrollment;
                                                @endphp
                                                <span class="badge bg-{{ $availableSlots > 5 ? 'success' : 'warning' }}">
                                                    {{ $availableSlots }} / {{ $class->max_capacity }}
                                                </span>
                                            </li>
                                        </ul>

                                        @if($availableSlots > 0)
                                            <a href="{{ route('classes.detail', $class->id) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                                <i class="bi bi-eye"></i> Xem chi tiết lớp học
                                            </a>
                                            @auth
                                                @if(auth()->user()->role === 'student')
                                                    <a href="{{ route('enrollments.create') }}?class_id={{ $class->id }}" class="btn btn-success btn-sm w-100">
                                                        <i class="bi bi-check-circle"></i> Đăng ký ngay
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-success btn-sm w-100">
                                                    <i class="bi bi-box-arrow-in-right"></i> Đăng nhập để đăng ký
                                                </a>
                                            @endauth
                                        @else
                                            <button class="btn btn-secondary btn-sm w-100" disabled>
                                                <i class="bi bi-x-circle"></i> Đã đầy
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; {{ date('Y') }} Trung tâm ngoại ngữ. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
