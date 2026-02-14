<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
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
}
