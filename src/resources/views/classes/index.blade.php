<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý lớp học') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>Danh sách lớp học</h4>
                        <div>
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-book me-1"></i>Quản lý khóa học
                            </a>
                            <a href="{{ route('classes.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Tạo lớp học mới
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($classes->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Chưa có lớp học nào. Hãy tạo lớp học mới!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tên lớp</th>
                                        <th>Khóa học</th>
                                        <th>Giáo viên</th>
                                        <th>Thời gian</th>
                                        <th>Học viên</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classes as $class)
                                        <tr>
                                            <td><strong>{{ $class->name }}</strong></td>
                                            <td>{{ $class->course->name }}</td>
                                            <td>{{ $class->teacher->user->name }}</td>
                                            <td>
                                                <small>
                                                    {{ $class->start_date->format('d/m/Y') }}<br>
                                                    {{ $class->end_date->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                {{ $class->current_enrollment }} / {{ $class->max_capacity }}
                                                <div class="progress mt-1" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ ($class->current_enrollment / $class->max_capacity) * 100 }}%">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($class->status === 'upcoming')
                                                    <span class="badge bg-secondary">Sắp diễn ra</span>
                                                @elseif($class->status === 'ongoing')
                                                    <span class="badge bg-success">Đang diễn ra</span>
                                                @elseif($class->status === 'completed')
                                                    <span class="badge bg-dark">Hoàn thành</span>
                                                @else
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.classes.show', $class->id) }}" 
                                                       class="btn btn-sm btn-info" title="Xem chi tiết">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('classes.edit', $class->id) }}" 
                                                       class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('classes.destroy', $class->id) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa lớp học này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
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

                        <div class="mt-4">
                            {{ $classes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
