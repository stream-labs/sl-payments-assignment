<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class PricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws ApiErrorException
     */
    public function run(): void
    {
        // Clear table before re-seeding it
        DB::table('prices')->truncate();

        $prices = $this->getStripePrices();

        foreach ($prices as $price) {
            DB::table('prices')->insert([
               'name' => $price['lookup_key'] ?? 'N/A',
               'payment_service' => 'Stripe',
               'payment_service_price_id' => $price['id'],
            ]);
        }
    }

    /**
     * @throws ApiErrorException
     */
    private function getStripePrices(): array
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
        $prices = $stripe->prices->all();

        return $prices->data ?? [];
    }
}
