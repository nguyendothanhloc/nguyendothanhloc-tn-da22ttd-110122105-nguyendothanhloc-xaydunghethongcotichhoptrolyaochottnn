<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Chỉnh sửa FAQ</h2>
            <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-pencil"></i> Cập nhật thông tin FAQ</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.faq.update', $chatbotKnowledge->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Category -->
                                <div class="mb-3">
                                    <label for="category" class="form-label">
                                        Danh mục <span class="text-danger">*</span>
                                    </label>
                                    <select name="category" id="category" 
                                            class="form-select @error('category') is-invalid @enderror" 
                                            required>
                                        <option value="">-- Chọn danh mục --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" 
                                                {{ old('category', $chatbotKnowledge->category) === $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Question -->
                                <div class="mb-3">
                                    <label for="question" class="form-label">
                                        Câu hỏi <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="question" id="question" rows="3" 
                                              class="form-control @error('question') is-invalid @enderror"
                                              placeholder="Nhập câu hỏi FAQ (tối thiểu 10 ký tự)"
                                              required>{{ old('question', $chatbotKnowledge->question) }}</textarea>
                                    @error('question')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i> Câu hỏi mà học viên có thể đặt ra
                                    </small>
                                </div>

                                <!-- Answer -->
                                <div class="mb-3">
                                    <label for="answer" class="form-label">
                                        Câu trả lời <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="answer" id="answer" rows="5" 
                                              class="form-control @error('answer') is-invalid @enderror"
                                              placeholder="Nhập câu trả lời (tối thiểu 20 ký tự)"
                                              required>{{ old('answer', $chatbotKnowledge->answer) }}</textarea>
                                    @error('answer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i> Câu trả lời chi tiết mà chatbot sẽ cung cấp
                                    </small>
                                </div>

                                <!-- Keywords -->
                                <div class="mb-3">
                                    <label for="keywords" class="form-label">
                                        Từ khóa <span class="text-muted">(Tùy chọn)</span>
                                    </label>
                                    <input type="text" name="keywords" id="keywords" 
                                           class="form-control @error('keywords') is-invalid @enderror"
                                           placeholder="Nhập từ khóa, phân cách bằng dấu phẩy"
                                           value="{{ old('keywords', $chatbotKnowledge->keywords) }}">
                                    @error('keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i> Từ khóa giúp chatbot tìm câu trả lời dễ dàng hơn. Ví dụ: hoàn tiền, refund, bảo lưu
                                    </small>
                                </div>

                                <!-- Priority -->
                                <div class="mb-3">
                                    <label for="priority" class="form-label">
                                        Độ ưu tiên <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="priority" id="priority" 
                                           class="form-control @error('priority') is-invalid @enderror"
                                           min="1" max="100" 
                                           value="{{ old('priority', $chatbotKnowledge->priority) }}" 
                                           required>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i> Số từ 1-100. Số cao hơn = ưu tiên cao hơn khi có nhiều câu trả lời phù hợp
                                    </small>
                                </div>

                                <!-- Is Active -->
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="is_active" id="is_active" 
                                               class="form-check-input" value="1"
                                               {{ old('is_active', $chatbotKnowledge->is_active) == '1' || old('is_active', $chatbotKnowledge->is_active) === true ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Kích hoạt FAQ này
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i> Chỉ các FAQ được kích hoạt mới được chatbot sử dụng
                                    </small>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> Tạo lúc: {{ $chatbotKnowledge->created_at->format('d/m/Y H:i') }}<br>
                                            <i class="bi bi-clock-history"></i> Cập nhật: {{ $chatbotKnowledge->updated_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary">
                                            <i class="bi bi-x-circle"></i> Hủy
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Cập nhật FAQ
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="card mt-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-lightbulb"></i> Hướng dẫn</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li><strong>Danh mục:</strong> Phân loại FAQ để dễ quản lý</li>
                                <li><strong>Câu hỏi:</strong> Viết câu hỏi theo cách học viên thường đặt</li>
                                <li><strong>Câu trả lời:</strong> Trả lời rõ ràng, chi tiết và thân thiện</li>
                                <li><strong>Từ khóa:</strong> Thêm các từ đồng nghĩa hoặc từ viết tắt</li>
                                <li><strong>Độ ưu tiên:</strong> FAQ có độ ưu tiên cao hơn sẽ được ưu tiên hiển thị</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
