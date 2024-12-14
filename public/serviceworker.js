const staticCacheName = "MandegarPars-static-v1.00";
const dynamicCacheName = "MandegarPars-dynamic-v1.00";
const assets = [

];

// Install Service Worker
self.addEventListener('install', evt => {
    console.log("سرویس وب اپ با موفقیت نصب شد");
    evt.waitUntil(
        caches.open(staticCacheName).then(cache => {
            console.log("ذخیره سازی آفلاین");
            cache.addAll(assets);
        })
    );
});
// Active Service Worker
self.addEventListener("activate", evt => {
    console.log("سرویس وب اپ با موفیت فعال شد")
    evt.waitUntil(
        caches.keys().then(keys => {
            console.log(keys)
            return Promise.all(keys
                .filter(key => key !== staticCacheName && key !== dynamicCacheName)
                .map(key => caches.delete(key))
            )
        })
    );
});
// fetch Service Worker
// self.addEventListener('fetch', evt => {
//   console.log('fetch event', evt);
//   evt.respondWith(
//     caches.match(evt.request).then(cacheRes => {
//       return cacheRes || fetch(evt.request).then(fetchRes => {
//         // return caches.open(dynamicCacheName).then(cache => {
//         //   cache.put(evt.request.url, fetchRes.clone());
//         //   return fetchRes;
//         // })
//       });
//     }).catch(()=> caches.match('/assets/fallback.html'))
//   );
// });



// self.addEventListener("install", evt => {
//   evt.waitUntil(
//     caches.open(staticCacheName).then(cache => {
//         console.log("caching assets...");
//         cache.addAll(cacheAssets);
//       })
//       .catch(err => {})
//   );
// });
// // self.addEventListener("active",evt => {

// // })
self.addEventListener("fetch", evt => {
    evt.respondWith(
        caches
            .match(evt.request)
            .then(res => {
                return res || fetch(evt.request);
            })
            .catch(err => {
                if (evt.request.url.indexOf(".html") > -1) {
                    return caches.match("fallback.html");
                }
            })
    );
});
