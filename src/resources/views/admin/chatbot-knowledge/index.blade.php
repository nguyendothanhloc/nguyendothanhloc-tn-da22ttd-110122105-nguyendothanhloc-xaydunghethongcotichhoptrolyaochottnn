<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Quản lý FAQ Chatbot</h2>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Quay lại Dashboard
                </a>
                <a href="{{ route('admin.faq.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tạo FAQ mới
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.faq.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="category" class="form-label">Danh mục</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="is_active" class="form-label">Trạng thái</label>
                            <select name="is_active" id="is_active" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Đã vô hiệu hóa</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Tìm trong câu hỏi, câu trả lời, từ khóa..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-funnel"></i> Lọc
                            </button>
                            <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Xóa bộ lọc
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- FAQs Table -->
            <div class="card">
                <div class="card-body">
                    @if($faqs->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Không có câu hỏi FAQ nào.
                            <a href="{{ route('admin.faq.create') }}" class="alert-link">Tạo FAQ đầu tiên</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 15%">Danh mục</th>
                                        <th style="width: 25%">Câu hỏi</th>
                                        <th style="width: 30%">Câu trả lời</th>
                                        <th style="width: 8%" class="text-center">Độ ưu tiên</th>
                                        <th style="width: 10%" class="text-center">Trạng thái</th>
                                        <th style="width: 12%" class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($faqs as $faq)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $faq->category }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ Str::limit($faq->question, 80) }}</strong>
                                                @if($faq->keywords)
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="bi bi-tag"></i> {{ Str::limit($faq->keywords, 50) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($faq->answer, 120) }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $faq->priority }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input status-toggle" 
                                                           type="checkbox" 
                                                           role="switch"
                                                           data-faq-id="{{ $faq->id }}"
                                                           {{ $faq->is_active ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.faq.edit', $faq->id) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Chỉnh sửa">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.faq.destroy', $faq->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa FAQ này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $faqs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="statusToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusToggles = document.querySelectorAll('.status-toggle');
            
            statusToggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const faqId = this.dataset.faqId;
                    const isChecked = this.checked;
                    
                    fetch(`/admin/chatbot-knowledge/${faqId}/toggle-status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');
                        } else {
                            showToast(data.message, 'error');
                            // Revert the toggle if failed
                            this.checked = !isChecked;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Có lỗi xảy ra khi thay đổi trạng thái', 'error');
                        // Revert the toggle if failed
                        this.checked = !isChecked;
                    });
                });
            });
        });

        function showToast(message, type) {
            const toastEl = document.getElementById('statusToast');
            const toastMessage = document.getElementById('toastMessage');
            const toast = new bootstrap.Toast(toastEl);
            
            // Set message
            toastMessage.textContent = message;
            
            // Set color based on type
            if (type === 'success') {
                toastEl.classList.remove('bg-danger', 'text-white');
                toastEl.classList.add('bg-success', 'text-white');
            } else {
                toastEl.classList.remove('bg-success', 'text-white');
                toastEl.classList.add('bg-danger', 'text-white');
            }
            
            toast.show();
        }
    </script>
    @endpush
</x-app-layout>
