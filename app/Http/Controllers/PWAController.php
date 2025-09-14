<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PWAController extends Controller
{
    /**
     * Generate dynamic manifest.json
     */
    public function manifest(): JsonResponse
    {
        $manifest = [
            'name' => config('app.name', 'Property Management App'),
            'short_name' => 'ProMan',
            'description' => 'A comprehensive property management application for managing properties, units, contractors, and more',
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#667eea',
            'orientation' => 'portrait-primary',
            'scope' => '/',
            'lang' => app()->getLocale(),
            'dir' => 'ltr',
            'categories' => ['business', 'productivity', 'utilities'],
            'icons' => $this->getIcons(),
            'screenshots' => $this->getScreenshots(),
            'shortcuts' => $this->getShortcuts(),
        ];

        return response()->json($manifest)
            ->header('Content-Type', 'application/manifest+json')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Serve service worker with proper headers
     */
    public function serviceWorker(): Response
    {
        $swContent = file_get_contents(public_path('sw.js'));
        
        return response($swContent)
            ->header('Content-Type', 'application/javascript')
            ->header('Cache-Control', 'public, max-age=0, must-revalidate')
            ->header('Service-Worker-Allowed', '/');
    }

    /**
     * Show offline page
     */
    public function offline()
    {
        return view('offline');
    }

    /**
     * Get app icons configuration
     */
    private function getIcons(): array
    {
        return [
            [
                'src' => '/images/icons/icon-72x72.png',
                'sizes' => '72x72',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/images/icons/icon-96x96.png',
                'sizes' => '96x96',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/images/icons/icon-128x128.png',
                'sizes' => '128x128',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/images/icons/icon-144x144.png',
                'sizes' => '144x144',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/images/icons/icon-152x152.png',
                'sizes' => '152x152',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/images/icons/icon-192x192.png',
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'maskable any'
            ],
            [
                'src' => '/images/icons/icon-384x384.png',
                'sizes' => '384x384',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => '/images/icons/icon-512x512.png',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'maskable any'
            ]
        ];
    }

    /**
     * Get screenshots for app stores
     */
    private function getScreenshots(): array
    {
        return [
            [
                'src' => '/images/screenshots/desktop-screenshot.png',
                'sizes' => '1280x720',
                'type' => 'image/png',
                'form_factor' => 'wide',
                'label' => 'Desktop Dashboard View'
            ],
            [
                'src' => '/images/screenshots/mobile-screenshot.png',
                'sizes' => '390x844',
                'type' => 'image/png',
                'form_factor' => 'narrow',
                'label' => 'Mobile Property Management'
            ]
        ];
    }

    /**
     * Get app shortcuts
     */
    private function getShortcuts(): array
    {
        return [
            [
                'name' => 'Dashboard',
                'short_name' => 'Dashboard',
                'description' => 'View property management dashboard',
                'url' => '/dashboard',
                'icons' => [
                    [
                        'src' => '/images/icons/shortcut-dashboard.png',
                        'sizes' => '96x96'
                    ]
                ]
            ],
            [
                'name' => 'Add Property',
                'short_name' => 'Add Property',
                'description' => 'Add a new property',
                'url' => '/properties/create',
                'icons' => [
                    [
                        'src' => '/images/icons/shortcut-add.png',
                        'sizes' => '96x96'
                    ]
                ]
            ],
            [
                'name' => 'Blocks',
                'short_name' => 'Blocks',
                'description' => 'Manage property blocks',
                'url' => '/blocks',
                'icons' => [
                    [
                        'src' => '/images/icons/shortcut-blocks.png',
                        'sizes' => '96x96'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get PWA installation status
     */
    public function installStatus(Request $request): JsonResponse
    {
        $userAgent = $request->header('User-Agent');
        $isMobile = preg_match('/Mobile|Android|iPhone|iPad/', $userAgent);
        $isChrome = preg_match('/Chrome/', $userAgent);
        $isSafari = preg_match('/Safari/', $userAgent) && !preg_match('/Chrome/', $userAgent);
        
        return response()->json([
            'can_install' => $isMobile && ($isChrome || $isSafari),
            'platform' => $this->detectPlatform($userAgent),
            'browser' => $this->detectBrowser($userAgent)
        ]);
    }

    /**
     * Detect platform from user agent
     */
    private function detectPlatform(string $userAgent): string
    {
        if (preg_match('/Android/', $userAgent)) return 'android';
        if (preg_match('/iPhone|iPad/', $userAgent)) return 'ios';
        if (preg_match('/Windows/', $userAgent)) return 'windows';
        if (preg_match('/Mac/', $userAgent)) return 'macos';
        if (preg_match('/Linux/', $userAgent)) return 'linux';
        return 'unknown';
    }

    /**
     * Detect browser from user agent
     */
    private function detectBrowser(string $userAgent): string
    {
        if (preg_match('/Chrome/', $userAgent)) return 'chrome';
        if (preg_match('/Firefox/', $userAgent)) return 'firefox';
        if (preg_match('/Safari/', $userAgent) && !preg_match('/Chrome/', $userAgent)) return 'safari';
        if (preg_match('/Edge/', $userAgent)) return 'edge';
        return 'unknown';
    }
}
