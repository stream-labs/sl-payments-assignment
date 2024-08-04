<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws ApiErrorException
     */
    public function run(): void
    {
        // Clear table before re-seeding it
        DB::table('customers')->truncate();

        $customers = $this->getStripeCustomers();

        foreach ($customers as $customer) {
            DB::table('customers')->insert([
                'name' => $customer['name'],
                'email' => $customer['email'],
                'payment_service' => 'Stripe',
                'payment_service_customer_id' => $customer['id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * @throws ApiErrorException
     */
    private function getStripeCustomers(): array
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
        // Note: This does not retrieve deleted customers.
        $customers = $stripe->customers->all(['test_clock' => env('STRIPE_TEST_CLOCK')]);

        return $customers->data ?? [];
    }
}
