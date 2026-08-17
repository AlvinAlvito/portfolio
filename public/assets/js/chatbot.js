document.addEventListener('DOMContentLoaded', () => {
    const panel = document.getElementById('chatPanel');
    const launcher = document.getElementById('chatLauncher');
    const closeButton = document.getElementById('chatClose');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const sendButton = document.getElementById('chatSend');
    const messages = document.getElementById('chatMessages');
    const suggestions = document.getElementById('chatSuggestions');

    if (!panel || !launcher || !form || !input || !messages) return;

    const endpoint = panel.dataset.endpoint;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const history = [];
    let waiting = false;

    const setOpen = (open) => {
        panel.classList.toggle('open', open);
        document.body.classList.toggle('chat-open', open);
        panel.setAttribute('aria-hidden', String(!open));
        launcher.setAttribute('aria-expanded', String(open));
        if (open) window.setTimeout(() => input.focus(), 180);
    };

    launcher.addEventListener('click', () => setOpen(!panel.classList.contains('open')));
    closeButton?.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', event => { if (event.key === 'Escape') setOpen(false); });

    const scrollToLatest = () => { messages.scrollTop = messages.scrollHeight; };

    const addMessage = (text, role, options = {}) => {
        const row = document.createElement('div');
        row.className = `chat-row ${role}`;

        if (role === 'bot') {
            const avatar = document.createElement('span');
            avatar.className = 'chat-mini-avatar';
            avatar.innerHTML = '<i data-lucide="sparkles" size="14"></i>';
            row.appendChild(avatar);
        }

        const bubble = document.createElement('div');
        bubble.className = `chat-message${options.error ? ' error' : ''}${options.crisis ? ' crisis' : ''}`;
        bubble.textContent = text;
        row.appendChild(bubble);
        messages.appendChild(row);
        if (window.lucide) lucide.createIcons();
        scrollToLatest();
        return row;
    };

    const addTyping = () => {
        const row = document.createElement('div');
        row.className = 'chat-row bot chat-typing-row';
        row.innerHTML = '<span class="chat-mini-avatar"><i data-lucide="sparkles" size="14"></i></span><div class="chat-message chat-typing"><i></i><i></i><i></i></div>';
        messages.appendChild(row);
        if (window.lucide) lucide.createIcons();
        scrollToLatest();
        return row;
    };

    const setWaiting = (value) => {
        waiting = value;
        input.disabled = value;
        sendButton.disabled = value;
        panel.classList.toggle('is-waiting', value);
    };

    const sendMessage = async (rawMessage) => {
        const message = rawMessage.trim();
        if (!message || waiting) return;

        suggestions?.remove();
        addMessage(message, 'user');
        input.value = '';
        input.style.height = 'auto';
        const typing = addTyping();
        setWaiting(true);

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({message, history: history.slice(-10)}),
            });
            const data = await response.json().catch(() => ({}));
            typing.remove();

            if (!response.ok || !data.reply) {
                throw new Error(data.message || 'Chatbot belum dapat menjawab. Silakan coba lagi.');
            }

            addMessage(data.reply, 'bot', {crisis: Boolean(data.crisis)});
            history.push({role: 'user', text: message}, {role: 'model', text: data.reply});
            if (history.length > 10) history.splice(0, history.length - 10);
        } catch (error) {
            typing.remove();
            addMessage(error.message || 'Terjadi gangguan koneksi. Silakan coba kembali.', 'bot', {error:true});
        } finally {
            setWaiting(false);
            input.focus();
        }
    };

    form.addEventListener('submit', event => { event.preventDefault(); sendMessage(input.value); });
    input.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); form.requestSubmit(); }
    });
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = `${Math.min(input.scrollHeight, 105)}px`;
    });
    document.querySelectorAll('[data-chat-prompt]').forEach(button => button.addEventListener('click', () => sendMessage(button.dataset.chatPrompt)));
});
