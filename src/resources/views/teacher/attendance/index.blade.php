<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Điểm danh - {{ $class->name }}</h2>
            <a href="{{ route('classes.show', $class->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Class Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Khóa học:</strong> {{ $class->course->name }}
                        </div>
                        <div class="col-md-4">
                            <strong>Số học viên:</strong> {{ $students->count() }}
                        </div>
                        <div class="col-md-4">
                            <strong>Trạng thái:</strong>
                            @if($class->status === 'ongoing')
                                <span class="badge bg-success">Đang diễn ra</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($class->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedules List -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar3"></i> Danh sách buổi học</h5>
                </div>
                <div class="card-body">
                    @if($schedules->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Chưa có lịch học nào được tạo cho lớp này.
                            <br><small class="text-muted">Lịch học sẽ được admin tạo sau khi lớp bắt đầu.</small>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Buổi</th>
                                        <th>Ngày học</th>
                                        <th>Thời gian</th>
                                        <th>Phòng học</th>
                                        <th>Đã điểm danh</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $index => $schedule)
                                        @php
                                            $attendanceCount = $schedule->attendances()->count();
                                            $totalStudents = $students->count();
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $schedule->topic ?? 'Buổi ' . ($index + 1) }}</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</td>
                                            <td>{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                                            <td>{{ $schedule->location ?? '-' }}</td>
                                            <td>
                                                @if($attendanceCount > 0)
                                                    <span class="badge bg-success">
                                                        {{ $attendanceCount }}/{{ $totalStudents }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Chưa điểm danh</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('teacher.attendance.show', [$class->id, $schedule->id]) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-check2-square"></i> 
                                                    {{ $attendanceCount > 0 ? 'Xem/Sửa' : 'Điểm danh' }}
                                                </a>
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
