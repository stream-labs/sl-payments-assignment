<?php

namespace Tests\Feature\App;

use App\Subscriptions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_find_subscription_by_payment_service_customer_id(): void
    {
        // Create subscriptions
        $subscriptions = Subscriptions::factory()->count(3)->create();
        $subscriptionCustomerId = $subscriptions[0]->payment_service_customer_id;
        $secondSubscriptionCustomerId = $subscriptions[1]->payment_service_customer_id;
        $thirdSubscriptionCustomerId = $subscriptions[2]->payment_service_customer_id;

        // Find subscription
        $foundSubscription = Subscriptions::findSubscriptionByPaymentServiceCustomerId($subscriptionCustomerId);

        // Go Right - Verify that we found the correct subscription
        $this->assertEquals($subscriptionCustomerId, $foundSubscription->payment_service_customer_id);

        // Go Wrong - Confirm that other subscriptions created do not match the found email
        $this->assertNotNull($secondSubscriptionCustomerId, $foundSubscription->payment_service_customer_id);
        $this->assertNotNull($thirdSubscriptionCustomerId, $foundSubscription->payment_service_customer_id);
    }

    public function test_do_not_find_customer_by_email(): void
    {
        // Create customers
        Subscriptions::factory()->count(1)->create();
        $randomSubscriptionCustomerId = 'cus_made-up-customer-id';

        // Find customer
        $foundSubscription = Subscriptions::findSubscriptionByPaymentServiceCustomerId($randomSubscriptionCustomerId);

        // Go Right - Verify that foundCustomer is null
        $this->assertNull($foundSubscription);
    }
}
