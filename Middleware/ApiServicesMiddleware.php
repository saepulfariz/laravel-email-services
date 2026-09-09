<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiLog;

class ApiServicesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $serviceSlug): Response
    {
        $apikey = $request->input('apikey');

        if (!$apikey) {
            $response = response()->json([
                'success' => false,
                'message' => 'API Key is missing.'
            ], 401);
            
            $this->logRequest($request, null, null, $response, 'API Key is missing.');
            return $response;
        }

        $service = \App\Models\Service::where('slug', $serviceSlug)->first();

        if (!$service) {
            $response = response()->json([
                'success' => false,
                'message' => 'Service ' . strtoupper(str_replace('-', ' ', $serviceSlug)) . ' not found.'
            ], 500);
            
            $this->logRequest($request, null, null, $response, 'Service not found.');
            return $response;
        }

        // Check API Key against database
        $hashedKey = hash('sha256', $apikey);
        $validKey = \App\Models\ApiKeyService::where('service_id', $service->id)
            ->where('is_active', true)
            ->whereHas('apiKey', function ($query) use ($hashedKey) {
                $query->where('key_hash', $hashedKey)
                    ->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    });
            })->first();

        if (!$validKey) {
            $response = response()->json([
                'success' => false,
                'message' => 'Invalid or Expired API Key'
            ], 401);
            
            $this->logRequest($request, null, $service->id, $response, 'Invalid or Expired API Key');
            return $response;
        }

        // Check Rate Limit based on ApiLog
        $rateLimit = \App\Models\ApiRateLimit::where('api_key_service_id', $validKey->id)->first();
        if ($rateLimit) {
            $period = strtolower(trim($rateLimit->period));
            
            $startTime = now();
            if ($period === 'minute') {
                $startTime = now()->subMinute();
            } elseif ($period === 'hour') {
                $startTime = now()->subHour();
            } elseif ($period === 'day') {
                $startTime = now()->subDay();
            } elseif ($period === 'month') {
                $startTime = now()->subMonth();
            } elseif ($period === 'year') {
                $startTime = now()->subYear();
            } elseif (is_numeric($period)) {
                $startTime = now()->subMinutes((int)$period);
            } else {
                $startTime = now()->subMinute(); // fallback
            }

            // Hitung jumlah request dari ApiLog
            // Kita tidak menghitung request yang berstatus 429 (Too Many Requests) agar tidak mengurangi 'saldo' saat mereka ditolak
            $requestCount = \App\Models\ApiLog::where('api_key_id', $validKey->api_key_id)
                ->where('service_id', $service->id)
                ->where('created_at', '>=', $startTime)
                ->where('status_code', '!=', 429)
                ->count();

            if ($requestCount >= $rateLimit->max_requests) {
                $response = response()->json([
                    'success' => false,
                    'message' => 'Too Many Requests'
                ], 429);
                
                $this->logRequest($request, $validKey->api_key_id, $service->id, $response, 'Rate limit exceeded');
                return $response;
            }
        }

        $response = $next($request);

        $message = 'Request processed successfully';
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            if (isset($data['message'])) {
                $message = $data['message'];
            }
        }

        $this->logRequest($request, $validKey->api_key_id, $service->id, $response, $message);

        return $response;
    }

    /**
     * Log the API request into the database.
     */
    private function logRequest(Request $request, $apiKeyId, $serviceId, Response $response, string $message): void
    {
        ApiLog::create([
            'api_key_id' => $apiKeyId,
            'service_id' => $serviceId,
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'ip' => $this->getRealIp($request),
            'domain' => $this->getRealDomain($request),
            'status_code' => $response->getStatusCode(),
            'message' => $message,
        ]);
    }

    /**
     * Get real IP from request (support behind reverse proxy like Nginx Proxy Manager)
     */
    private function getRealIp(Request $request): string
    {
        $ip = $request->header('x-forwarded-for') ?? $request->header('x-real-ip') ?? $request->ip();
        
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }
        
        return $ip;
    }

    /**
     * Get the consuming application's domain using Origin or Referer headers.
     * Falls back to the request host if not available.
     */
    private function getRealDomain(Request $request): string
    {
        // For frontend/AJAX requests, the 'Origin' header contains the URL of the consumer
        $origin = $request->header('origin');
        if ($origin) {
            return parse_url($origin, PHP_URL_HOST) ?? $origin;
        }

        // Referer is another common header that indicates the consuming page
        $referer = $request->header('referer');
        if ($referer) {
            return parse_url($referer, PHP_URL_HOST) ?? $referer;
        }

        // Fallback to the requested host (might be the API's own domain or NPM proxy domain)
        return $request->getHost();
    }
}
