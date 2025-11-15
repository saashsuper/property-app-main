<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-bs-theme="light" data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | PROMAN - Property Management System </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Property Management System" name="description" />
    <meta content="PROMAN" name="author" />
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#667eea">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="ProMan">
    <meta name="msapplication-TileColor" content="#667eea">
    <meta name="msapplication-config" content="/browserconfig.xml">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('images/logos/absolute-property-group-favicon.svg') }}">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.head-css')
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->


    {{-- @include('layouts.customizer') --}}


    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
    @stack('scripts')
    
    <!-- Clear customizer cache and hide popup -->
    <script>
        // Clear any customizer-related session storage
        if (typeof(Storage) !== "undefined") {
            sessionStorage.removeItem("defaultAttribute");
            sessionStorage.removeItem("data-theme");
            sessionStorage.removeItem("data-layout");
            sessionStorage.removeItem("data-sidebar-size");
            sessionStorage.removeItem("data-bs-theme");
            sessionStorage.removeItem("data-layout-width");
            sessionStorage.removeItem("data-sidebar");
            sessionStorage.removeItem("data-sidebar-image");
            sessionStorage.removeItem("data-layout-position");
            sessionStorage.removeItem("data-layout-style");
            sessionStorage.removeItem("data-topbar");
            sessionStorage.removeItem("data-preloader");
        }
        
        // Force close any customizer popup that might appear
        document.addEventListener('DOMContentLoaded', function() {
            // Hide customizer elements
            const customizerElements = document.querySelectorAll('.customizer-setting, #theme-settings-offcanvas, .offcanvas-end');
            customizerElements.forEach(function(element) {
                if (element) {
                    element.style.display = 'none !important';
                    element.classList.add('d-none');
                }
            });
            
            // Close any open offcanvas
            const offcanvasElements = document.querySelectorAll('.offcanvas');
            offcanvasElements.forEach(function(element) {
                if (element && element.classList.contains('show')) {
                    const offcanvas = new bootstrap.Offcanvas(element);
                    offcanvas.hide();
                }
            });
        });
    </script>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                        
                        // Check for updates
                        registration.addEventListener('updatefound', function() {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', function() {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // New content is available, show update notification
                                    if (confirm('New version available! Reload to update?')) {
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
        
        // PWA Install Prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later
            deferredPrompt = e;
            
            // Show install button or notification
            showInstallPrompt();
        });
        
        function showInstallPrompt() {
            // Install prompt disabled - button hidden
            console.log('PWA install prompt available but hidden');
            
            // Install button is hidden - no longer showing
            return;
            
            // Example: Show a custom install button
            if (deferredPrompt) {
                // Create and show install button
                const installButton = document.createElement('button');
                installButton.textContent = 'Install App';
                installButton.className = 'btn btn-primary';
                installButton.style.position = 'fixed';
                installButton.style.bottom = '20px';
                installButton.style.right = '20px';
                installButton.style.zIndex = '9999';
                
                installButton.addEventListener('click', () => {
                    // Show the install prompt
                    deferredPrompt.prompt();
                    // Wait for the user to respond to the prompt
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the install prompt');
                        } else {
                            console.log('User dismissed the install prompt');
                        }
                        deferredPrompt = null;
                    });
                });
                
                document.body.appendChild(installButton);
                
                // Auto-hide after 10 seconds
                setTimeout(() => {
                    if (installButton.parentNode) {
                        installButton.parentNode.removeChild(installButton);
                    }
                }, 10000);
            }
        }
        
        // Handle app installed event
        window.addEventListener('appinstalled', (evt) => {
            console.log('PWA was installed');
            // Hide install button if visible
            const installButton = document.querySelector('button[style*="position: fixed"]');
            if (installButton) {
                installButton.remove();
            }
        });
        
        // Online/Offline status
        window.addEventListener('online', () => {
            console.log('App is online');
            // You can show a notification or update UI
        });
        
        window.addEventListener('offline', () => {
            console.log('App is offline');
            // You can show a notification or update UI
        });
    </script>
</body>

</html>