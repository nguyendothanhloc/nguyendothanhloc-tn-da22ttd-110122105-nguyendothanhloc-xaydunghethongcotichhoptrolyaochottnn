<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chi tiết lớp học') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ auth()->user()->role === 'teacher' ? route('teacher.classes') : route('classes.index') }}" 
                           class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">{{ $class->name }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3">Thông tin khóa học</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Khóa học:</strong></td>
                                            <td>{{ $class->course->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngôn ngữ:</strong></td>
                                            <td>{{ $class->course->language }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trình độ:</strong></td>
                                            <td><span class="badge bg-info">{{ ucfirst($class->course->level) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Thời lượng:</strong></td>
                                            <td>{{ $class->course->duration_weeks }} tuần</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Học phí:</strong></td>
                                            <td class="text-danger fw-bold">{{ number_format($class->course->price, 0, ',', '.') }} VNĐ</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3">Thông tin lớp học</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Giáo viên:</strong></td>
                                            <td>{{ $class->teacher->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày bắt đầu:</strong></td>
                                            <td>{{ $class->start_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày kết thúc:</strong></td>
                                            <td>{{ $class->end_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sức chứa:</strong></td>
                                            <td>{{ $class->max_capacity }} học viên</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Đã đăng ký:</strong></td>
                                            <td>
                                                {{ $class->current_enrollment }} / {{ $class->max_capacity }}
                                                <div class="progress mt-1" style="height: 8px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ ($class->current_enrollment / $class->max_capacity) * 100 }}%">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trạng thái:</strong></td>
                                            <td>
                                                @if($class->status === 'upcoming')
                                                    <span class="badge bg-secondary">Sắp diễn ra</span>
                                                @elseif($class->status === 'ongoing')
                                                    <span class="badge bg-success">Đang diễn ra</span>
                                                @elseif($class->status === 'completed')
                                                    <span class="badge bg-dark">Đã hoàn thành</span>
                                                @else
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Danh sách học viên ({{ $class->enrollments->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if($class->enrollments->isEmpty())
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Chưa có học viên nào đăng ký lớp học này.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Họ tên</th>
                                                <th>Email</th>
                                                <th>Trình độ</th>
                                                <th>Ngày đăng ký</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($class->enrollments as $index => $enrollment)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $enrollment->student->user->name }}</td>
                                                    <td>{{ $enrollment->student->user->email }}</td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            {{ ucfirst($enrollment->student->level) }}
                                                        </span>
                                                    </td>
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
