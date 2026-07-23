import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
const origin = window.location.origin;
window.axios.defaults.baseURL = origin.includes(':5173')
    ? 'http://127.0.0.1:8000'
    : origin;

const token = localStorage.getItem('token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}