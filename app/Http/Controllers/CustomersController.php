<?php

namespace App\Http\Controllers;

use App\Customers;
use App\Http\Requests\StoreCustomersRequest;
use App\Http\Requests\UpdateCustomersRequest;
use Carbon\Carbon;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentMethod;
use Stripe\StripeClient;

class CustomersController
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
    }

    /**
     * @throws ApiErrorException
     */
    public function createTestStripeCustomer(): Customers
    {
        $stripeCustomer = $this->stripe->customers->create([
            'test_clock' => env('STRIPE_TEST_CLOCK'),
            'name' => 'Test Tester',
            'email' => 'test.tester@test.co.uk',
        ]);

        $customerRecord = new Customers([
            'name' => $stripeCustomer->name,
            'email' => $stripeCustomer->email,
            'payment_service' => 'Stripe',
            'payment_service_customer_id' => $stripeCustomer->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $customerRecord->save();

        $paymentMethodRecord = $this->createPaymentMethod($customerRecord);
        $this->attachPaymentMethod($customerRecord, $paymentMethodRecord);

        return $customerRecord;
    }

    /**
     * Sending raw credit card numbers is not safe & would need to be enabled by Stripe's support on
     * a developer account. Since this is coming up as an issue, then I will create the customer & subscription
     * through the fixture, but I wanted to demonstrate that I can create them through Stripe's API.
     *
     * @throws ApiErrorException
     */
    public function createPaymentMethod(Customers $customer): \Stripe\PaymentMethod
    {
        $nextYear = Carbon::today()->addYear()->format('Y');

        // For a real transaction, I would always create the payment as a token & would not send their CC as a post.
        return $this->stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 12,
                'exp_year' => $nextYear,
                'cvc' => '123',
            ]
        ]);
    }

    /**
     * Attaches the payment method to the customer
     *
     * @throws ApiErrorException
     */
    public function attachPaymentMethod(Customers $customer, PaymentMethod $paymentMethod): PaymentMethod
    {
        return $this->stripe->paymentMethods->attach($paymentMethod->id, ['customer' => $customer->payment_service_customer_id]);
    }
}
