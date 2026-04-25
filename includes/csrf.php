<?php
$csrfToken = htmlspecialchars(tfGetCsrfToken(), ENT_QUOTES);
?>
<script>
    window.TF_CSRF_TOKEN = '<?= $csrfToken ?>';
    (function () {
        const token = window.TF_CSRF_TOKEN;
        if (!token) return;
        const originalFetch = window.fetch;
        if (typeof originalFetch === 'function') {
            window.fetch = function (input, init) {
                const options = init ? { ...init } : {};
                const headers = new Headers(options.headers || {});
                if (!headers.has('X-CSRF-Token')) {
                    headers.set('X-CSRF-Token', token);
                }
                options.headers = headers;
                return originalFetch(input, options);
            };
        }
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;
            const method = (form.getAttribute('method') || 'GET').toUpperCase();
            if (method === 'GET') return;
            if (form.querySelector('input[name="_csrf"]')) return;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_csrf';
            input.value = token;
            form.appendChild(input);
        }, true);
    })();
</script>
