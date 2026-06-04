<?php

namespace App\Enums\Payment;

enum PaymentGateway: string
{
    case DUMMY = 'dummy';
    case RAZORPAY = 'razorpay';
    case CASHFREE = 'cashfree';
}
