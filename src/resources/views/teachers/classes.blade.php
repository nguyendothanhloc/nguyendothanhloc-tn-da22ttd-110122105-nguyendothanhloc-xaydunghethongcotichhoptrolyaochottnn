<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Lớp học của tôi') }}
            </h2>
            <a href="{{ route('teacher.schedule') }}" class="btn btn-success btn-sm">
                <i class="bi bi-calendar-week"></i> Xem lịch dạy
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($classes->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Bạn chưa được phân công lớp học nào.
                        </div>
                    @else
                        <div class="row row-cols-1 row-cols-md-2 g-4">
                            @foreach($classes as $class)
                                <div class="col">
                                    <div class="card h-100">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="card-title mb-0">{{ $class->name }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="text-muted mb-3">
                                                <i class="bi bi-book me-2"></i>{{ $class->course->name }}
                                            </h6>
                                            
                                            <div class="mb-2">
                                                <strong>Ngôn ngữ:</strong> {{ $class->course->language }}
                                            </div>
                                            <div class="mb-2">
                                                <strong>Trình độ:</strong> 
                                                <span class="badge bg-info">{{ ucfirst($class->course->level) }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Thời gian:</strong> 
                                                {{ $class->start_date->format('d/m/Y') }} - {{ $class->end_date->format('d/m/Y') }}
                                            </div>
                                            <div class="mb-2">
                                                <strong>Học viên:</strong> 
                                                {{ $class->current_enrollment }} / {{ $class->max_capacity }}
                                                <div class="progress mt-1" style="height: 8px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ ($class->current_enrollment / $class->max_capacity) * 100 }}%">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Trạng thái:</strong>
                                                @if($class->status === 'upcoming')
                                                    <span class="badge bg-secondary">Sắp diễn ra</span>
                                                @elseif($class->status === 'ongoing')
                                                    <span class="badge bg-success">Đang diễn ra</span>
                                                @elseif($class->status === 'completed')
                                                    <span class="badge bg-dark">Đã hoàn thành</span>
                                                @else
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <a href="{{ route('classes.show', $class->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye me-1"></i>Xem chi tiết
                                            </a>
                                            <a href="{{ route('teacher.attendance.index', $class->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-check2-square me-1"></i>Điểm danh
                                            </a>
                                            <a href="{{ route('teacher.assessments.index', $class->id) }}" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-clipboard-check me-1"></i>Quản lý điểm
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
