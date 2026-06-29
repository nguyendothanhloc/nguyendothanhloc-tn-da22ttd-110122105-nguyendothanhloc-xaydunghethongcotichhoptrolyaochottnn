<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Điểm danh - {{ $schedule->topic ?? 'Buổi học' }}</h2>
            <a href="{{ route('teacher.attendance.index', $class->id) }}" class="btn btn-secondary">
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

            <!-- Schedule Info -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Thông tin buổi học</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Lớp học:</strong><br>
                            {{ $class->name }}
                        </div>
                        <div class="col-md-3">
                            <strong>Ngày học:</strong><br>
                            {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Thời gian:</strong><br>
                            {{ $schedule->start_time }} - {{ $schedule->end_time }}
                        </div>
                        <div class="col-md-3">
                            <strong>Phòng học:</strong><br>
                            {{ $schedule->location ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Form -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Danh sách điểm danh</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.attendance.store', [$class->id, $schedule->id]) }}" method="POST" id="attendanceForm">
                        @csrf
                        
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="30%">Họ và tên</th>
                                        <th width="25%">Trạng thái</th>
                                        <th width="40%">Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                        @php
                                            $attendance = $student->attendances->first();
                                            $currentStatus = $attendance ? $attendance->status : 'present';
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $student->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $student->user->email }}</small>
                                                <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">
                                                <input type="hidden" name="attendance[{{ $index }}][status]" value="{{ $currentStatus }}" class="status-input">
                                            </td>
                                            <td>
                                                <div class="btn-group w-100" role="group" data-student-index="{{ $index }}">
                                                    <button type="button" 
                                                            class="btn btn-status btn-present {{ $currentStatus === 'present' ? 'active' : '' }}" 
                                                            data-status="present"
                                                            data-index="{{ $index }}">
                                                        <i class="bi bi-check-circle"></i> Có mặt
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-status btn-absent {{ $currentStatus === 'absent' ? 'active' : '' }}" 
                                                            data-status="absent"
                                                            data-index="{{ $index }}">
                                                        <i class="bi bi-x-circle"></i> Vắng mặt
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       name="attendance[{{ $index }}][note]" 
                                                       class="form-control form-control-sm" 
                                                       placeholder="Ghi chú (tùy chọn)"
                                                       value="{{ $attendance->note ?? '' }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#resetModal">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset điểm danh
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Lưu điểm danh
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Absence History -->
            @if($absentHistory->count() > 0)
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Lịch sử vắng mặt</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($absentHistory as $absence)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="bi bi-person-x text-danger"></i> 
                                            {{ $absence->student->user->name }}
                                        </h6>
                                        <p class="mb-1">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 
                                                Vắng ngày {{ \Carbon\Carbon::parse($absence->schedule->date)->format('d/m/Y') }}
                                                @if($absence->schedule->topic)
                                                    - {{ $absence->schedule->topic }}
                                                @endif
                                            </small>
                                        </p>
                                        @if($absence->note)
                                            <p class="mb-0">
                                                <small>
                                                    <i class="bi bi-sticky"></i> 
                                                    <strong>Ghi chú:</strong> {{ $absence->note }}
                                                </small>
                                            </p>
                                        @endif
                                    </div>
                                    <span class="badge bg-danger">Vắng</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="resetModalLabel">
                        <i class="bi bi-exclamation-triangle"></i> Xác nhận reset điểm danh
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Bạn có chắc chắn muốn xóa tất cả dữ liệu điểm danh của buổi học này?</p>
                    <p class="text-danger mb-0"><strong>Hành động này không thể hoàn tác!</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="{{ route('teacher.attendance.reset', [$class->id, $schedule->id]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Xác nhận xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Button Status Styles */
        .btn-status {
            position: relative;
            transition: all 0.3s ease;
            border: 2px solid #dee2e6;
        }

        .btn-present {
            background-color: #fff;
            color: #198754;
            border-color: #198754;
        }

        .btn-present:hover {
            background-color: #d1e7dd;
            color: #146c43;
        }

        .btn-present.active {
            background-color: #198754;
            color: #fff;
            border-color: #198754;
            font-weight: bold;
        }

        .btn-absent {
            background-color: #fff;
            color: #dc3545;
            border-color: #dc3545;
        }

        .btn-absent:hover {
            background-color: #f8d7da;
            color: #b02a37;
        }

        .btn-absent.active {
            background-color: #dc3545;
            color: #fff;
            border-color: #dc3545;
            font-weight: bold;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .btn-group {
                display: flex;
                flex-direction: column;
            }

            .btn-status {
                width: 100% !important;
                margin-bottom: 5px;
            }

            .table {
                font-size: 0.875rem;
            }
        }

        /* List group item hover effect */
        .list-group-item {
            transition: background-color 0.2s ease;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle button behavior
            const statusButtons = document.querySelectorAll('.btn-status');
            
            statusButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const index = this.dataset.index;
                    const status = this.dataset.status;
                    const btnGroup = this.closest('.btn-group');
                    
                    // Remove active class from all buttons in this group
                    btnGroup.querySelectorAll('.btn-status').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Update hidden input value
                    const hiddenInput = document.querySelector(`input[name="attendance[${index}][status]"]`);
                    if (hiddenInput) {
                        hiddenInput.value = status;
                    }
                });
            });

            // Form validation
            const form = document.getElementById('attendanceForm');
            form.addEventListener('submit', function(e) {
                const statusInputs = document.querySelectorAll('.status-input');
                let isValid = true;

                statusInputs.forEach(input => {
                    if (!input.value) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Vui lòng chọn trạng thái điểm danh cho tất cả học viên!');
                }
            });
        });
    </script>
</x-app-layout>
