<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Subscriptions>
 */
class SubscriptionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_service' => 'Stripe',
            'payment_service_subscription_id' => 'sub_' . $this->faker->uuid(),
            'payment_service_customer_id' => 'cus_' . $this->faker->uuid(),
        ];
    }
}
