<?php 

namespace App\Actions;

use Illuminate\Validation\ValidationException;
use Stripe\Subscription;

class CreateSubscription extends Action
{
    /**
     * Validation rules
     * 
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'customerId' => 'required',
            'priceId' => 'required',
            'couponId' => 'nullable',
            'trialPeriodDays' => 'nullable',
            'currency' => 'nullable',
        ];
    }
    /**
     * Create a subscription
     * 
     * @throws ValidationException
     * @param array<string, mixed> $data
     * @return Subscription
     */
    public function run(array $data): Subscription
    {
        $data = $this->validate($data);

        return stripe()->subscriptions->create([
            'customer' => $data['customerId'],
            'items' => [
                ['price' => $data['priceId']],
            ],
            'coupon' => data_get($data, 'couponId'),
            'trial_period_days' => data_get($data, 'trialPeriodDays'),
            'currency' => data_get($data, 'currency', 'usd'),
        ]);
    }
}