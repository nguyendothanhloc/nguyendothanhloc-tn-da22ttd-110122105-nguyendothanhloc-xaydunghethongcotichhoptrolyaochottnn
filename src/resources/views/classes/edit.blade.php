<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chỉnh sửa lớp học') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                    </div>

                    <form action="{{ route('classes.update', $class->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="course_id" class="form-label">Khóa học <span class="text-danger">*</span></label>
                            <select class="form-select @error('course_id') is-invalid @enderror" 
                                    id="course_id" name="course_id" required>
                                <option value="">-- Chọn khóa học --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" 
                                            {{ (old('course_id', $class->course_id) == $course->id) ? 'selected' : '' }}>
                                        {{ $course->name }} ({{ $course->language }} - {{ ucfirst($course->level) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="teacher_id" class="form-label">Giáo viên <span class="text-danger">*</span></label>
                            <select class="form-select @error('teacher_id') is-invalid @enderror" 
                                    id="teacher_id" name="teacher_id" required>
                                <option value="">-- Chọn giáo viên --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" 
                                            {{ (old('teacher_id', $class->teacher_id) == $teacher->id) ? 'selected' : '' }}>
                                        {{ $teacher->user->name }}
                                        @if($teacher->specialization)
                                            - {{ $teacher->specialization }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Tên lớp học <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $class->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" 
                                       value="{{ old('start_date', $class->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Định dạng: YYYY-MM-DD (Ví dụ: 2026-06-01)</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" 
                                       value="{{ old('end_date', $class->end_date->format('Y-m-d')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Phải sau ngày bắt đầu</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="max_capacity" class="form-label">Sức chứa tối đa <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('max_capacity') is-invalid @enderror" 
                                   id="max_capacity" name="max_capacity" 
                                   value="{{ old('max_capacity', $class->max_capacity) }}" 
                                   min="1" required>
                            @error('max_capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Hiện tại: {{ $class->current_enrollment }} học viên đã đăng ký</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="shift" class="form-label">Ca học</label>
                                <select class="form-select @error('shift') is-invalid @enderror" 
                                        id="shift" name="shift">
                                    <option value="">-- Không chọn --</option>
                                    <option value="morning" {{ old('shift', $class->shift) == 'morning' ? 'selected' : '' }}>Sáng (7:00 - 11:00)</option>
                                    <option value="afternoon" {{ old('shift', $class->shift) == 'afternoon' ? 'selected' : '' }}>Chiều (13:00 - 17:00)</option>
                                    <option value="evening" {{ old('shift', $class->shift) == 'evening' ? 'selected' : '' }}>Tối (18:00 - 21:00)</option>
                                </select>
                                @error('shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Khung giờ học chính của lớp</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Thứ trong tuần</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @php
                                        $weekdays = [
                                            '2' => 'T2',
                                            '3' => 'T3',
                                            '4' => 'T4',
                                            '5' => 'T5',
                                            '6' => 'T6',
                                            '7' => 'T7',
                                            '8' => 'CN'
                                        ];
                                        $selectedWeekdays = old('weekdays', $class->weekdays ? explode(',', $class->weekdays) : []);
                                    @endphp
                                    @foreach($weekdays as $value => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="weekdays[]" value="{{ $value }}" 
                                                   id="weekday{{ $value }}"
                                                   {{ in_array($value, $selectedWeekdays) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="weekday{{ $value }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('weekdays')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Chọn các ngày học trong tuần</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="upcoming" {{ old('status', $class->status) == 'upcoming' ? 'selected' : '' }}>
                                    Sắp diễn ra
                                </option>
                                <option value="ongoing" {{ old('status', $class->status) == 'ongoing' ? 'selected' : '' }}>
                                    Đang diễn ra
                                </option>
                                <option value="completed" {{ old('status', $class->status) == 'completed' ? 'selected' : '' }}>
                                    Đã hoàn thành
                                </option>
                                <option value="cancelled" {{ old('status', $class->status) == 'cancelled' ? 'selected' : '' }}>
                                    Đã hủy
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('classes.index') }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
