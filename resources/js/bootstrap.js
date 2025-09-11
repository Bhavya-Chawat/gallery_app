import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Ensure SPA auth via Sanctum works with cookies on same-origin
window.axios.defaults.withCredentials = true;

// Ensure API requests target the Laravel app origin (not Vite dev/preview)
try {
    const appOrigin = (typeof window !== 'undefined' && window.Ziggy && window.Ziggy.url) ? window.Ziggy.url : window.location.origin;
    window.axios.defaults.baseURL = appOrigin;
} catch (_) {
    // Fallback: keep default
}
