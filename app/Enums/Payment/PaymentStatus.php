<?php

namespace App\Enums\Payment;

enum PaymentStatus: string
{
    case INITIATED = 'initiated';
    case PENDING = 'pending';
    case AUTHORIZED = 'authorized';
    case CAPTURED = 'captured';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
}
