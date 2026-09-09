<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKeyService extends Model
{
    protected $fillable = [
        'api_key_id',
        'service_id',
        'is_active',
        'allowed_ips',
        'allowed_domains',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allowed_ips' => 'array',
        'allowed_domains' => 'array',
    ];

    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function rateLimits()
    {
        return $this->hasMany(ApiRateLimit::class, 'api_key_service_id');
    }
}
