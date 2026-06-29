<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Lịch dạy của tôi</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <!-- Calendar View -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Lịch giảng dạy tháng {{ now()->format('m/Y') }}</h5>
                            <div>
                                <a href="{{ route('teacher.classes') }}" class="btn btn-sm btn-light">
                                    <i class="bi bi-house"></i> Về trang chủ
                                    </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($schedules->isEmpty())
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Chưa có lịch dạy nào.
                                </div>
                            @else
                                <!-- Statistics -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="card border-info">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-1">Tổng buổi học</h6>
                                                <h3 class="mb-0 text-info">{{ $schedules->count() }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border-success">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-1">Đã hoàn thành</h6>
                                                <h3 class="mb-0 text-success">{{ $schedules->where('status', 'completed')->count() }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border-warning">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-1">Sắp tới</h6>
                                                <h3 class="mb-0 text-warning">{{ $schedules->where('status', 'scheduled')->count() }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border-primary">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-1">Lớp dạy</h6>
                                                <h3 class="mb-0 text-primary">{{ $schedules->unique('class_id')->count() }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                                                <th width="15%">Ngày</th>
                                                <th width="10%">Thời gian</th>
                                                <th width="18%">Lớp học</th>
                                                <th width="15%">Phòng học</th>
                                                <th width="18%">Chủ đề</th>
                                                <th width="10%">Trạng thái</th>
                                                <th width="14%">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schedules as $schedule)
                                                @php
                                                    $date = \Carbon\Carbon::parse($schedule->date);
                                                    $weekNum = $date->weekOfYear;
                                                    $isToday = $date->isToday();
                                                    $isPast = $date->isPast();
                                                @endphp
                                                <tr class="week-{{ $weekNum }} {{ $isToday ? 'table-success' : '' }}" data-week="{{ $weekNum }}">
                                                    <td>
                                                        <strong>{{ $date->locale('vi')->isoFormat('dddd') }}</strong><br>
                                                        <small>{{ $date->format('d/m/Y') }}</small>
                                                        @if($isToday)
                                                            <br><span class="badge bg-danger">Hôm nay</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success">
                                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}<br>
                                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $schedule->class->name }}</strong><br>
                                                        <small class="text-muted">{{ $schedule->class->course->name }}</small>
                                                    </td>
                                                    <td>
                                                        <i class="bi bi-geo-alt-fill text-danger"></i> {{ $schedule->location }}
                                                    </td>
                                                    <td>{{ $schedule->topic }}</td>
                                                    <td class="text-center">
                                                        @if($schedule->status === 'completed')
                                                            <span class="badge bg-success">Đã dạy</span>
                                                        @elseif($schedule->status === 'cancelled')
                                                            <span class="badge bg-danger">Hủy</span>
                                                        @else
                                                            <span class="badge bg-info">Sắp tới</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            @if($schedule->status === 'scheduled' && $date->isToday())
                                                                <a href="{{ route('teacher.attendance.show', ['classId' => $schedule->class_id, 'scheduleId' => $schedule->id]) }}" 
                                                                   class="btn btn-primary"
                                                                   title="Điểm danh">
                                                                    <i class="bi bi-check2-square"></i>
                                                                </a>
                                                            @endif
                                                            <a href="{{ route('classes.show', $schedule->class_id) }}" 
                                                               class="btn btn-info"
                                                               title="Xem lớp">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Legend -->
                                <div class="mt-3">
                                    <h6>Chú thích:</h6>
                                    <span class="badge bg-danger me-2">Hôm nay</span>
                                    <span class="badge bg-success me-2">Đã dạy</span>
                                    <span class="badge bg-info me-2">Sắp tới</span>
                                    <span class="badge bg-danger">Hủy</span>
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
