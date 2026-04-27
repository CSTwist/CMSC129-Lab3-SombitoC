<div id="ai-chat-container" class="position-fixed bottom-0 end-0 m-4" style="z-index: 1060;">
    {{-- Chat Toggle Button --}}
    <button id="chat-toggle" class="btn shadow-lg d-flex align-items-center justify-content-center" style="
        width: 60px; height: 60px; border-radius: 50%;
        background-color: var(--navy-text); color: white;
        border: none; transition: transform 0.3s ease;
    ">
        <i class="bi bi-chat-dots-fill" style="font-size: 1.5rem;"></i>
    </button>

    {{-- Chat Box Window --}}
    <div id="chat-window" class="card shadow-lg d-none" style="
        width: 350px; height: 450px; border-radius: 20px;
        position: absolute; bottom: 80px; right: 0;
        overflow: hidden; border: 1px solid rgba(0,0,0,0.1);
        display: flex; flex-direction: column;
    ">
        {{-- Chat Header --}}
        <div class="card-header d-flex justify-content-between align-items-center" style="
            background-color: var(--navy-text); color: white; padding: 15px 20px;
        ">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-robot"></i>
                <span class="fw-bold">Journal Assistant</span>
            </div>
            <button id="close-chat" class="btn btn-sm text-white p-0 border-0 shadow-none">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Chat Messages Area --}}
        <div id="chat-messages" class="card-body p-3 overflow-y-auto bg-light" style="flex-grow: 1;">
            <div class="ai-message mb-3">
                <div class="p-2 rounded-3" style="background-color: #E2E8F0; color: #1E293B; max-width: 85%; font-size: 0.9rem;">
                    Hello! I'm your AI Journal Assistant. How can I help you today?
                </div>
            </div>
        </div>

        {{-- Chat Input Area --}}
        <div class="card-footer bg-white border-top p-2">
            <form id="chat-form" class="d-flex gap-2">
                @csrf
                <input type="text" id="chat-input" class="form-control form-control-sm border-0 bg-light shadow-none" placeholder="Type a message..." style="border-radius: 15px; padding: 10px 15px;">
                <button type="submit" class="btn btn-sm btn-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: var(--navy-text); border: none;">
                    <i class="bi bi-send-fill" style="font-size: 0.9rem;"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chat-toggle');
    const chatWindow = document.getElementById('chat-window');
    const closeChat = document.getElementById('close-chat');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');

    // Toggle chat window
    chatToggle.addEventListener('click', () => {
        chatWindow.classList.toggle('d-none');
        if (!chatWindow.classList.contains('d-none')) {
            chatInput.focus();
        }
    });

    closeChat.addEventListener('click', () => {
        chatWindow.classList.add('d-none');
    });

    // Handle form submission
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (!message) return;

        // Clear input and add user message to UI
        chatInput.value = '';
        appendMessage('user', message);

        // Add loading state
        const loadingId = 'loading-' + Date.now();
        appendMessage('ai', 'Thinking...', loadingId);

        try {
            const response = await fetch('{{ route('chat/send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();
            
            // Remove loading message and add AI response
            const loadingMsg = document.getElementById(loadingId);
            if (loadingMsg) loadingMsg.closest('.ai-message').remove();

            if (data.error) {
                appendMessage('ai', 'Error: ' + data.error);
            } else {
                appendMessage('ai', data.response);
            }

        } catch (error) {
            console.error('Chat error:', error);
            const loadingMsg = document.getElementById(loadingId);
            if (loadingMsg) loadingMsg.closest('.ai-message').remove();
            appendMessage('ai', 'Sorry, something went wrong. Please try again later.');
        }
    });

    function appendMessage(role, text, id = null) {
        const div = document.createElement('div');
        div.className = `${role}-message mb-3 d-flex ${role === 'user' ? 'justify-content-end' : ''}`;
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'p-2 rounded-3';
        if (id) contentDiv.id = id;
        
        if (role === 'user') {
            contentDiv.style.backgroundColor = 'var(--navy-text)';
            contentDiv.style.color = 'white';
        } else {
            contentDiv.style.backgroundColor = '#E2E8F0';
            contentDiv.style.color = '#1E293B';
        }
        
        contentDiv.style.maxWidth = '85%';
        contentDiv.style.fontSize = '0.9rem';
        contentDiv.textContent = text;
        
        div.appendChild(contentDiv);
        chatMessages.appendChild(div);
        
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
</script>
