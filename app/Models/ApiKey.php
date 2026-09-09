<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class ApiKey extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'key_hash',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apiKeyServices()
    {
        return $this->hasMany(ApiKeyService::class);
    }

    public function apiLogs()
    {
        return $this->hasMany(ApiLog::class);
    }
}
