<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiRateLimit extends Model
{
    protected $fillable = [
        'api_key_service_id',
        'max_requests',
        'period',
    ];

    public function apiKeyService()
    {
        return $this->belongsTo(ApiKeyService::class, 'api_key_service_id');
    }
}
