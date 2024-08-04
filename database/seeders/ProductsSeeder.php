<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws ApiErrorException
     */
    public function run(): void
    {
        // Clear table before re-seeding it
        DB::table('products')->truncate();

        $products = $this->getStripeProducts();

        foreach ($products as $product) {
            DB::table('products')->insert([
                'name' => $product['name'],
                'payment_service' => 'Stripe',
                'payment_service_product_id' => $product['id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * @throws ApiErrorException
     */
    private function getStripeProducts(): array {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
        $products = $stripe->products->all();

        return $products->data ?? [];
    }
}
