<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Quản lý khóa học</h2>
            <div>
                @if(auth()->user()->role === 'admin')
                    @php
                        $pendingCount = \App\Models\Enrollment::where('status', 'pending')->count();
                    @endphp
                    <a href="{{ route('enrollments.admin') }}" class="btn btn-warning me-2 position-relative">
                        <i class="bi bi-inbox"></i> Đăng ký mới
                        @if($pendingCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('teachers.index') }}" class="btn btn-info me-2">
                        <i class="bi bi-people"></i> Quản lý giáo viên
                    </a>
                    <a href="{{ route('classes.index') }}" class="btn btn-secondary me-2">
                        <i class="bi bi-calendar3"></i> Quản lý lớp học
                    </a>
                    <a href="{{ route('courses.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tạo khóa học mới
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('courses.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="language" class="form-label">Ngôn ngữ</label>
                            <select name="language" id="language" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="English" {{ request('language') === 'English' ? 'selected' : '' }}>English</option>
                                <option value="Japanese" {{ request('language') === 'Japanese' ? 'selected' : '' }}>Japanese</option>
                                <option value="Korean" {{ request('language') === 'Korean' ? 'selected' : '' }}>Korean</option>
                                <option value="Chinese" {{ request('language') === 'Chinese' ? 'selected' : '' }}>Chinese</option>
                                <option value="French" {{ request('language') === 'French' ? 'selected' : '' }}>French</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="level" class="form-label">Trình độ</label>
                            <select name="level" id="level" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="elementary" {{ request('level') === 'elementary' ? 'selected' : '' }}>Elementary</option>
                                <option value="intermediate" {{ request('level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ request('level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Đã vô hiệu hóa</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-funnel"></i> Lọc
                            </button>
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Xóa bộ lọc
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Courses Table -->
            <div class="card">
                <div class="card-body">
                    @if($courses->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Không có khóa học nào.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tên khóa học</th>
                                        <th>Ngôn ngữ</th>
                                        <th>Trình độ</th>
                                        <th>Thời lượng</th>
                                        <th>Giá</th>
                                        <th>Trạng thái</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                        <tr>
                                            <td>
                                                <strong>{{ $course->name }}</strong>
                                                @if($course->description)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($course->description, 60) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $course->language }}</td>
                                            <td>
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
                                            </td>
                                            <td>{{ $course->duration_weeks }} tuần</td>
                                            <td>{{ number_format($course->price, 0, ',', '.') }} VNĐ</td>
                                            <td>
                                                @if($course->is_active)
                                                    <span class="badge bg-success">Đang hoạt động</span>
                                                @else
                                                    <span class="badge bg-secondary">Đã vô hiệu hóa</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if(auth()->user()->role === 'admin')
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('courses.edit', $course->id) }}" 
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Chỉnh sửa">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        @if($course->is_active)
                                                            <form action="{{ route('courses.deactivate', $course->id) }}" 
                                                                  method="POST" 
                                                                  class="d-inline"
                                                                  onsubmit="return confirm('Bạn có chắc chắn muốn vô hiệu hóa khóa học này?')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" 
                                                                        class="btn btn-sm btn-outline-danger"
                                                                        title="Vô hiệu hóa">
                                                                    <i class="bi bi-x-circle"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @else
                                                    <a href="{{ route('courses.edit', $course->id) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i> Xem chi tiết
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if(method_exists($courses, 'links'))
                            <div class="mt-3">
                                {{ $courses->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
