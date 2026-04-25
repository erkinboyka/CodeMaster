<div id="tf-toast-root" class="tf-toast-root"></div>

<style>
    .tf-toast-root {
        position: fixed;
        top: calc(16px + env(safe-area-inset-top));
        right: 16px;
        z-index: 2147483647;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
        max-height: calc(100vh - 32px);
        overflow-y: auto;
        width: min(360px, calc(100vw - 24px));
    }

    .tf-toast {
        background: #0f172a;
        color: #ffffff;
        font-size: 14px;
        line-height: 1.4;
        padding: 12px 14px;
        border-radius: 10px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.12);
        max-width: 100%;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        pointer-events: auto;
        animation: tf-toast-in 180ms ease-out;
    }

    .tf-toast::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #38bdf8;
        margin-top: 6px;
        flex-shrink: 0;
    }

    .tf-toast--success {
        background: #065f46;
    }

    .tf-toast--success::before {
        background: #34d399;
    }

    .tf-toast--warning {
        background: #92400e;
    }

    .tf-toast--warning::before {
        background: #fbbf24;
    }

    .tf-toast--error {
        background: #991b1b;
    }

    .tf-toast--error::before {
        background: #f87171;
    }

    @keyframes tf-toast-in {
        from {
            transform: translateY(-8px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @media (max-width: 640px) {
        .tf-toast-root {
            left: 12px;
            right: 12px;
            top: 12px;
            width: auto;
            max-height: calc(100vh - 24px);
        }
    }
</style>

<script>
    (() => {
        const toastRoot = document.getElementById('tf-toast-root');

        function tryDecodeCp1251Mojibake(input) {
            if (typeof input !== 'string' || !/[]/.test(input)) return input;
            const bytes = [];
            for (const ch of input) {
                const code = ch.charCodeAt(0);
                if (code <= 0x7f) {
                    bytes.push(code);
                    continue;
                }
                if (code >= 0x0410 && code <= 0x044f) {
                    bytes.push(code - 0x0350);
                    continue;
                }
                if (code === 0x0401) { bytes.push(0xa8); continue; }
                if (code === 0x0451) { bytes.push(0xb8); continue; }
                if (code === 0x2019) { bytes.push(0x92); continue; }
                if (code === 0x2116) { bytes.push(0xb9); continue; }
                return input;
            }
            try {
                const decoded = new TextDecoder('utf-8', { fatal: false }).decode(new Uint8Array(bytes));
                if (/[А-Яа-яЁё]/.test(decoded)) {
                    return decoded;
                }
            } catch (e) { }
            return input;
        }

        function normalizeMessage(message) {
            if (typeof message !== 'string') return message;
            let text = message.trim();
            text = tryDecodeCp1251Mojibake(text);
            text = text.replace(/sss/g, '').replace(/\uFFFD/g, '');
            return text;
        }

        function showToast(message, type = 'info') {
            const text = normalizeMessage(message);
            if (!toastRoot || !text) return;
            while (toastRoot.firstChild) {
                toastRoot.removeChild(toastRoot.firstChild);
            }
            const toast = document.createElement('div');
            toast.className = 'tf-toast' + (type ? ` tf-toast--${type}` : '');
            toast.textContent = text;
            toastRoot.prepend(toast);
            setTimeout(() => toast.remove(), 3500);
        }

        window.tfNotify = (message, type = 'info') => {
            showToast(message, type);
        };

        window.tfConfirm = (message) => {
            const text = normalizeMessage(message || 'Подтвердите действие');
            return Promise.resolve(window.confirm(text));
        };

        if (!window.tfJsonPatchApplied && window.Response && Response.prototype && Response.prototype.json) {
            window.tfJsonPatchApplied = true;
            const originalJson = Response.prototype.json;
            Response.prototype.json = async function () {
                try {
                    const text = await this.text();
                    const cleaned = (text || '').replace(/^\uFEFF/, '').trim();
                    if (!cleaned) {
                        return { success: false, message: 'Пустой ответ сервера' };
                    }
                    if (cleaned[0] === '<') {
                        return { success: false, message: 'Ошибка сервера' };
                    }
                    try {
                        return JSON.parse(cleaned);
                    } catch (e) {
                        return { success: false, message: 'Некорректный ответ сервера' };
                    }
                } catch (e) {
                    return originalJson.call(this);
                }
            };
        }
    })();
</script>
