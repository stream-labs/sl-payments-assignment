<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class CouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws ApiErrorException
     */
    public function run(): void
    {
        // Clear table before re-seeding it
        DB::table('coupons')->truncate();

        $coupons = $this->getStripeCoupons();

        foreach ($coupons as $coupon) {
            DB::table('coupons')->insert([
                'name' => $coupon['name'],
                'payment_service' => 'Stripe',
                'payment_service_coupon_id' => $coupon['id'],
            ]);
        }
    }

    /**
     * @throws ApiErrorException
     */
    private function getStripeCoupons(): array
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
        $coupons = $stripe->coupons->all();

        return $coupons->data ?? [];
    }
}
