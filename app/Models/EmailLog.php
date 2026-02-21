<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailLog extends Model
{
    protected $fillable = [
        'token',
        'type',
        'to',
        'cc',
        'bcc',
        'reply_to',
        'subject',
        'body',
        'attachments',
        'status',
        'error_message'
    ];

    protected $casts = [
        'attachments' => 'array'
    ];

    public $incrementing = false; // karena bukan auto-increment 
    protected $keyType = 'string'; // karena UUID string

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
