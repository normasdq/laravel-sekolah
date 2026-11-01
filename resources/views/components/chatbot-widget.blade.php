@php
    $rawPhone = preg_replace('/[^0-9]/', '', $settings->get('school_phone', '6281234567890'));
    if (str_starts_with($rawPhone, '0')) {
        $rawPhone = '62' . substr($rawPhone, 1);
    }
    $waUrl = "https://wa.me/{$rawPhone}?text=" . rawurlencode('Halo Admin SMP Tunas Harapan Bekasi, saya ingin bertanya mengenai pendaftaran SPMB.');
    $isStudentAuth = auth()->check() && auth()->user()->role === 'user';
    $studentName = $isStudentAuth ? auth()->user()->name : null;
@endphp

<style>
    .tunas-chatbot-wrapper {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 45;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Floating Trigger Button */
    .tunas-chatbot-trigger {
        display: inline-flex;
        align-items: center;
        gap: 11px;
        padding: 11px 18px;
        border-radius: 9999px;
        background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
        color: #ffffff;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700;
        font-size: 13.5px;
        border: 1px solid rgba(255, 255, 255, 0.22);
        box-shadow: 0 14px 34px -6px rgba(37, 99, 235, 0.55), 0 0 0 1px rgba(15, 23, 42, 0.2);
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .tunas-chatbot-trigger:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 18px 40px -6px rgba(14, 165, 233, 0.65);
    }

    .tunas-chatbot-trigger:active {
        transform: translateY(0) scale(0.98);
    }

    .tunas-chatbot-trigger-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        position: relative;
    }

    .tunas-chatbot-online-dot {
        position: absolute;
        top: -1px;
        right: -1px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #22c55e;
        border: 2px solid #1d4ed8;
    }

    /* Chat Window */
    .tunas-chatbot-window {
        position: absolute;
        bottom: 66px;
        right: 0;
        width: 400px;
        max-width: calc(100vw - 32px);
        height: 480px;
        max-height: calc(100vh - 175px);
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 20px;
        box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.75);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
        transform: translateY(16px) scale(0.96);
        transform-origin: bottom right;
        transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .tunas-chatbot-window.is-open {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0) scale(1);
    }

    /* Header */
    .tunas-chatbot-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .tunas-chatbot-avatar {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 15px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
    }

    .tunas-chatbot-wa-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 10px;
        border-radius: 9px;
        background: rgba(37, 211, 102, 0.16);
        border: 1px solid rgba(37, 211, 102, 0.4);
        color: #4ade80 !important;
        font-size: 11.5px;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .tunas-chatbot-wa-btn:hover {
        background: #25D366;
        color: #ffffff !important;
        border-color: #25D366;
    }

    .tunas-chatbot-icon-btn {
        width: 29px;
        height: 29px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .tunas-chatbot-icon-btn:hover {
        background: rgba(255, 255, 255, 0.14);
        color: #ffffff;
    }

    /* Messages Container */
    .tunas-chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 11px;
        background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 55%), #090d16;
    }

    .tunas-chatbot-messages::-webkit-scrollbar {
        width: 5px;
    }

    .tunas-chatbot-messages::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.25);
        border-radius: 999px;
    }

    .tunas-msg-row {
        display: flex;
        flex-direction: column;
        max-width: 87%;
    }

    .tunas-msg-row.is-bot {
        align-self: flex-start;
    }

    .tunas-msg-row.is-user {
        align-self: flex-end;
    }

    .tunas-msg-bubble {
        padding: 10px 13px;
        border-radius: 15px;
        font-size: 12.8px;
        line-height: 1.55;
        word-wrap: break-word;
    }

    .tunas-msg-row.is-bot .tunas-msg-bubble {
        background: #1e293b;
        color: #e2e8f0;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-bottom-left-radius: 4px;
    }

    .tunas-msg-row.is-user .tunas-msg-bubble {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
    }

    .tunas-msg-bubble ul {
        margin: 6px 0 4px 0;
        padding-left: 18px;
        list-style-type: disc;
    }

    .tunas-msg-bubble li {
        margin-bottom: 3px;
    }

    .tunas-msg-bubble code {
        background: rgba(15, 23, 42, 0.65);
        color: #38bdf8;
        padding: 1px 6px;
        border-radius: 5px;
        font-size: 12px;
    }

    /* Quick Reply Chips */
    .tunas-chatbot-chips {
        padding: 8px 12px;
        background: #0f172a;
        border-top: 1px solid rgba(255, 255, 255, 0.07);
        display: flex;
        gap: 6px;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none;
    }

    .tunas-chatbot-chips::-webkit-scrollbar {
        display: none;
    }

    .tunas-chip-btn {
        padding: 5px 11px;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.14);
        border: 1px solid rgba(59, 130, 246, 0.32);
        color: #93c5fd;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }

    .tunas-chip-btn:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    /* Input Area */
    .tunas-chatbot-footer {
        padding: 10px 12px 11px;
        background: #1e293b;
        border-top: 1px solid rgba(255, 255, 255, 0.09);
    }

    .tunas-chatbot-form {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tunas-chatbot-input {
        flex: 1;
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 11px;
        padding: 8px 12px;
        color: #f8fafc;
        font-size: 12.8px;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .tunas-chatbot-input:focus {
        border-color: #3b82f6;
    }

    .tunas-chatbot-input::placeholder {
        color: #64748b;
    }

    .tunas-chatbot-send {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
        border: none;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13.5px;
        cursor: pointer;
        flex-shrink: 0;
        transition: opacity 0.2s, transform 0.15s;
    }

    .tunas-chatbot-send:hover {
        transform: scale(1.04);
    }

    .tunas-chatbot-send:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .tunas-chatbot-subfooter {
        margin-top: 7px;
        text-align: center;
        font-size: 11px;
        color: #94a3b8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Typing Dots */
    .tunas-typing-dots {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 2px;
    }

    .tunas-typing-dots span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #60a5fa;
        animation: tunasBounce 1.2s infinite ease-in-out;
    }

    .tunas-typing-dots span:nth-child(2) {
        animation-delay: 0.15s;
    }

    .tunas-typing-dots span:nth-child(3) {
        animation-delay: 0.3s;
    }

    @keyframes tunasBounce {
        0%, 80%, 100% { transform: scale(0.65); opacity: 0.45; }
        40% { transform: scale(1); opacity: 1; }
    }

    @media print {
        .tunas-chatbot-wrapper {
            display: none !important;
        }
    }
</style>

<div class="tunas-chatbot-wrapper" id="tunasChatbot">
    <!-- Jendela Chat Interaktif -->
    <div class="tunas-chatbot-window" id="tunasChatWindow" role="dialog" aria-label="Asisten Virtual Tunas AI">
        <!-- Header ChatBot + Pintasan WhatsApp CS -->
        <div class="tunas-chatbot-header">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="tunas-chatbot-avatar">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="min-w-0">
                    <div class="font-display font-bold text-white text-[13.5px] leading-tight whitespace-nowrap">
                        Tunas AI Assistant
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-emerald-400 font-medium mt-0.5 whitespace-nowrap">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Online &bull; Asisten SPMB</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1.5 flex-shrink-0">
                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                    class="tunas-chatbot-wa-btn" title="Chat Langsung dengan Panitia via WhatsApp">
                    <i class="fab fa-whatsapp text-sm"></i>
                    <span>Hubungi CS</span>
                </a>
                <button type="button" id="tunasChatResetBtn" class="tunas-chatbot-icon-btn" title="Mulai Ulang Percakapan">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
                <button type="button" id="tunasChatCloseBtn" class="tunas-chatbot-icon-btn" title="Tutup Chat">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Daftar Pesan Percakapan -->
        <div class="tunas-chatbot-messages" id="tunasChatMessages"></div>

        <!-- Quick Reply Chips -->
        <div class="tunas-chatbot-chips" id="tunasChatChips"></div>

        <!-- Input Bar & Footer Link WhatsApp -->
        <div class="tunas-chatbot-footer">
            <form id="tunasChatForm" class="tunas-chatbot-form" autocomplete="off">
                <input type="text" id="tunasChatInput" class="tunas-chatbot-input"
                    placeholder="Ketik pertanyaan seputar SPMB atau sekolah..." maxlength="500" required>
                <button type="submit" id="tunasChatSendBtn" class="tunas-chatbot-send" aria-label="Kirim Pesan">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
            <div class="tunas-chatbot-subfooter">
                <span>Butuh bantuan panitia manusia?</span>
                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline font-semibold ml-1">
                    <i class="fab fa-whatsapp"></i> Chat via WhatsApp CS
                </a>
            </div>
        </div>
    </div>

    <!-- Tombol Utama Floating ChatBot AI di Kanan Bawah -->
    <button type="button" id="tunasChatToggleBtn" class="tunas-chatbot-trigger" aria-label="Buka Asisten ChatBot Tunas AI">
        <span class="tunas-chatbot-trigger-icon">
            <i class="fa-solid fa-comments" id="tunasTriggerIcon"></i>
            <span class="tunas-chatbot-online-dot"></span>
        </span>
        <span class="text-left leading-tight">
            <span class="block text-xs font-extrabold tracking-wide">Tanya Tunas AI</span>
            <span class="block text-[10px] font-medium text-sky-100 opacity-90">Info SPMB & WhatsApp CS</span>
        </span>
    </button>
</div>

<script>
    (function () {
        const STORAGE_KEY = 'tunas_ai_chat_history_v2';
        const ENDPOINT = @json(route('chatbot.send', [], false));
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const STUDENT_NAME = @json($studentName);

        try {
            sessionStorage.removeItem('tunas_ai_chat_history_v1');
        } catch (e) {}

        const defaultGreeting = STUDENT_NAME
            ? `Assalamu'alaikum **${STUDENT_NAME}**! 👋\nSaya **Tunas AI**, asisten virtual SMP Tunas Harapan Bekasi. Ada yang ingin ditanyakan mengenai progres pendaftaran SPMB, Ujian CBT, atau seleksi SAW Anda?`
            : `Assalamu'alaikum! 👋 Selamat datang di **SMP Tunas Harapan Bekasi**.\nSaya **Tunas AI**, asisten virtual yang siap membantu informasi **Alur SPMB Online, Syarat Berkas, Biaya, Ujian CBT, hingga Program Unggulan**.\n\nSilakan pilih topik di bawah atau ketik pertanyaan Anda:`;

        const defaultSuggestions = STUDENT_NAME
            ? [
                'Cek status pendaftaran saya',
                'Info Ujian CBT & Seleksi SAW',
                'Biaya pendaftaran SPMB',
                'Syarat berkas pendaftaran',
            ]
            : [
                'Alur pendaftaran SPMB',
                'Syarat berkas pendaftaran',
                'Biaya pendaftaran SPMB',
                'Info Ujian CBT & Seleksi SAW',
                'Program unggulan sekolah',
            ];

        const chatWindow = document.getElementById('tunasChatWindow');
        const toggleBtn = document.getElementById('tunasChatToggleBtn');
        const closeBtn = document.getElementById('tunasChatCloseBtn');
        const resetBtn = document.getElementById('tunasChatResetBtn');
        const triggerIcon = document.getElementById('tunasTriggerIcon');
        const messagesEl = document.getElementById('tunasChatMessages');
        const chipsEl = document.getElementById('tunasChatChips');
        const formEl = document.getElementById('tunasChatForm');
        const inputEl = document.getElementById('tunasChatInput');
        const sendBtn = document.getElementById('tunasChatSendBtn');

        let history = [];

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatMarkdown(text) {
            let safe = escapeHtml(text);
            safe = safe.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            safe = safe.replace(/`([^`]+)`/g, '<code>$1</code>');

            const lines = safe.split('\n');
            let html = '';
            let inList = false;

            for (const line of lines) {
                const trimmed = line.trim();
                if (trimmed.startsWith('- ')) {
                    if (!inList) {
                        html += '<ul>';
                        inList = true;
                    }
                    html += '<li>' + trimmed.substring(2) + '</li>';
                } else {
                    if (inList) {
                        html += '</ul>';
                        inList = false;
                    }
                    if (trimmed === '') {
                        html += '<div style="height:6px;"></div>';
                    } else {
                        html += '<div>' + line + '</div>';
                    }
                }
            }
            if (inList) {
                html += '</ul>';
            }
            return html;
        }

        function saveHistory() {
            try {
                sessionStorage.setItem(STORAGE_KEY, JSON.stringify(history.slice(-12)));
            } catch (e) {}
        }

        function loadHistory() {
            try {
                const raw = sessionStorage.getItem(STORAGE_KEY);
                if (raw) {
                    const parsed = JSON.parse(raw);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        history = parsed;
                        return;
                    }
                }
            } catch (e) {}
            history = [{ role: 'assistant', content: defaultGreeting }];
        }

        function renderMessages() {
            messagesEl.innerHTML = '';
            history.forEach(item => {
                appendBubble(item.role, item.content, false);
            });
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function appendBubble(role, content, scroll = true) {
            const row = document.createElement('div');
            row.className = 'tunas-msg-row ' + (role === 'user' ? 'is-user' : 'is-bot');

            const bubble = document.createElement('div');
            bubble.className = 'tunas-msg-bubble';
            bubble.innerHTML = role === 'user' ? escapeHtml(content) : formatMarkdown(content);

            row.appendChild(bubble);
            messagesEl.appendChild(row);
            if (scroll) {
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }
        }

        function renderChips(suggestions) {
            chipsEl.innerHTML = '';
            (suggestions || defaultSuggestions).forEach(label => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'tunas-chip-btn';
                btn.textContent = label;
                btn.addEventListener('click', () => {
                    sendUserMessage(label);
                });
                chipsEl.appendChild(btn);
            });
        }

        function showTypingIndicator() {
            const row = document.createElement('div');
            row.className = 'tunas-msg-row is-bot';
            row.id = 'tunasTypingRow';
            row.innerHTML = '<div class="tunas-msg-bubble"><div class="tunas-typing-dots"><span></span><span></span><span></span></div></div>';
            messagesEl.appendChild(row);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function removeTypingIndicator() {
            const el = document.getElementById('tunasTypingRow');
            if (el) el.remove();
        }

        async function sendUserMessage(text) {
            const cleanText = String(text || '').trim();
            if (!cleanText || sendBtn.disabled) return;

            inputEl.value = '';
            history.push({ role: 'user', content: cleanText });
            saveHistory();
            appendBubble('user', cleanText);

            sendBtn.disabled = true;
            showTypingIndicator();

            try {
                const response = await fetch(ENDPOINT, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        message: cleanText,
                        history: history.slice(-7, -1),
                    }),
                });

                const data = await response.json();
                removeTypingIndicator();

                const reply = data?.reply || 'Maaf, saat ini asisten sedang sibuk. Silakan klik tombol Hubungi CS untuk terhubung via WhatsApp.';
                history.push({ role: 'assistant', content: reply });
                saveHistory();
                appendBubble('assistant', reply);

                if (Array.isArray(data?.suggestions) && data.suggestions.length > 0) {
                    renderChips(data.suggestions);
                }
            } catch (err) {
                removeTypingIndicator();
                const fallbackMsg = 'Maaf, terjadi gangguan jaringan. Anda juga dapat langsung menghubungi panitia SPMB kami melalui tombol **Hubungi CS** di bagian atas.';
                history.push({ role: 'assistant', content: fallbackMsg });
                saveHistory();
                appendBubble('assistant', fallbackMsg);
            } finally {
                sendBtn.disabled = false;
                inputEl.focus();
            }
        }

        function toggleChat(forceState) {
            const isOpen = typeof forceState === 'boolean'
                ? forceState
                : !chatWindow.classList.contains('is-open');

            if (isOpen) {
                chatWindow.classList.add('is-open');
                triggerIcon.className = 'fa-solid fa-chevron-down';
                setTimeout(() => inputEl.focus(), 150);
            } else {
                chatWindow.classList.remove('is-open');
                triggerIcon.className = 'fa-solid fa-comments';
            }
        }

        toggleBtn?.addEventListener('click', () => toggleChat());
        closeBtn?.addEventListener('click', () => toggleChat(false));

        resetBtn?.addEventListener('click', () => {
            history = [{ role: 'assistant', content: defaultGreeting }];
            saveHistory();
            renderMessages();
            renderChips(defaultSuggestions);
        });

        formEl?.addEventListener('submit', (e) => {
            e.preventDefault();
            sendUserMessage(inputEl.value);
        });

        loadHistory();
        renderMessages();
        renderChips(defaultSuggestions);
    })();
</script>
