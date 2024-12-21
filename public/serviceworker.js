const staticCacheName = "MandegarPars-static-v1.01";
const dynamicCacheName = "MandegarPars-dynamic-v1.01";
const assets = [
    "/",
    "/index.html",
    "/assets/css/style.css",
    "/assets/js/script.js",
    "/assets/media/image/icon-192x192.png",
    "/assets/media/image/icon-512x512.png",
    "/assets/fallback.html"
];

// محدودیت تعداد آیتم‌های کش داینامیک
const limitCacheSize = (cacheName, maxItems) => {
    caches.open(cacheName).then(cache => {
        cache.keys().then(keys => {
            if (keys.length > maxItems) {
                cache.delete(keys[0]).then(() => limitCacheSize(cacheName, maxItems));
            }
        });
    });
};

// نصب سرویس ورکر
self.addEventListener("install", evt => {
    console.log("سرویس ورکر نصب شد");
    evt.waitUntil(
        caches.open(staticCacheName).then(cache => {
            console.log("ذخیره‌سازی فایل‌های استاتیک در کش");
            cache.addAll(assets);
        })
    );
});

// فعال‌سازی سرویس ورکر
self.addEventListener("activate", evt => {
    console.log("سرویس ورکر فعال شد");
    evt.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys
                    .filter(key => key !== staticCacheName && key !== dynamicCacheName)
                    .map(key => caches.delete(key))
            );
        })
    );
});

// مدیریت درخواست‌ها
self.addEventListener("fetch", evt => {
    if (evt.request.url.indexOf("http") === 0) {
        evt.respondWith(
            caches.match(evt.request).then(cacheRes => {
                return (
                    cacheRes ||
                    fetch(evt.request)
                        .then(fetchRes => {
                            return caches.open(dynamicCacheName).then(cache => {
                                cache.put(evt.request.url, fetchRes.clone());
                                limitCacheSize(dynamicCacheName, 50); // محدود کردن تعداد آیتم‌ها به 50
                                return fetchRes;
                            });
                        })
                        .catch(() => {
                            if (evt.request.url.indexOf(".html") > -1) {
                                return caches.match("/assets/fallback.html");
                            }
                        })
                );
            })
        );
    }
});
