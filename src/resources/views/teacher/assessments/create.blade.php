<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Tạo bài kiểm tra mới</h2>
            <a href="{{ route('teacher.assessments.index', $class->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Thông tin bài kiểm tra</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('teacher.assessments.store', $class->id) }}" method="POST">
                                @csrf

                                <!-- Assessment Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên bài kiểm tra <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Ví dụ: Kiểm tra giữa kỳ 1"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Assessment Type -->
                                <div class="mb-3">
                                    <label for="type" class="form-label">Loại bài kiểm tra <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" 
                                            name="type" 
                                            required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="quiz" {{ old('type') === 'quiz' ? 'selected' : '' }}>Kiểm tra</option>
                                        <option value="midterm" {{ old('type') === 'midterm' ? 'selected' : '' }}>Giữa kỳ</option>
                                        <option value="final" {{ old('type') === 'final' ? 'selected' : '' }}>Cuối kỳ</option>
                                        <option value="assignment" {{ old('type') === 'assignment' ? 'selected' : '' }}>Bài tập</option>
                                        <option value="project" {{ old('type') === 'project' ? 'selected' : '' }}>Dự án</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Max Score -->
                                <div class="mb-3">
                                    <label for="max_score" class="form-label">Điểm tối đa <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('max_score') is-invalid @enderror" 
                                           id="max_score" 
                                           name="max_score" 
                                           value="{{ old('max_score', 10) }}" 
                                           min="0" 
                                           max="100" 
                                           step="0.5"
                                           required>
                                    @error('max_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Điểm tối đa từ 0 đến 100</small>
                                </div>

                                <!-- Assessment Date -->
                                <div class="mb-3">
                                    <label for="assessment_date" class="form-label">Ngày kiểm tra <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('assessment_date') is-invalid @enderror" 
                                           id="assessment_date" 
                                           name="assessment_date" 
                                           value="{{ old('assessment_date') }}" 
                                           required>
                                    @error('assessment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              placeholder="Mô tả nội dung bài kiểm tra (tùy chọn)">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('teacher.assessments.index', $class->id) }}" class="btn btn-secondary">
                                        Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Tạo bài kiểm tra
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
