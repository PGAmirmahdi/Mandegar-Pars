self.addEventListener('install', event => {
    console.log('نصب سرویس ورکر.');
    event.waitUntil(
        caches.open('pwa-cache').then(cache => {
            return cache.addAll([
                '/',
                '/css/app.css',
                '/js/app.js',
                '/assets/media/image/icons/logo-sm.png',
                '/assets/media/image/icons/logo-lg.png'
            ]);
        })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );
});
