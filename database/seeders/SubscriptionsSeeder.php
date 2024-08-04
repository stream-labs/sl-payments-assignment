<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class SubscriptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws ApiErrorException
     */
    public function run(): void
    {
        // Clear table before re-seeding it
        DB::table('subscriptions')->truncate();

        $subscriptions = $this->getStripeSubscriptions();

        foreach ($subscriptions as $subscription) {
            DB::table('subscriptions')->insert([
               'payment_service' => 'Stripe',
               'payment_service_subscription_id' => $subscription->id,
               'payment_service_customer_id' => $subscription->customer,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * @throws ApiErrorException
     */
    private function getStripeSubscriptions(): array
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
        $subscriptions = $stripe->subscriptions->all(['test_clock' => env('STRIPE_TEST_CLOCK'), 'status' => 'all']);

        return $subscriptions->data ?? [];
    }
}
