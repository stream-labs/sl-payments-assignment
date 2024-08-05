<?php

namespace App\Actions;

use Stripe\Customer;

class DeleteCustomer
{
    public function run(Customer $customer): void
    {
        $customer->delete();
    }
}