<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tạo lớp học mới') }}
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

                    <form action="{{ route('classes.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="course_id" class="form-label">Khóa học <span class="text-danger">*</span></label>
                            <select class="form-select @error('course_id') is-invalid @enderror" 
                                    id="course_id" name="course_id" required onchange="filterTeachers()">
                                <option value="">-- Chọn khóa học --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" 
                                            data-language="{{ $course->language }}"
                                            {{ old('course_id') == $course->id ? 'selected' : '' }}>
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
                                <option value="">-- Chọn khóa học trước --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" 
                                            data-specialization="{{ strtolower($teacher->specialization ?? '') }}"
                                            {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
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
                            <small class="text-muted" id="teacher-filter-hint"></small>
                        </div>

                        <script>
                        function filterTeachers() {
                            const courseSelect = document.getElementById('course_id');
                            const teacherSelect = document.getElementById('teacher_id');
                            const hint = document.getElementById('teacher-filter-hint');
                            
                            const selectedOption = courseSelect.options[courseSelect.selectedIndex];
                            const courseLanguage = selectedOption.getAttribute('data-language');
                            
                            if (!courseLanguage) {
                                teacherSelect.innerHTML = '<option value="">-- Chọn khóa học trước --</option>';
                                hint.textContent = '';
                                return;
                            }
                            
                            // Filter teachers by language
                            const allOptions = Array.from(teacherSelect.options);
                            let matchCount = 0;
                            
                            teacherSelect.innerHTML = '<option value="">-- Chọn giáo viên --</option>';
                            
                            allOptions.forEach(option => {
                                if (option.value === '') return;
                                
                                const specialization = option.getAttribute('data-specialization');
                                const languageLower = courseLanguage.toLowerCase();
                                
                                // Show teacher if specialization contains the language
                                if (specialization && specialization.includes(languageLower)) {
                                    teacherSelect.appendChild(option.cloneNode(true));
                                    matchCount++;
                                }
                            });
                            
                            // If no match, show all teachers
                            if (matchCount === 0) {
                                allOptions.forEach(option => {
                                    if (option.value !== '') {
                                        teacherSelect.appendChild(option.cloneNode(true));
                                    }
                                });
                                hint.textContent = 'Không tìm thấy giáo viên chuyên môn phù hợp. Hiển thị tất cả giáo viên.';
                                hint.className = 'text-warning';
                            } else {
                                hint.textContent = `Đã lọc ${matchCount} giáo viên phù hợp với môn ${courseLanguage}`;
                                hint.className = 'text-success';
                            }
                        }
                        </script>

                        <div class="mb-3">
                            <label for="name" class="form-label">Tên lớp học <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="Ví dụ: Lớp tiếng Anh A1 - Sáng T2-T4-T6" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Chọn ngày bắt đầu khóa học</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Phải sau ngày bắt đầu</small>
                            </div>
                        </div>

                        <script>
                        // Set min date to today for both date inputs
                        document.addEventListener('DOMContentLoaded', function() {
                            const startDateInput = document.getElementById('start_date');
                            const endDateInput = document.getElementById('end_date');
                            
                            // Set min date to today
                            const today = new Date().toISOString().split('T')[0];
                            startDateInput.min = today;
                            endDateInput.min = today;
                            
                            // Update end date min when start date changes
                            startDateInput.addEventListener('change', function() {
                                endDateInput.min = this.value;
                                if (endDateInput.value && endDateInput.value < this.value) {
                                    endDateInput.value = '';
                                }
                            });
                        });
                        </script>

                        <div class="mb-3">
                            <label for="max_capacity" class="form-label">Sức chứa tối đa <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('max_capacity') is-invalid @enderror" 
                                   id="max_capacity" name="max_capacity" value="{{ old('max_capacity', 20) }}" 
                                   min="1" required>
                            @error('max_capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Số lượng học viên tối đa có thể đăng ký lớp học này</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="shift" class="form-label">Ca học</label>
                                <select class="form-select @error('shift') is-invalid @enderror" 
                                        id="shift" name="shift">
                                    <option value="">-- Không chọn --</option>
                                    <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Sáng (7:00 - 11:00)</option>
                                    <option value="afternoon" {{ old('shift') == 'afternoon' ? 'selected' : '' }}>Chiều (13:00 - 17:00)</option>
                                    <option value="evening" {{ old('shift') == 'evening' ? 'selected' : '' }}>Tối (18:00 - 21:00)</option>
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
                                        $oldWeekdays = old('weekdays', []);
                                    @endphp
                                    @foreach($weekdays as $value => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="weekdays[]" value="{{ $value }}" 
                                                   id="weekday{{ $value }}"
                                                   {{ in_array($value, $oldWeekdays) ? 'checked' : '' }}>
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

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('classes.index') }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Tạo lớp học
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
