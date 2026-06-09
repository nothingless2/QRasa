<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Trusted CDN sources used by the application.
     * Keep in sync with actual assets loaded in Blade templates.
     */
    private const TRUSTED_SCRIPTS = [
        "'self'",
        'https://cdn.jsdelivr.net',
        'https://cdnjs.cloudflare.com',
        'https://unpkg.com',
        "'unsafe-inline'", // Required for Alpine.js x-data inline handlers
    ];

    private const TRUSTED_STYLES = [
        "'self'",
        'https://fonts.bunny.net',
        'https://fonts.googleapis.com',
        'https://cdnjs.cloudflare.com',
        "'unsafe-inline'", // Required for Tailwind JIT + Alpine
    ];

    private const TRUSTED_FONTS = [
        "'self'",
        'https://fonts.bunny.net',
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
        'https://cdnjs.cloudflare.com',
        'data:',
    ];

    private const TRUSTED_IMAGES = [
        "'self'",
        'data:',
        'blob:',
        'https:',
    ];

    private const TRUSTED_CONNECT = [
        "'self'",
        'https://actions.google.com', // KDS notification sound
    ];

    private const TRUSTED_FRAMES = [
        "'self'",
        'https://www.google.com', // Google Maps iframe
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Build a permissive-but-safe CSP that works on both HTTP (dev) and HTTPS (prod)
        $appUrl = config('app.url', 'http://localhost');

        $csp = implode('; ', [
            "default-src 'self'",
            // Alpine.js v3 uses new Function() at runtime to evaluate template expressions
            // (x-data, x-show, @click, etc.) — this requires 'unsafe-eval' regardless of
            // whether Alpine is bundled via Vite/npm or loaded from CDN.
            // To remove unsafe-eval, switch to @alpinejs/csp build (has expression limitations).
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://www.googletagmanager.com",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com https://cdnjs.cloudflare.com https://unpkg.com",
            "font-src 'self' data: https://fonts.bunny.net https://fonts.googleapis.com https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' ws: wss: https:",
            "frame-src 'self' https://www.google.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Content-Security-Policy', $csp);

        // Only add HSTS in production (HTTPS only — don't add on HTTP/localhost)
        if (config('app.env') === 'production') {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        return $response;
    }
}
