import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * WebSocket/Broadcasting disabled
 * Chat functionality uses REST API with polling instead of real-time WebSockets
 */
