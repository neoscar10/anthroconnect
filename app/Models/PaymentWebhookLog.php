<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentWebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'event_type',
        'event_id',
        'signature',
        'payload',
        'processed',
        'processed_at',
        'failure_reason',
        'exception_trace',
        'retry_count',
        'transaction_reference',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean',
        'processed_at' => 'datetime',
        'retry_count' => 'integer',
    ];
}
