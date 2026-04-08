<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $global_app_title ?? config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#4f46e5">
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
        
        <script>
            function urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }

            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('ServiceWorker registered with scope:', registration.scope);
                    
                    @auth
                    if ('PushManager' in window) {
                        registration.pushManager.getSubscription().then(subscription => {
                            if (!subscription) {
                                // Request subscription gracefully on next interaction if needed, 
                                // but for simplicity we prompt directly here if authorized.
                                const VAPID_PUB = '{{ env("VAPID_PUBLIC_KEY") }}';
                                if(VAPID_PUB && confirm('¿Deseas activar las notificaciones push para cambios en lotes?')) {
                                    registration.pushManager.subscribe({
                                        userVisibleOnly: true,
                                        applicationServerKey: urlBase64ToUint8Array(VAPID_PUB)
                                    }).then(newSubscription => {
                                        fetch('{{ route("push.subscribe") }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify(newSubscription)
                                        });
                                    }).catch(err => console.error('Push subscription failed:', err));
                                }
                            }
                        });
                    }
                    @endauth
                }).catch(err => console.error('ServiceWorker registration failed:', err));
            }
        </script>
    </body>
</html>
