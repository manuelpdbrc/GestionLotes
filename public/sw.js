self.addEventListener('install', (event) => {
    // Instalación: por ahora no cacheamos estáticos porque estamos en desarrollo,
    // pero está preparado para "offline-first" si se agregan a la cache.
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const sendNotification = body => {
        const title = "Administración de Lotes";
        
        // Parse data if JSON
        let data = { title: title, body: body, icon: '/icon-192.png', badge: '/icon-192.png' };
        try {
            const json = JSON.parse(body);
            data = { ...data, ...json.notification };
            if(data.title) title = data.title;
        } catch (e) {}

        const options = {
            body: data.body,
            icon: data.icon || '/icon-192.png',
            badge: data.badge || '/icon-192.png',
            vibrate: [200, 100, 200, 100, 200, 100, 200],
            data: data.data || {}
        };

        return self.registration.showNotification(data.title || title, options);
    };

    if (event.data) {
        event.waitUntil(sendNotification(event.data.text()));
    }
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const urlToOpen = new URL(event.notification.data.url || '/', self.location.origin).href;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            let matchingClient = null;
            for (let i = 0; i < windowClients.length; i++) {
                const windowClient = windowClients[i];
                if (windowClient.url === urlToOpen) {
                    matchingClient = windowClient;
                    break;
                }
            }

            if (matchingClient) {
                return matchingClient.focus();
            } else {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});
