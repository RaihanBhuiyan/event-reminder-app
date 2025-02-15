import { createApp } from 'vue';
import App from './components/App.vue';
import axios from 'axios';

// Import Bootstrap's JavaScript
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
// Configure axios
axios.defaults.baseURL = 'http://localhost:8000';

// Create Vue app
const app = createApp(App);

// Service Worker Registration
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(() => console.log('Service Worker Registered'))
        .catch((err) => console.log('Registration Failed:', err));
}

// Provide axios globally to Vue components
app.config.globalProperties.$axios = axios;

// Sync offline events when coming online
window.addEventListener('online', async () => {
    const offlineEvents = JSON.parse(localStorage.getItem('offlineEvents') || '[]');
    if (offlineEvents.length > 0) {
        try {
            await Promise.all(offlineEvents.map(event =>
                axios.post('/api/events', event)
            ));
            localStorage.removeItem('offlineEvents');
            console.log('Offline events synced successfully');
        } catch (error) {
            console.error('Sync failed:', error);
        }
    }
});

// Mount the app
app.mount('#app');
