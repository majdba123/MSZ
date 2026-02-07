import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['Content-Type'] = 'application/json';

/**
 * Token management utilities.
 */
window.Auth = {
    getToken() {
        return localStorage.getItem('auth_token');
    },

    setToken(token) {
        localStorage.setItem('auth_token', token);
        this.applyToken();
    },

    removeToken() {
        localStorage.removeItem('auth_token');
        delete window.axios.defaults.headers.common['Authorization'];
    },

    applyToken() {
        const token = this.getToken();
        if (token) {
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        }
    },

    isAuthenticated() {
        return !!this.getToken();
    },
};

// Apply token on page load if it exists
window.Auth.applyToken();
