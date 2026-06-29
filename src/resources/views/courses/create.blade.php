<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Tạo khóa học mới</h2>
            <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin khóa học</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('courses.store') }}">
                                @csrf

                                <!-- Course Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Tên khóa học <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Ví dụ: English for Beginners"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4"
                                              placeholder="Mô tả chi tiết về khóa học...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Language -->
                                <div class="mb-3">
                                    <label for="language" class="form-label">
                                        Ngôn ngữ <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('language') is-invalid @enderror" 
                                            id="language" 
                                            name="language" 
                                            required>
                                        <option value="">-- Chọn ngôn ngữ --</option>
                                        <option value="English" {{ old('language') === 'English' ? 'selected' : '' }}>English</option>
                                        <option value="Japanese" {{ old('language') === 'Japanese' ? 'selected' : '' }}>Japanese</option>
                                        <option value="Korean" {{ old('language') === 'Korean' ? 'selected' : '' }}>Korean</option>
                                        <option value="Chinese" {{ old('language') === 'Chinese' ? 'selected' : '' }}>Chinese</option>
                                        <option value="French" {{ old('language') === 'French' ? 'selected' : '' }}>French</option>
                                        <option value="German" {{ old('language') === 'German' ? 'selected' : '' }}>German</option>
                                        <option value="Spanish" {{ old('language') === 'Spanish' ? 'selected' : '' }}>Spanish</option>
                                    </select>
                                    @error('language')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Level -->
                                <div class="mb-3">
                                    <label for="level" class="form-label">
                                        Trình độ <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('level') is-invalid @enderror" 
                                            id="level" 
                                            name="level" 
                                            required>
                                        <option value="">-- Chọn trình độ --</option>
                                        <option value="beginner" {{ old('level') === 'beginner' ? 'selected' : '' }}>Beginner (Sơ cấp)</option>
                                        <option value="elementary" {{ old('level') === 'elementary' ? 'selected' : '' }}>Elementary (Cơ bản)</option>
                                        <option value="intermediate" {{ old('level') === 'intermediate' ? 'selected' : '' }}>Intermediate (Trung cấp)</option>
                                        <option value="advanced" {{ old('level') === 'advanced' ? 'selected' : '' }}>Advanced (Nâng cao)</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Duration -->
                                <div class="mb-3">
                                    <label for="duration_weeks" class="form-label">
                                        Thời lượng (tuần) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('duration_weeks') is-invalid @enderror" 
                                           id="duration_weeks" 
                                           name="duration_weeks" 
                                           value="{{ old('duration_weeks') }}" 
                                           min="1"
                                           placeholder="Ví dụ: 12"
                                           required>
                                    @error('duration_weeks')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">Số tuần học của khóa học</div>
                                </div>

                                <!-- Price -->
                                <div class="mb-3">
                                    <label for="price" class="form-label">
                                        Giá (VNĐ) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" 
                                           name="price" 
                                           value="{{ old('price') }}" 
                                           min="0"
                                           step="1000"
                                           placeholder="Ví dụ: 5000000"
                                           required>
                                    @error('price')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">Học phí của khóa học</div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                        Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Tạo khóa học
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
