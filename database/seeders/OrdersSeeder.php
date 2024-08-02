<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws ApiErrorException
     */
    public function run(): void
    {
        // Clear table before re-seeding it
        DB::table('orders')->truncate();

        $orders = $this->getStripeOrders();

        foreach ($orders as $order) {
            DB::table('orders')->insert([
                'total' => $this->getTotalInDollars($order['amount']),
                'transaction_date' => $this->convertTimestampToHumanReadable($order['order_date']),
                'description' => $order['description'],
                'payment_service' => 'Stripe',
                'payment_service_order_id' => $order['id'],
                'payment_service_customer_id' => $order['customer'],
            ]);
        }
    }

    /**
     * @throws ApiErrorException
     */
    private function getStripeOrders(): array {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
        $orders = $stripe->paymentIntents->all();

        return $orders->data ?? [];
    }

    private function getTotalInDollars($total): float
    {
        return number_format(($total /100), 2, '.', ' ');
    }

    private function convertTimestampToHumanReadable($timestamp): string
    {
        return date('Y-m-d H:i:s', $timestamp);
    }
}
