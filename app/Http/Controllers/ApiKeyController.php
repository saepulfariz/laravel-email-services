<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiKey;
use App\Models\Service;
use App\Models\ApiKeyService;
use App\Models\ApiRateLimit;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ApiKeyController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:api-keys.view', only: ['index']),
            new Middleware('permission:api-keys.create', only: ['create', 'store']),
            new Middleware('permission:api-keys.edit', only: ['edit', 'update']),
            new Middleware('permission:api-keys.delete', only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        $query = ApiKey::with(['user', 'apiKeyServices.service']);

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $apiKeys = $query->latest()->paginate(10)->withQueryString();
        $title = 'API Keys Management';

        return view('api-keys.index', compact('apiKeys', 'title', 'search'));
    }

    public function create()
    {
        $title = 'Generate New API Key';
        $services = Service::where('is_active', true)->get();

        return view('api-keys.create', compact('title', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
            'service_settings' => 'nullable|array',
        ]);

        // Generate Plain Token (e.g. ak_live_...)
        $envPrefix = app()->environment('production') ? 'ak_live_' : 'ak_test_';
        $plainToken = $envPrefix . Str::random(64);

        // Hash for storage
        $hashedToken = hash('sha256', $plainToken);

        $apiKey = ApiKey::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'key_hash' => $hashedToken,
            'is_active' => true,
            'expires_at' => $request->expires_at,
        ]);

        // Attach Services and settings
        if ($request->has('services')) {
            foreach ($request->services as $serviceId) {
                $settings = $request->service_settings[$serviceId] ?? [];

                // Parse allowed IPs and Domains (comma separated or JSON)
                $allowedIps = null;
                if (!empty($settings['allowed_ips'])) {
                    $allowedIps = array_map('trim', explode(',', $settings['allowed_ips']));
                }

                $allowedDomains = null;
                if (!empty($settings['allowed_domains'])) {
                    $allowedDomains = array_map('trim', explode(',', $settings['allowed_domains']));
                }

                $pivot = ApiKeyService::create([
                    'api_key_id' => $apiKey->id,
                    'service_id' => $serviceId,
                    'is_active' => true,
                    'allowed_ips' => $allowedIps,
                    'allowed_domains' => $allowedDomains,
                ]);

                // Create Rate Limits if provided
                if (!empty($settings['rate_limits'])) {
                    foreach ($settings['rate_limits'] as $rateLimit) {
                        if (!empty($rateLimit['max_requests']) && !empty($rateLimit['period'])) {
                            ApiRateLimit::create([
                                'api_key_service_id' => $pivot->id,
                                'max_requests' => (int) $rateLimit['max_requests'],
                                'period' => $rateLimit['period'],
                            ]);
                        }
                    }
                }
            }
        }

        // Flash plain token to session so user can copy it ONCE
        return redirect()->route('api-keys.index')
            ->with('success', 'API Key generated successfully!')
            ->with('plain_token', $plainToken);
    }

    public function edit(ApiKey $apiKey)
    {
        $title = 'Edit API Key';
        $services = Service::where('is_active', true)->get();
        $apiKey->load(['apiKeyServices.rateLimits']);

        return view('api-keys.edit', compact('apiKey', 'title', 'services'));
    }

    public function update(Request $request, ApiKey $apiKey)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
            'service_settings' => 'nullable|array',
        ]);

        $apiKey->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
            'expires_at' => $request->expires_at,
        ]);

        // Sync Services
        $selectedServices = $request->input('services', []);

        // Remove unselected services
        $apiKey->apiKeyServices()->whereNotIn('service_id', $selectedServices)->delete();

        foreach ($selectedServices as $serviceId) {
            $settings = $request->service_settings[$serviceId] ?? [];

            $allowedIps = !empty($settings['allowed_ips']) ? array_map('trim', explode(',', $settings['allowed_ips'])) : null;
            $allowedDomains = !empty($settings['allowed_domains']) ? array_map('trim', explode(',', $settings['allowed_domains'])) : null;

            $pivot = ApiKeyService::updateOrCreate(
                ['api_key_id' => $apiKey->id, 'service_id' => $serviceId],
                [
                    'is_active' => true,
                    'allowed_ips' => $allowedIps,
                    'allowed_domains' => $allowedDomains,
                ]
            );

            // Rebuild Rate Limits
            $pivot->rateLimits()->delete();
            if (!empty($settings['rate_limits'])) {
                foreach ($settings['rate_limits'] as $rateLimit) {
                    if (!empty($rateLimit['max_requests']) && !empty($rateLimit['period'])) {
                        ApiRateLimit::create([
                            'api_key_service_id' => $pivot->id,
                            'max_requests' => (int) $rateLimit['max_requests'],
                            'period' => $rateLimit['period'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('api-keys.index')->with('success', 'API Key updated successfully.');
    }

    public function destroy(ApiKey $apiKey)
    {
        $apiKey->delete();
        return redirect()->route('api-keys.index')->with('success', 'API Key revoked and deleted.');
    }
}
