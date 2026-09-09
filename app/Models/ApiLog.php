<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'api_key_id',
        'service_id',
        'endpoint',
        'method',
        'ip',
        'domain',
        'status_code',
        'message',
    ];

    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
