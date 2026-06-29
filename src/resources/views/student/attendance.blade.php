<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Lịch sử điểm danh</h2>
            <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Tổng buổi học</h6>
                            <h2>{{ $stats['total'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Có mặt</h6>
                            <h2 class="text-success">{{ $stats['present'] }}</h2>
                            @if($stats['total'] > 0)
                                <small class="text-muted">{{ round(($stats['present'] / $stats['total']) * 100, 1) }}%</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-danger">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Vắng mặt</h6>
                            <h2 class="text-danger">{{ $stats['absent'] }}</h2>
                            @if($stats['total'] > 0)
                                <small class="text-muted">{{ round(($stats['absent'] / $stats['total']) * 100, 1) }}%</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Đi muộn</h6>
                            <h2 class="text-warning">{{ $stats['late'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance History -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Lịch sử điểm danh</h5>
                </div>
                <div class="card-body">
                    @if($attendances->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Chưa có dữ liệu điểm danh.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Khóa học</th>
                                        <th>Buổi học</th>
                                        <th>Ngày</th>
                                        <th>Trạng thái</th>
                                        <th>Ghi chú</th>
                                        <th>Thời gian ghi nhận</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendances as $attendance)
                                        <tr>
                                            <td>{{ $attendance->schedule->class->course->name }}</td>
                                            <td>{{ $attendance->schedule->topic ?? 'Buổi học' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($attendance->schedule->date)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($attendance->status === 'present')
                                                    <span class="badge bg-success">✓ Có mặt</span>
                                                @elseif($attendance->status === 'absent')
                                                    <span class="badge bg-danger">✗ Vắng mặt</span>
                                                @elseif($attendance->status === 'late')
                                                    <span class="badge bg-warning">⏰ Đi muộn</span>
                                                @else
                                                    <span class="badge bg-info">📝 Có phép</span>
                                                @endif
                                            </td>
                                            <td>{{ $attendance->note ?? '-' }}</td>
                                            <td>{{ $attendance->recorded_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $attendances->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
