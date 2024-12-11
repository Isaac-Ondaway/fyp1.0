import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Axios setup
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Pusher setup
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'b49793dfdac952bca68c',
    cluster: 'ap1',
    forceTLS: true,
});
