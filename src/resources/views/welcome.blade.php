<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trung tâm ngoại ngữ</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
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

    <!-- Hero Section -->
    <div class="bg-primary text-white py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Khám phá khóa học ngoại ngữ</h1>
            <p class="lead">Nâng cao kỹ năng ngôn ngữ của bạn với các khóa học chất lượng</p>
        </div>
    </div>

    <!-- Courses Section -->
    <div class="container my-5">
        <h2 class="mb-4">Khóa học nổi bật</h2>
        
        @php
            $courses = \App\Models\Course::where('is_active', true)
                ->withCount(['classes' => function($q) {
                    $q->whereIn('status', ['upcoming', 'ongoing']);
                }])
                ->take(6)
                ->get();
        @endphp

        @if($courses->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Hiện tại chưa có khóa học nào.
            </div>
        @else
            <div class="row">
                @foreach($courses as $course)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">{{ $course->name }}</h5>
                                <p class="card-text text-muted">
                                    {{ Str::limit($course->description, 100) }}
                                </p>
                                
                                <div class="mb-3">
                                    <span class="badge bg-primary me-2">
                                        <i class="bi bi-translate"></i> {{ $course->language }}
                                    </span>
                                    @php
                                        $levelBadges = [
                                            'beginner' => 'bg-success',
                                            'elementary' => 'bg-info',
                                            'intermediate' => 'bg-warning',
                                            'advanced' => 'bg-danger'
                                        ];
                                        $levelLabels = [
                                            'beginner' => 'Beginner',
                                            'elementary' => 'Elementary',
                                            'intermediate' => 'Intermediate',
                                            'advanced' => 'Advanced'
                                        ];
                                    @endphp
                                    <span class="badge {{ $levelBadges[$course->level] ?? 'bg-secondary' }}">
                                        {{ $levelLabels[$course->level] ?? $course->level }}
                                    </span>
                                </div>

                                <ul class="list-unstyled mb-3">
                                    <li><i class="bi bi-clock"></i> {{ $course->duration_weeks }} tuần</li>
                                    <li><i class="bi bi-currency-dollar"></i> {{ number_format($course->price, 0, ',', '.') }} VNĐ</li>
                                    <li><i class="bi bi-people"></i> {{ $course->classes_count }} lớp đang mở</li>
                                </ul>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <a href="{{ route('courses.detail', $course->id) }}" class="btn btn-primary w-100">
                                    <i class="bi bi-eye"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                @auth
                    @if(auth()->user()->role === 'student')
                        <a href="{{ route('courses.browse') }}" class="btn btn-primary btn-lg">
                            Xem tất cả khóa học <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                @else
                    <a href="{{ route('courses.browse') }}" class="btn btn-outline-primary btn-lg me-2">
                        Xem tất cả khóa học <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        Đăng ký ngay <i class="bi bi-person-plus"></i>
                    </a>
                @endauth
            </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; {{ date('Y') }} Trung tâm ngoại ngữ. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
