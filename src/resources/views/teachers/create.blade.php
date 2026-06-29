<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tạo tài khoản giáo viên mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('teachers.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                    </div>

                    <form method="POST" action="{{ route('teachers.store') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Họ và tên <span class="text-danger">*</span>
                            </label>
                            <input id="name" class="form-control @error('name') is-invalid @enderror" 
                                   type="text" name="name" value="{{ old('name') }}" 
                                   required autofocus autocomplete="name" 
                                   placeholder="Ví dụ: Nguyễn Văn A">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input id="email" class="form-control @error('email') is-invalid @enderror" 
                                   type="email" name="email" value="{{ old('email') }}" 
                                   required autocomplete="username"
                                   placeholder="example@email.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                Số điện thoại
                            </label>
                            <input id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   type="text" name="phone" value="{{ old('phone') }}" 
                                   autocomplete="tel"
                                   placeholder="0901234567">
                            <small class="text-muted">Tùy chọn</small>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Specialization -->
                        <div class="mb-3">
                            <label for="specialization" class="form-label">
                                Chuyên môn
                            </label>
                            <input id="specialization" class="form-control @error('specialization') is-invalid @enderror" 
                                   type="text" name="specialization" value="{{ old('specialization') }}" 
                                   placeholder="Ví dụ: Tiếng Anh giao tiếp, Tiếng Nhật, Tiếng Hàn">
                            <small class="text-muted">Ngôn ngữ hoặc môn học chuyên trách. Quan trọng để phân công lớp học!</small>
                            @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Qualifications -->
                        <div class="mb-3">
                            <label for="qualifications" class="form-label">
                                Bằng cấp & Chứng chỉ
                            </label>
                            <textarea id="qualifications" name="qualifications" rows="3" 
                                      class="form-control @error('qualifications') is-invalid @enderror" 
                                      placeholder="Ví dụ: Thạc sĩ Ngôn ngữ Anh, Chứng chỉ TESOL, IELTS 8.0">{{ old('qualifications') }}</textarea>
                            <small class="text-muted">Tùy chọn - Liệt kê bằng cấp và chứng chỉ</small>
                            @error('qualifications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bio -->
                        <div class="mb-3">
                            <label for="bio" class="form-label">
                                Giới thiệu
                            </label>
                            <textarea id="bio" name="bio" rows="3" 
                                      class="form-control @error('bio') is-invalid @enderror" 
                                      placeholder="Giới thiệu ngắn về kinh nghiệm giảng dạy">{{ old('bio') }}</textarea>
                            <small class="text-muted">Tùy chọn - Kinh nghiệm và phương pháp giảng dạy</small>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Mật khẩu <span class="text-danger">*</span>
                            </label>
                            <input id="password" class="form-control @error('password') is-invalid @enderror"
                                   type="password" name="password" required autocomplete="new-password"
                                   placeholder="Tối thiểu 8 ký tự">
                            <small class="text-muted">Mật khẩu phải có ít nhất 8 ký tự</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                Xác nhận mật khẩu <span class="text-danger">*</span>
                            </label>
                            <input id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                                   type="password" name="password_confirmation" required autocomplete="new-password"
                                   placeholder="Nhập lại mật khẩu">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus me-1"></i>Tạo tài khoản giáo viên
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
