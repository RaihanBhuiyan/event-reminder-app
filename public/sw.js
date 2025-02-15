const CACHE_NAME = 'event-reminder-v1';
const ASSETS = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/offline'  // Create an offline.html page
];

// Install event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(ASSETS))
    );
});

// Fetch event
self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
            .catch(() => caches.match('/offline'))
    );
});