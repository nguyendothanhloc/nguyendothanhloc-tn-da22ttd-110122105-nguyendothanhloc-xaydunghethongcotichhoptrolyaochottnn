<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Lớp học của tôi</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($enrollments->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">Bạn chưa đăng ký lớp học nào.</p>
                        <a href="{{ route('courses.browse') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-search"></i> Khám phá khóa học
                        </a>
                    </div>
                </div>
            @else
                @foreach ($enrollments as $enrollment)
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-0">
                                        <i class="bi bi-book"></i> {{ $enrollment->class->course->name }} - {{ $enrollment->class->name }}
                                    </h5>
                                    <small>Giáo viên: {{ $enrollment->class->teacher->user->name }}</small>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    @if($enrollment->status === 'paid')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Đã thanh toán
                                        </span>
                                    @elseif($enrollment->status === 'pending')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock"></i> Chờ thanh toán
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Đã hủy
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Class Info -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <strong><i class="bi bi-calendar-range"></i> Thời gian:</strong><br>
                                    {{ $enrollment->class->start_date->format('d/m/Y') }} - {{ $enrollment->class->end_date->format('d/m/Y') }}
                                </div>
                                <div class="col-md-3">
                                    <strong><i class="bi bi-people"></i> Sĩ số:</strong><br>
                                    {{ $enrollment->class->current_enrollment }} / {{ $enrollment->class->max_capacity }} học viên
                                </div>
                                <div class="col-md-3">
                                    <strong><i class="bi bi-calendar-check"></i> Ngày đăng ký:</strong><br>
                                    {{ $enrollment->enrollment_date->format('d/m/Y') }}
                                </div>
                                <div class="col-md-3">
                                    <strong><i class="bi bi-graph-up"></i> Hoàn thành:</strong><br>
                                    {{ $enrollment->completion_percentage ?? 0 }}%
                                </div>
                            </div>

                            <ul class="nav nav-tabs" id="classTab{{ $enrollment->id }}" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="schedule-tab{{ $enrollment->id }}" 
                                            data-bs-toggle="tab" data-bs-target="#schedule{{ $enrollment->id }}" 
                                            type="button" role="tab">
                                        <i class="bi bi-calendar3"></i> Lịch học sắp tới
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="grades-tab{{ $enrollment->id }}" 
                                            data-bs-toggle="tab" data-bs-target="#grades{{ $enrollment->id }}" 
                                            type="button" role="tab">
                                        <i class="bi bi-clipboard-data"></i> Kết quả đánh giá
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content mt-3" id="classTabContent{{ $enrollment->id }}">
                                <!-- Schedule Tab -->
                                <div class="tab-pane fade show active" id="schedule{{ $enrollment->id }}" role="tabpanel">
                                    @if($enrollment->class->schedules->isEmpty())
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i> Chưa có lịch học sắp tới.
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Ngày học</th>
                                                        <th>Thời gian</th>
                                                        <th>Chủ đề</th>
                                                        <th>Địa điểm</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($enrollment->class->schedules->take(5) as $schedule)
                                                        <tr class="{{ \Carbon\Carbon::parse($schedule->date)->isToday() ? 'table-warning' : '' }}">
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}
                                                                @if(\Carbon\Carbon::parse($schedule->date)->isToday())
                                                                    <span class="badge bg-warning text-dark">Hôm nay</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                                                            <td>{{ $schedule->topic ?? '-' }}</td>
                                                            <td>{{ $schedule->location ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($enrollment->class->schedules->count() > 5)
                                            <p class="text-muted text-center">
                                                <small>Hiển thị 5 buổi học gần nhất</small>
                                            </p>
                                        @endif
                                    @endif
                                </div>

                                <!-- Grades Tab -->
                                <div class="tab-pane fade" id="grades{{ $enrollment->id }}" role="tabpanel">
                                    @php
                                        $classScores = $assessmentScores[$enrollment->class_id] ?? collect();
                                    @endphp

                                    @if($classScores->isEmpty())
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i> Chưa có kết quả đánh giá nào.
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Bài kiểm tra</th>
                                                        <th>Loại</th>
                                                        <th>Ngày</th>
                                                        <th>Điểm</th>
                                                        <th>Đánh giá</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($classScores as $score)
                                                        <tr>
                                                            <td><strong>{{ $score->assessment->name }}</strong></td>
                                                            <td>
                                                                @if($score->assessment->type === 'midterm')
                                                                    <span class="badge bg-info">Giữa kỳ</span>
                                                                @elseif($score->assessment->type === 'final')
                                                                    <span class="badge bg-danger">Cuối kỳ</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Thường xuyên</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $score->assessment->assessment_date ? $score->assessment->assessment_date->format('d/m/Y') : '-' }}</td>
                                                            <td>
                                                                <strong>{{ $score->score }} / {{ $score->assessment->max_score }}</strong>
                                                                @php
                                                                    $percentage = ($score->score / $score->assessment->max_score) * 100;
                                                                @endphp
                                                                @if($percentage >= 80)
                                                                    <span class="badge bg-success">Giỏi</span>
                                                                @elseif($percentage >= 65)
                                                                    <span class="badge bg-primary">Khá</span>
                                                                @elseif($percentage >= 50)
                                                                    <span class="badge bg-warning text-dark">Trung bình</span>
                                                                @else
                                                                    <span class="badge bg-danger">Yếu</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">{{ $score->feedback ?? 'Chưa có nhận xét' }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        @php
                                            $avgScore = $classScores->avg(function($score) {
                                                return ($score->score / $score->assessment->max_score) * 100;
                                            });
                                        @endphp
                                        <div class="alert alert-secondary">
                                            <strong><i class="bi bi-calculator"></i> Điểm trung bình:</strong> {{ number_format($avgScore, 1) }}%
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('enrollments.show', $enrollment->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Xem chi tiết
                            </a>
                            @if($enrollment->status !== 'cancelled')
                                <form action="{{ route('enrollments.destroy', $enrollment->id) }}" method="POST" 
                                      class="d-inline" onsubmit="return confirm('Bạn có chắc muốn hủy đăng ký lớp học này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-circle"></i> Hủy đăng ký
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
