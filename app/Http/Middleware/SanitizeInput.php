<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Fields that should NOT be sanitized (passwords, tokens, file inputs, etc.)
     */
    private const SKIP_FIELDS = [
        'password',
        'password_confirmation',
        'current_password',
        '_token',
        '_method',
        'map_iframe',    // Admin settings: raw HTML iframe allowed for admin only
    ];

    /**
     * Handle an incoming request — strip dangerous characters from all string inputs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        $sanitized = $this->sanitizeArray($input);
        $request->merge($sanitized);

        return $next($request);
    }

    /**
     * Recursively sanitize all string values in an array.
     */
    private function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, self::SKIP_FIELDS, true)) {
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                // Strip HTML/PHP tags to prevent XSS
                // htmlspecialchars_decode is NOT used — we keep the safe version
                $data[$key] = strip_tags($value);
                // Remove null bytes (SQL injection vector)
                $data[$key] = str_replace("\0", '', $data[$key]);
                // Trim whitespace
                $data[$key] = trim($data[$key]);
            }
        }

        return $data;
    }
}
