<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Quản lý giáo viên</h2>
            <div>
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
                <a href="{{ route('courses.index') }}" class="btn btn-info me-2">
                    <i class="bi bi-book"></i> Quản lý khóa học
                </a>
                <a href="{{ route('classes.index') }}" class="btn btn-secondary me-2">
                    <i class="bi bi-calendar3"></i> Quản lý lớp học
                </a>
                <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Tạo tài khoản giáo viên
                </a>
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

            <!-- Teachers Table -->
            <div class="card">
                <div class="card-body">
                    @if($teachers->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Chưa có giáo viên nào.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tên giáo viên</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Chuyên môn</th>
                                        <th>Trạng thái</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teachers as $teacher)
                                        <tr>
                                            <td>
                                                <strong>{{ $teacher->user->name }}</strong>
                                            </td>
                                            <td>{{ $teacher->user->email }}</td>
                                            <td>{{ $teacher->user->phone ?? '-' }}</td>
                                            <td>{{ $teacher->specialization ?? '-' }}</td>
                                            <td>
                                                @if($teacher->user->is_active)
                                                    <span class="badge bg-success">Đang hoạt động</span>
                                                @else
                                                    <span class="badge bg-secondary">Đã vô hiệu hóa</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <!-- Edit -->
                                                    <a href="{{ route('teachers.edit', $teacher->id) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Sửa">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>

                                                    <!-- Toggle Status -->
                                                    <form action="{{ route('teachers.toggle-status', $teacher->id) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="btn btn-sm {{ $teacher->user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                                title="{{ $teacher->user->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}">
                                                            <i class="bi {{ $teacher->user->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Delete -->
                                                    <form action="{{ route('teachers.destroy', $teacher->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản giáo viên này? Hành động này không thể hoàn tác.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
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
</x-app-layout>
