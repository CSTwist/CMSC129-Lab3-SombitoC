<div id="ai-chat-container" class="position-fixed bottom-0 end-0 m-4" style="z-index: 1060;">
    {{-- Chat Toggle Button --}}
    <button id="chat-toggle" class="btn shadow-lg d-flex align-items-center justify-content-center" style="
        width: 60px; height: 60px; border-radius: 50%;
        background-color: var(--navy-text); color: white;
        border: none; transition: transform 0.3s ease, box-shadow 0.3s ease;
    " title="Open AI Journal Assistant">
        <i class="bi bi-robot" style="font-size: 1.6rem;"></i>
    </button>

    {{-- Chat Box Window --}}
    <div id="chat-window" class="card shadow-lg d-none" style="
        width: 380px; height: 500px; border-radius: 20px;
        position: absolute; bottom: 80px; right: 0;
        overflow: hidden; border: 1px solid rgba(0,0,0,0.1);
        display: flex; flex-direction: column;
    ">
        {{-- Chat Header --}}
        <div class="card-header d-flex justify-content-between align-items-center" style="
            background-color: var(--navy-text); color: white; padding: 14px 18px;
        ">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-white text-dark" style="width: 32px; height: 32px;">
                    <i class="bi bi-stars text-primary" style="font-size: 1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size: 0.95rem; line-height: 1.1;">Journal Assistant</div>
                    <small class="text-white-50" style="font-size: 0.75rem;">AI Reflection Companion</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button id="clear-chat" class="btn btn-sm text-white-50 p-1 border-0 shadow-none hover-white" title="Clear chat history">
                    <i class="bi bi-trash3" style="font-size: 0.9rem;"></i>
                </button>
                <button id="close-chat" class="btn btn-sm text-white p-1 border-0 shadow-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        {{-- Suggested Quick Action Chips --}}
        <div id="chat-quick-actions" class="p-2 border-bottom bg-white d-flex gap-1 overflow-x-auto" style="white-space: nowrap; scrollbar-width: none;">
            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 rounded-pill quick-chip" data-prompt="Show my journal stats and mood summary" style="font-size: 0.75rem;">
                📊 Mood & Stats
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 rounded-pill quick-chip" data-prompt="Give me a thoughtful gratitude reflection prompt" style="font-size: 0.75rem;">
                🙏 Gratitude Prompt
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 rounded-pill quick-chip" data-prompt="Suggest a scripture-inspired devotional reflection prompt" style="font-size: 0.75rem;">
                📖 Scripture Reflection
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 rounded-pill quick-chip" data-prompt="What were my most recent favorite entries?" style="font-size: 0.75rem;">
                ⭐ Favorites
            </button>
        </div>

        {{-- Chat Messages Area --}}
        <div id="chat-messages" class="card-body p-3 overflow-y-auto bg-light" style="flex-grow: 1;">
            <div class="ai-message mb-3 d-flex">
                <div class="p-3 rounded-4 shadow-sm" style="background-color: #ffffff; color: #1E293B; max-width: 88%; font-size: 0.88rem; border-left: 3px solid var(--navy-text);">
                    <div class="fw-semibold mb-1 text-primary"><i class="bi bi-chat-quote-fill me-1"></i>Welcome!</div>
                    Hello! I'm your AI Journal Assistant. Ask me to find entries, analyze your moods, generate reflection prompts, or help write today's entry!
                </div>
            </div>
        </div>

        {{-- Chat Input Area --}}
        <div class="card-footer bg-white border-top p-2">
            <form id="chat-form" class="d-flex gap-2 align-items-center">
                @csrf
                <input type="text" id="chat-input" class="form-control form-control-sm border-0 bg-light shadow-none" placeholder="Ask anything about your journals..." style="border-radius: 15px; padding: 10px 14px; font-size: 0.88rem;">
                <button type="submit" class="btn btn-sm btn-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background-color: var(--navy-text); border: none;">
                    <i class="bi bi-send-fill" style="font-size: 0.95rem;"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.quick-chip:hover {
    background-color: var(--navy-text) !important;
    color: white !important;
    border-color: var(--navy-text) !important;
}
.hover-white:hover {
    color: white !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chat-toggle');
    const chatWindow = document.getElementById('chat-window');
    const closeChat = document.getElementById('close-chat');
    const clearChat = document.getElementById('clear-chat');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const quickChips = document.querySelectorAll('.quick-chip');

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

    // Clear chat history
    if (clearChat) {
        clearChat.addEventListener('click', async () => {
            if (!confirm('Clear AI conversation history?')) return;
            try {
                await fetch('{{ route('chat/clear') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                chatMessages.innerHTML = `
                    <div class="ai-message mb-3 d-flex">
                        <div class="p-3 rounded-4 shadow-sm" style="background-color: #ffffff; color: #1E293B; max-width: 88%; font-size: 0.88rem; border-left: 3px solid var(--navy-text);">
                            Conversation history cleared. How can I assist you today?
                        </div>
                    </div>
                `;
            } catch (err) {
                console.error('Failed to clear chat', err);
            }
        });
    }

    // Quick Action Chips
    quickChips.forEach(chip => {
        chip.addEventListener('click', () => {
            const prompt = chip.getAttribute('data-prompt');
            if (prompt) {
                chatInput.value = prompt;
                chatForm.dispatchEvent(new Event('submit'));
            }
        });
    });

    // Handle form submission
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (!message) return;

        chatInput.value = '';
        appendMessage('user', message);

        const loadingId = 'loading-' + Date.now();
        appendMessage('ai', '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Thinking...', loadingId, true);

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
            
            const loadingMsg = document.getElementById(loadingId);
            if (loadingMsg) loadingMsg.closest('.ai-message').remove();

            if (data.error) {
                appendMessage('ai', '⚠️ ' + data.error);
            } else {
                appendMessage('ai', data.response);
                
                if (data.refresh && typeof fetchFilteredJournals === 'function') {
                    fetchFilteredJournals();
                }
            }

        } catch (error) {
            console.error('Chat error:', error);
            const loadingMsg = document.getElementById(loadingId);
            if (loadingMsg) loadingMsg.closest('.ai-message').remove();
            appendMessage('ai', 'Sorry, I ran into an error connecting to the AI service. Please try again.');
        }
    });

    function appendMessage(role, text, id = null, isRawHtml = false) {
        const div = document.createElement('div');
        div.className = `${role}-message mb-3 d-flex ${role === 'user' ? 'justify-content-end' : 'justify-content-start'}`;
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'p-3 rounded-4 shadow-sm';
        if (id) contentDiv.id = id;
        
        if (role === 'user') {
            contentDiv.style.backgroundColor = 'var(--navy-text)';
            contentDiv.style.color = 'white';
            contentDiv.style.maxWidth = '85%';
            contentDiv.style.fontSize = '0.88rem';
            contentDiv.textContent = text;
        } else {
            contentDiv.style.backgroundColor = '#ffffff';
            contentDiv.style.color = '#1E293B';
            contentDiv.style.maxWidth = '88%';
            contentDiv.style.fontSize = '0.88rem';
            contentDiv.style.border = '1px solid rgba(0,0,0,0.06)';

            if (isRawHtml) {
                contentDiv.innerHTML = text;
            } else if (typeof marked !== 'undefined') {
                contentDiv.innerHTML = marked.parse(text);
                contentDiv.querySelectorAll('a').forEach(a => {
                    a.target = '_blank';
                    a.rel = 'noopener noreferrer';
                });
                contentDiv.querySelectorAll('p').forEach(p => p.style.marginBottom = '0.4rem');
                const lastP = contentDiv.querySelector('p:last-child');
                if (lastP) lastP.style.marginBottom = '0';
            } else {
                contentDiv.textContent = text;
            }
        }
        
        div.appendChild(contentDiv);
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
</script>

