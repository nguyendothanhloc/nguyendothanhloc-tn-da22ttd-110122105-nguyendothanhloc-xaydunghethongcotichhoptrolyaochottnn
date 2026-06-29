<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Thời khóa biểu</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <!-- Attendance Statistics by Class -->
            @if(!empty($attendanceStats) && count($attendanceStats) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Thống kê điểm danh theo lớp</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($attendanceStats as $classId => $stats)
                                    @php
                                        $class = $schedules->where('class_id', $classId)->first()->class ?? null;
                                    @endphp
                                    @if($class)
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-primary">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary">
                                                    <i class="bi bi-book"></i> {{ $class->name }}
                                                </h6>
                                                <p class="card-text small text-muted mb-2">{{ $class->course->name }}</p>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-muted">Tổng buổi học:</span>
                                                    <strong>{{ $stats['total'] }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-success">Số buổi có mặt:</span>
                                                    <strong class="text-success">{{ $stats['present'] }}</strong>
                                                </div>
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar 
                                                        @if($stats['percentage'] >= 80) bg-success 
                                                        @elseif($stats['percentage'] >= 60) bg-warning 
                                                        @else bg-danger 
                                                        @endif" 
                                                        role="progressbar" 
                                                        style="width: {{ $stats['percentage'] }}%;" 
                                                        aria-valuenow="{{ $stats['percentage'] }}" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100">
                                                        <strong>{{ $stats['percentage'] }}%</strong>
                                                    </div>
                                                </div>
                                                @if($stats['percentage'] < 80)
                                                    <small class="text-danger mt-2 d-block">
                                                        <i class="bi bi-exclamation-triangle"></i> Cần đạt ≥80% để nhận chứng chỉ
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Calendar View -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-calendar-month"></i> Lịch học tháng {{ now()->format('m/Y') }}</h5>
                            <div>
                                <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-light">
                                    <i class="bi bi-house"></i> Về trang chủ
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($schedules->isEmpty())
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Chưa có lịch học nào.
                                </div>
                            @else
                                <!-- Filter by Week -->
                                <div class="mb-3">
                                    <label class="form-label">Chọn tuần:</label>
                                    <select class="form-select" id="weekFilter" onchange="filterByWeek(this.value)">
                                        <option value="all">Tất cả</option>
                                        @php
                                            $weeks = $schedules->groupBy(function($schedule) {
                                                return \Carbon\Carbon::parse($schedule->date)->weekOfYear;
                                            });
                                        @endphp
                                        @foreach($weeks as $weekNum => $weekSchedules)
                                            @php
                                                $firstDate = \Carbon\Carbon::parse($weekSchedules->first()->date);
                                                $lastDate = \Carbon\Carbon::parse($weekSchedules->last()->date);
                                            @endphp
                                            <option value="week-{{ $weekNum }}">
                                                Tuần {{ $weekNum }}: {{ $firstDate->format('d/m') }} - {{ $lastDate->format('d/m') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Schedule Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="13%">Ngày</th>
                                                <th width="10%">Thời gian</th>
                                                <th width="18%">Khóa học</th>
                                                <th width="13%">Lớp</th>
                                                <th width="13%">Phòng học</th>
                                                <th width="15%">Chủ đề</th>
                                                <th width="10%">Trạng thái</th>
                                                <th width="8%">Điểm danh</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schedules as $schedule)
                                                @php
                                                    $date = \Carbon\Carbon::parse($schedule->date);
                                                    $weekNum = $date->weekOfYear;
                                                    $isToday = $date->isToday();
                                                    $isPast = $date->isPast();
                                                    // Get attendance status for this schedule
                                                    $attendance = $schedule->attendances->first();
                                                @endphp
                                                <tr class="week-{{ $weekNum }} {{ $isToday ? 'table-warning' : '' }}" data-week="{{ $weekNum }}">
                                                    <td>
                                                        <strong>{{ $date->locale('vi')->isoFormat('dddd') }}</strong><br>
                                                        <small>{{ $date->format('d/m/Y') }}</small>
                                                        @if($isToday)
                                                            <br><span class="badge bg-danger">Hôm nay</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary">
                                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}<br>
                                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $schedule->class->course->name }}</strong><br>
                                                        <small class="text-muted">{{ $schedule->class->course->language }}</small>
                                                    </td>
                                                    <td>
                                                        {{ $schedule->class->name }}<br>
                                                        <small class="text-muted">GV: {{ $schedule->class->teacher->user->name }}</small>
                                                    </td>
                                                    <td>
                                                        <i class="bi bi-geo-alt-fill text-danger"></i> {{ $schedule->location }}
                                                    </td>
                                                    <td>{{ $schedule->topic }}</td>
                                                    <td class="text-center">
                                                        @if($schedule->status === 'completed')
                                                            <span class="badge bg-success">Đã học</span>
                                                        @elseif($schedule->status === 'cancelled')
                                                            <span class="badge bg-danger">Hủy</span>
                                                        @else
                                                            <span class="badge bg-info">Sắp tới</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($attendance)
                                                            @if($attendance->status === 'present')
                                                                <span class="badge bg-success" title="Có mặt">✓</span>
                                                            @elseif($attendance->status === 'absent')
                                                                <span class="badge bg-danger" title="Vắng mặt">✗</span>
                                                            @elseif($attendance->status === 'late')
                                                                <span class="badge bg-warning" title="Đi muộn">⏰</span>
                                                            @elseif($attendance->status === 'excused')
                                                                <span class="badge bg-info" title="Có phép">📝</span>
                                                            @endif
                                                        @else
                                                            @if($isPast && $schedule->status === 'completed')
                                                                <span class="badge bg-secondary" title="Chưa điểm danh">-</span>
                                                            @else
                                                                <span class="text-muted" title="Chưa diễn ra">-</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Legend -->
                                <div class="mt-3">
                                    <h6>Chú thích trạng thái buổi học:</h6>
                                    <span class="badge bg-danger me-2">Hôm nay</span>
                                    <span class="badge bg-success me-2">Đã học</span>
                                    <span class="badge bg-info me-2">Sắp tới</span>
                                    <span class="badge bg-danger">Hủy</span>
                                    
                                    <h6 class="mt-3">Chú thích điểm danh:</h6>
                                    <span class="badge bg-success me-2">✓ Có mặt</span>
                                    <span class="badge bg-danger me-2">✗ Vắng mặt</span>
                                    <span class="badge bg-warning me-2">⏰ Đi muộn</span>
                                    <span class="badge bg-info me-2">📝 Có phép</span>
                                    <span class="badge bg-secondary">- Chưa điểm danh</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterByWeek(weekValue) {
            const allRows = document.querySelectorAll('tbody tr[data-week]');
            
            if (weekValue === 'all') {
                allRows.forEach(row => row.style.display = '');
            } else {
                const weekNum = weekValue.replace('week-', '');
                allRows.forEach(row => {
                    if (row.dataset.week === weekNum) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        }
    </script>
</x-app-layout>
