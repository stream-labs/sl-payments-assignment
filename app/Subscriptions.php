<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    use HasFactory;

    public static function findSubscriptionByPaymentServiceCustomerId(string $paymentServiceCustomerId)
    {
        return Subscriptions::where('payment_service_customer_id', $paymentServiceCustomerId)->first();
    }
}
