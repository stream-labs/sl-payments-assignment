<?php

namespace App\Http\Controllers;

use App\Customers;
use App\Prices;
use App\Subscriptions;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Stripe\Subscription;

class SubscriptionsController
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
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

    /**
     * @throws ApiErrorException
     */
    public function updateTestSubscription(): Subscription
    {
        // Find customer where email is test.testington@test.co.uk & get their customer.payment_service_provider_id
        $testCustomer = Customers::findCustomerByEmail('test.testington@test.co.uk');

        // Find their Subscription by subscription.payment_service_customer_id & get subscription.payment_service_subscription_id
        $testSubscription = Subscriptions::findSubscriptionByPaymentServiceCustomerId($testCustomer->payment_service_customer_id);

        // Make a request to Stripe to get their subscription record. I need this, so I can get the subscription_item_id
        $stripeSubscription = $this->lookupStripeSubscription($testSubscription->payment_service_subscription_id);
        $stripeItemId = $stripeSubscription['items']->data[0]['id'];

        // Get the price.payment_service_price_id where the name is "monthly_crossclip_premium"
        $upgradePrice = Prices::findPriceByName('monthly_crossclip_premium');
        $stripeUpgradePriceId = $upgradePrice->payment_service_price_id;

        // Make a request to update their subscription
        try {
            $updatedSubscription = $this->stripe->subscriptions->update(
                $testSubscription->payment_service_subscription_id,
                [
                    'proration_behavior' => 'create_prorations',
                    // Try changing this to updating the item instead
                    'items' => [
                        [
                            'id' => $stripeItemId,
                            'deleted' => true,
                        ],
                        ['price' => $stripeUpgradePriceId],
                    ],
                ]
            );
        } catch (ApiErrorException $e) {
            var_dump($e->getMessage());
        }

        return $updatedSubscription;
    }

    public function lookupStripeSubscription(string $paymentServiceSubscriptionId): Subscription
    {
        return $this->stripe->subscriptions->retrieve($paymentServiceSubscriptionId);
    }
}
