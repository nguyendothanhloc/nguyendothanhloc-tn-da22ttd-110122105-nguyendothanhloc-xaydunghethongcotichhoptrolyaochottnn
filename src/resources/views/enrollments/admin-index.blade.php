<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Quản lý đăng ký học viên
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>
                            Danh sách đăng ký
                            @if($pendingCount > 0)
                                <span class="badge bg-warning">{{ $pendingCount }} mới</span>
                            @endif
                        </h4>
                        <div>
                            <a href="{{ route('classes.index') }}" class="btn btn-secondary me-2">
                                Quản lý lớp học
                            </a>
                            <a href="{{ route('courses.index') }}" class="btn btn-info">
                                Quản lý khóa học
                            </a>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('enrollments.admin') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Tất cả</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="class_id" class="form-label">Lớp học</label>
                                <select name="class_id" id="class_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Tất cả lớp học</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} - {{ $class->course->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <a href="{{ route('enrollments.admin') }}" class="btn btn-secondary">
                                    Xóa bộ lọc
                                </a>
                            </div>
                        </div>
                    </form>

                    @if($enrollments->isEmpty())
                        <div class="alert alert-info">
                            Không có đăng ký nào.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Học viên</th>
                                        <th>Lớp học</th>
                                        <th>Khóa học</th>
                                        <th>Giáo viên</th>
                                        <th>Ngày đăng ký</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        <tr class="{{ $enrollment->status === 'pending' ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>{{ $enrollment->student->user->name }}</strong><br>
                                                <small class="text-muted">{{ $enrollment->student->user->email }}</small><br>
                                                <span class="badge bg-info">{{ ucfirst($enrollment->student->level) }}</span>
                                            </td>
                                            <td>{{ $enrollment->class->name }}</td>
                                            <td>
                                                {{ $enrollment->class->course->name }}<br>
                                                <small class="text-muted">
                                                    {{ $enrollment->class->course->language }} - {{ ucfirst($enrollment->class->course->level) }}
                                                </small>
                                            </td>
                                            <td>{{ $enrollment->class->teacher->user->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($enrollment->enrollment_date)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($enrollment->status === 'pending')
                                                    <span class="badge bg-warning">Chờ thanh toán</span>
                                                @elseif($enrollment->status === 'paid')
                                                    <span class="badge bg-success">Đã thanh toán</span>
                                                @else
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('classes.show', $enrollment->class->id) }}" 
                                                   class="btn btn-sm btn-info mb-1" title="Xem lớp học">
                                                    Xem lớp
                                                </a>
                                                
                                                @if($enrollment->status === 'pending')
                                                    <form action="{{ route('enrollments.approve', $enrollment->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success mb-1" 
                                                                onclick="return confirm('Xác nhận duyệt đăng ký này?')">
                                                            Duyệt
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('enrollments.reject', $enrollment->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-danger mb-1" 
                                                                onclick="return confirm('Xác nhận từ chối đăng ký này?')">
                                                            Từ chối
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $enrollments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
