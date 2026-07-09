const CACHE_NAME = 'flyingstar-v3';
const ASSETS = [
  'index.html',
  'reports.html',
  'manifest.json',
  'icons/favicon.ico',
  'icons/favicon-16.png',
  'icons/favicon-32.png',
  'icons/apple-touch-icon.png',
  'icons/icon-192.png',
  'icons/icon-512.png',
  'icons/icon-192-maskable.png',
  'icons/icon-512-maskable.png'
];

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((names) =>
      Promise.all(names.filter(n => n !== CACHE_NAME).map(n => caches.delete(n)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (e) => {
  if (e.request.method !== 'GET') return;
  e.respondWith(
    caches.match(e.request).then((cached) => {
      if (cached) return cached;
      return fetch(e.request)
        .then((response) => {
          // نخزن نسخة من أي ملف جديد تم تحميله بنجاح (نفس الأصل فقط)
          if (response && response.ok && e.request.url.startsWith(self.location.origin)) {
            const copy = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(e.request, copy));
          }
          return response;
        })
        .catch(() => {
          // في حالة عدم الاتصال وعدم توفر نسخة مخزنة، ارجع للصفحة الرئيسية كحل بديل
          if (e.request.mode === 'navigate') return caches.match('index.html');
        });
    })
  );
});
