// importScripts('https://storage.googleapis.com/workbox-cdn/releases/4.3.0/workbox-sw.js');
importScripts('./dist/js/workbox-sw.js');

// //Workbox Config
workbox.setConfig({
    debug: false //set to true if you want to see SW in action.
});

const { registerRoute, Route } = workbox.routing;
const { StaleWhileRevalidate } = workbox.strategies;

// Handle images:
const imageRoute = new Route(({ request }) => {
    return request.destination === 'image'
}, new StaleWhileRevalidate({
    cacheName: 'images'
}));

// Handle scripts:
const scriptsRoute = new Route(({ request }) => {
    return request.destination === 'script';
}, new StaleWhileRevalidate({
    cacheName: 'scripts'
}));

// Handle styles:
const stylesRoute = new Route(({ request }) => {
    return request.destination === 'style';
}, new StaleWhileRevalidate({
    cacheName: 'styles'
}));

// Handle fonts:
const fontRoute = new Route(({ request }) => {
    return request.destination === 'font';
}, new StaleWhileRevalidate({
    cacheName: 'fonts'
}));
registerRoute(imageRoute);
registerRoute(stylesRoute);
registerRoute(scriptsRoute);
registerRoute(fontRoute);

const CACHE_NAME = "offline";
const OFFLINE_URL = "offline.php";

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll([OFFLINE_URL])
        })
    );
});
caches.open('images').then(cache => {
    return cache.addAll(['assets/manifest_asset/5.svg'])
});
self.addEventListener("fetch", (event) => {
    if (event.request.mode === "navigate") {
        event.respondWith(
            (async () => {
                try {
                    const preloadResponse = await event.preloadResponse;
                    if (preloadResponse) {
                        return preloadResponse;
                    }
                    const networkResponse = await fetch(event.request).catch(async function (error) {
                        if (error instanceof TypeError && error.message === "Failed to fetch") {
                            const cache = await caches.open(CACHE_NAME);
                            const cachedResponse = await cache.match(OFFLINE_URL);
                            return cachedResponse;
                        }
                    });
                    return networkResponse;
                } catch (error) {
                    console.log("Fetch failed; returning offline page instead.", error);
                    const cache = await caches.open(CACHE_NAME);
                    const cachedResponse = await cache.match(OFFLINE_URL);
                    return cachedResponse;
                }
            })()
        );
    }
});
