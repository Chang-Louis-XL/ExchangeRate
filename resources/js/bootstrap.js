/**
 * 我們會將 axios HTTP 函式庫引入到 JavaScript 應用程式中。
 * 這個函式庫非常好用，允許我們執行非同步的 HTTP 請求
 * 並輕鬆執行請求與處理回應。
 */

import axios from 'axios';
window.axios = axios;

// 為所有 HTTP 請求設置默認標頭
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * 添加 CSRF 令牌作為默認標頭，這樣所有通過 axios 的請求都會帶上它
 * 這對於 POST, PUT, PATCH, DELETE 請求是必需的
 */
const csrfToken = document.head.querySelector('meta[name="csrf-token"]');

if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
