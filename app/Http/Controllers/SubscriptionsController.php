<?php

namespace App\Http\Controllers;

use App\Customers;
use App\Http\Requests\StoreSubscriptionsRequest;
use App\Http\Requests\UpdateSubscriptionsRequest;
use App\Prices;
use App\Subscriptions;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class SubscriptionsController
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscriptions $subscriptions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscriptions $subscriptions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionsRequest $request, Subscriptions $subscriptions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscriptions $subscriptions)
    {
        //
    }

    /**
     * @throws ApiErrorException
     */
    public function createTestStripeSubscription(Customers $customer): Subscriptions
    {
        $price = Prices::where('name', '=', 'monthly_crossclip_basic')->first();
        // Create a test customer with a 30-day trial
        $stripeSubscription = $this->stripe->subscriptions->create([
            'customer' => $customer->payment_service_customer_id,
            'items' => [['price' => $price->payment_service_price_id]],
            'trial_end' => now()->addDays(30)->timestamp,
        ]);

        $subscriptionRecord = new Subscriptions();
        $subscriptionRecord->payment_service = 'Stripe';
        $subscriptionRecord->payment_service_subscription_id = $stripeSubscription->id;
        $subscriptionRecord->payment_service_customer_id = $stripeSubscription->customer;
        $subscriptionRecord->save();

        return $subscriptionRecord;
    }
}
