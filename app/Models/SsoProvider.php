<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SsoProvider extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'is_active',
        'can_register',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'can_register' => 'boolean',
    ];
}
