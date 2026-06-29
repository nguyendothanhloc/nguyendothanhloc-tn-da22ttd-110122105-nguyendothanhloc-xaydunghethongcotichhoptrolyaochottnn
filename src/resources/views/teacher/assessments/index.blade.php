<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Quản lý bài kiểm tra - {{ $class->name }}</h2>
            <div>
                <a href="{{ route('teacher.assessments.create', $class->id) }}" class="btn btn-success me-2">
                    <i class="bi bi-plus-circle"></i> Tạo bài kiểm tra mới
                </a>
                <a href="{{ route('classes.show', $class->id) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
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
                        <div class="col-md-6">
                            <strong>Khóa học:</strong> {{ $class->course->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Số học viên:</strong> {{ $class->current_enrollment }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessments List -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Danh sách bài kiểm tra</h5>
                </div>
                <div class="card-body">
                    @if($assessments->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Chưa có bài kiểm tra nào.
                            <a href="{{ route('teacher.assessments.create', $class->id) }}" class="alert-link">
                                Tạo bài kiểm tra đầu tiên
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tên bài kiểm tra</th>
                                        <th>Loại</th>
                                        <th>Điểm tối đa</th>
                                        <th>Ngày kiểm tra</th>
                                        <th>Đã chấm điểm</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assessments as $assessment)
                                        @php
                                            $scoredCount = $assessment->scores()->count();
                                            $totalStudents = $class->current_enrollment;
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $assessment->name }}</strong></td>
                                            <td>
                                                @if($assessment->type === 'quiz')
                                                    <span class="badge bg-info">Kiểm tra</span>
                                                @elseif($assessment->type === 'midterm')
                                                    <span class="badge bg-warning">Giữa kỳ</span>
                                                @elseif($assessment->type === 'final')
                                                    <span class="badge bg-danger">Cuối kỳ</span>
                                                @elseif($assessment->type === 'assignment')
                                                    <span class="badge bg-secondary">Bài tập</span>
                                                @else
                                                    <span class="badge bg-primary">Dự án</span>
                                                @endif
                                            </td>
                                            <td>{{ $assessment->max_score }}</td>
                                            <td>{{ $assessment->assessment_date->format('d/m/Y') }}</td>
                                            <td>
                                                @if($scoredCount > 0)
                                                    <span class="badge bg-success">
                                                        {{ $scoredCount }}/{{ $totalStudents }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Chưa chấm</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('teacher.assessments.show', [$class->id, $assessment->id]) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil-square"></i> 
                                                    {{ $scoredCount > 0 ? 'Xem/Sửa điểm' : 'Nhập điểm' }}
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
