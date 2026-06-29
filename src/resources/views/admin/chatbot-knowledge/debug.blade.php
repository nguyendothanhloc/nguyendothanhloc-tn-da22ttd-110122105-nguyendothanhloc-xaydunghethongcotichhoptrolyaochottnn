<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">🔍 FAQ Form Debug Page</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <!-- Debug Info Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Debug Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>CSRF Token Present:</th>
                                    <td>
                                        <span id="csrf-check">Checking...</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Current User:</th>
                                    <td>{{ Auth::user()->name ?? 'Not logged in' }}</td>
                                </tr>
                                <tr>
                                    <th>User Role:</th>
                                    <td>{{ Auth::user()->role ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>User ID:</th>
                                    <td>{{ Auth::id() ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Session Status:</th>
                                    <td>{{ session()->isStarted() ? '✅ Active' : '❌ Not started' }}</td>
                                </tr>
                                <tr>
                                    <th>Create Route:</th>
                                    <td>{{ route('admin.faq.create') }}</td>
                                </tr>
                                <tr>
                                    <th>Store Route:</th>
                                    <td>{{ route('admin.faq.store') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Test Form Card -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Test FAQ Create Form</h5>
                        </div>
                        <div class="card-body">
                            <!-- Form Submission Result -->
                            <div id="form-result" class="alert d-none" role="alert"></div>

                            <!-- Test Form -->
                            <form id="test-form" action="{{ route('admin.faq.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="category" class="form-label">Danh mục *</label>
                                    <select name="category" id="category" class="form-select" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        <option value="Chính sách hoàn tiền">Chính sách hoàn tiền</option>
                                        <option value="Quy định chuyển lớp">Quy định chuyển lớp</option>
                                        <option value="Thủ tục nghỉ học / bảo lưu">Thủ tục nghỉ học / bảo lưu</option>
                                        <option value="Điều kiện nhận ưu đãi / giảm giá">Điều kiện nhận ưu đãi / giảm giá</option>
                                        <option value="Khác">Khác</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="question" class="form-label">Câu hỏi *</label>
                                    <textarea name="question" id="question" rows="3" class="form-control" required 
                                              placeholder="Nhập câu hỏi (tối thiểu 10 ký tự)">Test FAQ từ debug page - Có dấu tiếng Việt</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="answer" class="form-label">Câu trả lời *</label>
                                    <textarea name="answer" id="answer" rows="4" class="form-control" required
                                              placeholder="Nhập câu trả lời (tối thiểu 20 ký tự)">Đây là câu trả lời test từ debug page với nhiều ký tự tiếng Việt có dấu.</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="keywords" class="form-label">Từ khóa</label>
                                    <input type="text" name="keywords" id="keywords" class="form-control" 
                                           value="test,debug,kiem tra">
                                </div>

                                <div class="mb-3">
                                    <label for="priority" class="form-label">Độ ưu tiên *</label>
                                    <input type="number" name="priority" id="priority" class="form-control" 
                                           min="1" max="100" value="50" required>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="is_active" id="is_active" 
                                               class="form-check-input" checked>
                                        <label class="form-check-label" for="is_active">
                                            Kích hoạt FAQ này
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="bi bi-check-circle"></i> Tạo FAQ Test
                                </button>
                                
                                <button type="button" class="btn btn-secondary" id="ajax-submit-btn">
                                    <i class="bi bi-cloud-upload"></i> Test via AJAX
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Console Log Card -->
                    <div class="card mt-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">JavaScript Console Log</h5>
                        </div>
                        <div class="card-body">
                            <pre id="console-log" style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px;">Logs will appear here...</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Custom console.log that also displays on page
        const consoleLog = document.getElementById('console-log');
        const originalLog = console.log;
        const originalError = console.error;
        
        function logToPage(message, type = 'log') {
            const timestamp = new Date().toLocaleTimeString();
            const prefix = type === 'error' ? '❌ ERROR' : '✅ LOG';
            const color = type === 'error' ? 'color: red;' : '';
            consoleLog.innerHTML += `[${timestamp}] ${prefix}: ${message}\n`;
            consoleLog.scrollTop = consoleLog.scrollHeight;
        }
        
        console.log = function(...args) {
            originalLog.apply(console, args);
            logToPage(args.join(' '));
        };
        
        console.error = function(...args) {
            originalError.apply(console, args);
            logToPage(args.join(' '), 'error');
        };

        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ Page loaded successfully');
            
            // Check CSRF token
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfCheck = document.getElementById('csrf-check');
            
            if (csrfMeta && csrfMeta.content) {
                csrfCheck.innerHTML = `✅ Yes (${csrfMeta.content.substring(0, 20)}...)`;
                console.log('✅ CSRF token found: ' + csrfMeta.content.substring(0, 30) + '...');
            } else {
                csrfCheck.innerHTML = '❌ Missing!';
                console.error('❌ CSRF token meta tag not found!');
            }
            
            // Form submit handler
            const form = document.getElementById('test-form');
            const submitBtn = document.getElementById('submit-btn');
            const formResult = document.getElementById('form-result');
            
            form.addEventListener('submit', function(e) {
                console.log('📤 Form submission started (normal submit)');
                console.log('Form action: ' + form.action);
                console.log('Form method: ' + form.method);
                
                // Log all form data
                const formData = new FormData(form);
                console.log('Form data:');
                for (let [key, value] of formData.entries()) {
                    console.log(`  ${key}: ${value}`);
                }
                
                // Don't prevent default - let it submit normally
                // This allows us to see if there are any server-side errors
            });
            
            // AJAX submit test
            const ajaxBtn = document.getElementById('ajax-submit-btn');
            ajaxBtn.addEventListener('click', function() {
                console.log('📤 AJAX submission started');
                
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                
                // Convert FormData to JSON
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                
                console.log('AJAX Data:', JSON.stringify(data));
                console.log('CSRF Token:', csrfToken);
                
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    console.log('📥 Response received, status: ' + response.status);
                    return response.json().catch(() => response.text());
                })
                .then(data => {
                    console.log('📥 Response data:', data);
                    
                    formResult.classList.remove('d-none', 'alert-danger', 'alert-success');
                    
                    if (typeof data === 'object' && data.errors) {
                        formResult.classList.add('alert-danger');
                        formResult.innerHTML = '<strong>❌ Validation Errors:</strong><br>' + 
                            Object.values(data.errors).flat().join('<br>');
                        console.error('Validation errors:', data.errors);
                    } else if (typeof data === 'string' && data.includes('redirect')) {
                        formResult.classList.add('alert-success');
                        formResult.innerHTML = '✅ Success! FAQ created (redirecting...)';
                        console.log('✅ Success response received');
                    } else {
                        formResult.classList.add('alert-success');
                        formResult.innerHTML = '✅ Response received: ' + JSON.stringify(data).substring(0, 100);
                    }
                })
                .catch(error => {
                    console.error('❌ AJAX Error:', error);
                    formResult.classList.remove('d-none', 'alert-success');
                    formResult.classList.add('alert-danger');
                    formResult.innerHTML = '❌ Error: ' + error.message;
                });
            });
            
            // Capture any window errors
            window.addEventListener('error', function(e) {
                console.error('❌ JavaScript Error: ' + e.message + ' at ' + e.filename + ':' + e.lineno);
            });
            
            console.log('✅ All event listeners attached');
        });
    </script>
    @endpush
</x-app-layout>
