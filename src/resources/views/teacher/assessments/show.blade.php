<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Nhập điểm - {{ $assessment->name }}</h2>
            <a href="{{ route('teacher.assessments.index', $class->id) }}" class="btn btn-secondary">
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Assessment Info -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Thông tin bài kiểm tra</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Lớp học:</strong><br>
                            {{ $class->name }}
                        </div>
                        <div class="col-md-3">
                            <strong>Loại:</strong><br>
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
                        </div>
                        <div class="col-md-3">
                            <strong>Điểm tối đa:</strong><br>
                            {{ $assessment->max_score }}
                        </div>
                        <div class="col-md-3">
                            <strong>Ngày kiểm tra:</strong><br>
                            {{ $assessment->assessment_date->format('d/m/Y') }}
                        </div>
                    </div>
                    @if($assessment->description)
                        <hr>
                        <div>
                            <strong>Mô tả:</strong><br>
                            {{ $assessment->description }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Scores Form -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Nhập điểm học viên</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.assessments.storeScores', [$class->id, $assessment->id]) }}" method="POST">
                        @csrf
                        
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Họ và tên</th>
                                        <th width="20%">Email</th>
                                        <th width="20%">Điểm (0-{{ $assessment->max_score }})</th>
                                        <th width="30%">Nhận xét</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                        @php
                                            $score = $student->assessmentScores->first();
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $student->user->name }}</strong>
                                                <input type="hidden" name="scores[{{ $index }}][student_id]" value="{{ $student->id }}">
                                            </td>
                                            <td>{{ $student->user->email }}</td>
                                            <td>
                                                <input type="number" 
                                                       name="scores[{{ $index }}][score]" 
                                                       class="form-control" 
                                                       min="0" 
                                                       max="{{ $assessment->max_score }}" 
                                                       step="0.5"
                                                       value="{{ $score->score ?? '' }}"
                                                       placeholder="0-{{ $assessment->max_score }}"
                                                       required>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       name="scores[{{ $index }}][feedback]" 
                                                       class="form-control form-control-sm" 
                                                       placeholder="Nhận xét (tùy chọn)"
                                                       value="{{ $score->feedback ?? '' }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Lưu điểm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
