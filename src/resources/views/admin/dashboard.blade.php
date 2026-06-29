<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Trang quản trị</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <!-- Welcome Message -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h5><i class="bi bi-person-circle"></i> Xin chào, {{ Auth::user()->name }}!</h5>
                        <p class="mb-0">Chào mừng đến với trang quản trị hệ thống trung tâm ngoại ngữ.</p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <!-- Pending Enrollments -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Đăng ký chờ duyệt</h6>
                                    <h3 class="mb-0">{{ \App\Models\Enrollment::where('status', 'pending')->count() }}</h3>
                                </div>
                                <div class="text-warning">
                                    <i class="bi bi-inbox-fill" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                            <a href="{{ route('enrollments.admin') }}" class="btn btn-sm btn-warning mt-3 w-100">
                                Xem chi tiết <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Total Students -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Tổng học viên</h6>
                                    <h3 class="mb-0">{{ \App\Models\Student::count() }}</h3>
                                </div>
                                <div class="text-success">
                                    <i class="bi bi-person-badge-fill" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-success mt-3 w-100">
                                Quản lý học viên <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Total Courses -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Tổng khóa học</h6>
                                    <h3 class="mb-0">{{ \App\Models\Course::where('is_active', true)->count() }}</h3>
                                </div>
                                <div class="text-info">
                                    <i class="bi bi-book-fill" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                            <a href="{{ route('courses.index') }}" class="btn btn-sm btn-info mt-3 w-100">
                                Quản lý khóa học <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Total Teachers -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Tổng giáo viên</h6>
                                    <h3 class="mb-0">{{ \App\Models\Teacher::count() }}</h3>
                                </div>
                                <div class="text-primary">
                                    <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                            <a href="{{ route('teachers.index') }}" class="btn btn-sm btn-primary mt-3 w-100">
                                Quản lý giáo viên <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-lightning-fill"></i> Thao tác nhanh</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('courses.create') }}" class="btn btn-outline-info w-100 py-3">
                                        <i class="bi bi-plus-circle" style="font-size: 2rem;"></i>
                                        <div class="mt-2">Tạo khóa học mới</div>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('classes.create') }}" class="btn btn-outline-secondary w-100 py-3">
                                        <i class="bi bi-plus-circle" style="font-size: 2rem;"></i>
                                        <div class="mt-2">Tạo lớp học mới</div>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('teachers.create') }}" class="btn btn-outline-primary w-100 py-3">
                                        <i class="bi bi-person-plus" style="font-size: 2rem;"></i>
                                        <div class="mt-2">Tạo tài khoản giáo viên</div>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('enrollments.admin') }}" class="btn btn-outline-warning w-100 py-3 position-relative">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <div class="mt-2">Xem đăng ký mới</div>
                                        @php
                                            $pendingCount = \App\Models\Enrollment::where('status', 'pending')->count();
                                        @endphp
                                        @if($pendingCount > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                {{ $pendingCount }}
                                            </span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Enrollments -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Đăng ký gần đây</h5>
                                <a href="{{ route('enrollments.admin') }}" class="btn btn-sm btn-outline-primary">
                                    Xem tất cả <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                $recentEnrollments = \App\Models\Enrollment::with(['student.user', 'class.course'])
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
                            @endphp

                            @if($recentEnrollments->isEmpty())
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle"></i> Chưa có đăng ký nào.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Học viên</th>
                                                <th>Khóa học</th>
                                                <th>Lớp học</th>
                                                <th>Ngày đăng ký</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentEnrollments as $enrollment)
                                                <tr class="{{ $enrollment->status === 'pending' ? 'table-warning' : '' }}">
                                                    <td>
                                                        <strong>{{ $enrollment->student->user->name }}</strong><br>
                                                        <small class="text-muted">{{ $enrollment->student->user->email }}</small>
                                                    </td>
                                                    <td>{{ $enrollment->class->course->name }}</td>
                                                    <td>{{ $enrollment->class->name }}</td>
                                                    <td>{{ $enrollment->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @if($enrollment->status === 'pending')
                                                            <span class="badge bg-warning">Chờ duyệt</span>
                                                        @elseif($enrollment->status === 'paid')
                                                            <span class="badge bg-success">Đã thanh toán</span>
                                                        @elseif($enrollment->status === 'cancelled')
                                                            <span class="badge bg-danger">Đã hủy</span>
                                                        @endif
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
        </div>
    </div>
</x-app-layout>
