import axios from 'axios';
window.axios = axios;
import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: window.PUSHER_APP_KEY,
    cluster: window.PUSHER_APP_CLUSTER,
    forceTLS: true
});

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
