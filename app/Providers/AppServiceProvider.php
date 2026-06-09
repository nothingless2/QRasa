<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Paginator::defaultView('pagination::tailwind');
        Paginator::defaultSimpleView('pagination::simple-tailwind');

        // ── Rate Limiters ─────────────────────────────────────────────────
        // Global: 100 requests / minute / IP
        RateLimiter::for('global-web', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Terlalu banyak permintaan. Coba lagi dalam satu menit.',
                    ], 429);
                });
        });

        // Auth endpoints: 10 requests / minute / IP
        RateLimiter::for('auth-routes', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(function () {
                    return back()
                        ->withErrors(['email' => 'Terlalu banyak percobaan. Silakan tunggu sebentar.'])
                        ->withInput($request->except('password'));
                });
        });
    }
}
