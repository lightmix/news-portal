import axios from 'axios';
import './echo';
import moment from 'moment/min/moment-with-locales';

moment.locale(navigator.language)

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Echo.connector.pusher.connection.bind('connected', function () {
    window.axios.defaults.headers.common['X-Socket-Id'] = window.Echo.socketId();
});
