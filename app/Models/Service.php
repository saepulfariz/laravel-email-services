<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Service extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function apiKeyServices()
    {
        return $this->hasMany(ApiKeyService::class);
    }

    public function apiLogs()
    {
        return $this->hasMany(ApiLog::class);
    }
}
