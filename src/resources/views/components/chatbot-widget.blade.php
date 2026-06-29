<!-- Chatbot Widget -->
<div id="chatbot-widget">
    <!-- Chat Button -->
    <button id="chatbot-toggle" class="chatbot-toggle">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span class="chatbot-badge" id="chatbot-badge" style="display: none;">1</span>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" class="chatbot-window" style="display: none;">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="d-flex align-items-center">
                <div class="chatbot-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="5"></circle>
                        <path d="M20 21a8 8 0 1 0-16 0"></path>
                    </svg>
                </div>
                <div>
                    <h6 class="mb-0">Trợ lý ảo</h6>
                    <small class="text-white-50">Luôn sẵn sàng hỗ trợ</small>
                </div>
            </div>
            <button id="chatbot-close" class="chatbot-close">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Messages -->
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="chatbot-message assistant">
                <div class="chatbot-message-content">
                    <p>Xin chào! Tôi là trợ lý ảo của Trung tâm Ngoại ngữ. Tôi có thể giúp bạn:</p>
                    <ul class="mb-0">
                        <li>Tìm khóa học phù hợp</li>
                        <li>Xem lịch học</li>
                        <li>Kiểm tra điểm số</li>
                        <li>Thông tin học phí</li>
                        <li>Và nhiều hơn nữa...</li>
                    </ul>
                    <p class="mb-0 mt-2">Bạn cần tôi giúp gì? 😊</p>
                </div>
            </div>
        </div>

        <!-- Input -->
        <div class="chatbot-input">
            <form id="chatbot-form">
                <input 
                    type="text" 
                    id="chatbot-input-field" 
                    placeholder="Nhập câu hỏi của bạn..." 
                    autocomplete="off"
                    maxlength="500"
                >
                <button type="submit" id="chatbot-send">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
#chatbot-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}

.chatbot-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
}

.chatbot-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.chatbot-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.chatbot-window {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 380px;
    height: 600px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.chatbot-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.chatbot-close {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.chatbot-close:hover {
    opacity: 1;
}

.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f8f9fa;
}

.chatbot-message {
    margin-bottom: 16px;
    display: flex;
}

.chatbot-message.user {
    justify-content: flex-end;
}

.chatbot-message-content {
    max-width: 80%;
    padding: 12px 16px;
    border-radius: 12px;
    line-height: 1.5;
}

.chatbot-message.assistant .chatbot-message-content {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px 12px 12px 4px;
}

.chatbot-message.user .chatbot-message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px 12px 4px 12px;
}

.chatbot-message-content p {
    margin-bottom: 8px;
}

.chatbot-message-content p:last-child {
    margin-bottom: 0;
}

.chatbot-message-content ul {
    padding-left: 20px;
    margin-bottom: 8px;
}

.chatbot-input {
    padding: 16px;
    background: white;
    border-top: 1px solid #e9ecef;
}

.chatbot-input form {
    display: flex;
    gap: 8px;
}

.chatbot-input input {
    flex: 1;
    padding: 10px 16px;
    border: 1px solid #e9ecef;
    border-radius: 24px;
    outline: none;
    font-size: 14px;
}

.chatbot-input input:focus {
    border-color: #667eea;
}

.chatbot-input button {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.chatbot-input button:hover {
    transform: scale(1.05);
}

.chatbot-input button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.chatbot-typing {
    display: flex;
    gap: 4px;
    padding: 8px 0;
}

.chatbot-typing span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #667eea;
    animation: typing 1.4s infinite;
}

.chatbot-typing span:nth-child(2) {
    animation-delay: 0.2s;
}

.chatbot-typing span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 40px);
        height: calc(100vh - 120px);
        right: 20px;
        bottom: 90px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('chatbot-toggle');
    const window = document.getElementById('chatbot-window');
    const close = document.getElementById('chatbot-close');
    const form = document.getElementById('chatbot-form');
    const input = document.getElementById('chatbot-input-field');
    const messages = document.getElementById('chatbot-messages');
    const sendBtn = document.getElementById('chatbot-send');

    // Toggle chat window
    toggle.addEventListener('click', function() {
        window.style.display = window.style.display === 'none' ? 'flex' : 'none';
        if (window.style.display === 'flex') {
            input.focus();
        }
    });

    close.addEventListener('click', function() {
        window.style.display = 'none';
    });

    // Send message
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = input.value.trim();
        if (!message) return;

        // Add user message
        addMessage(message, 'user');
        input.value = '';
        sendBtn.disabled = true;

        // Show typing indicator
        const typingDiv = addTypingIndicator();

        try {
            // Send to server
            const response = await fetch('{{ route("chat.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();

            // Remove typing indicator
            typingDiv.remove();

            if (data.success) {
                addMessage(data.response, 'assistant');
            } else {
                addMessage('Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại.', 'assistant');
            }
        } catch (error) {
            typingDiv.remove();
            addMessage('Không thể kết nối đến server. Vui lòng thử lại.', 'assistant');
        }

        sendBtn.disabled = false;
        input.focus();
    });

    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${sender}`;
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'chatbot-message-content';
        
        // Convert line breaks to <br> and preserve formatting
        const formattedText = text.replace(/\n/g, '<br>');
        contentDiv.innerHTML = formattedText;
        
        messageDiv.appendChild(contentDiv);
        messages.appendChild(messageDiv);
        
        // Scroll to bottom
        messages.scrollTop = messages.scrollHeight;
    }

    function addTypingIndicator() {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chatbot-message assistant';
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'chatbot-message-content';
        
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chatbot-typing';
        typingDiv.innerHTML = '<span></span><span></span><span></span>';
        
        contentDiv.appendChild(typingDiv);
        messageDiv.appendChild(contentDiv);
        messages.appendChild(messageDiv);
        
        messages.scrollTop = messages.scrollHeight;
        
        return messageDiv;
    }
});
</script>
