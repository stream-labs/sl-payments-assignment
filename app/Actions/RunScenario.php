<?php

namespace App\Actions;

use App\Actions\CreateCustomer;
use App\Actions\CreateSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RunScenario extends Action
{
    public $couponId = 'g5EhlhNS';

    public $pricePremiumId = 'price_1PkBhjFtwQ4FxG6goAvTILnH';

    public $priceBasicId = 'price_1PkBhjFtwQ4FxG6gzftQy8xT';

    /**
     * Create a new scenario
     *
     * @param CreateCustomer $createCustomerAction
     * @param CreateSubscription $createSubscriptionAction
     */
    public function __construct(
        protected CreateCustomer $createCustomerAction,
        protected CreateSubscription $createSubscriptionAction,
    ) {
    }

    /**
     * Run the action
     *
     */
    public function run()
    {
        $customer = [
            'name' => 'James Bond',
            'email' => 'james.bond@mi6.uk',
            'phone' => '+447700900000',
        ];

        $subscription = [
            'priceId' =>  $this->priceBasicId,
            'couponId' => $this->couponId,
            'trialPeriodDays' => 30,
            'currency' => 'gbp',
        ];

        return $this->setUp($customer, $subscription);
    }

    protected function currentClockTime(): Carbon
    {
        $clockId = config('services.stripe.test_clock');
        $currentTime = stripe()->testHelpers->testClocks->retrieve($clockId)->frozen_time;
        return Carbon::createFromTimestamp($currentTime);
    }

    /**
     * Run the action
     *
     * @param array<string, mixed> $customer
     * @param array<string, mixed> $subscription
     * @return void
     */
    protected function setUp(array $customer = [], array $subscription = [])
    {
        $customer = $this->createCustomerAction->run($customer);
        $subscription['customerId'] = $customer->id;

        $subscription = $this->createSubscriptionAction->run($subscription);

        $subscriptionStartDate = Carbon::createFromTimestamp($subscription->created);


        /**
         * Advance the clock to the 5th month in 1 month increments.
         * This is because the test clocks only allow advance of time by 
         * a maximum of 2 months at a time.
         */

         $targetMonth = Carbon::createFromTimestamp($subscription->created)->addMonths(5);
        
         while($this->currentClockTime()->month !== $targetMonth->month) {
            sleep(2);
            stripe()->testHelpers->testClocks->advance(
                config('services.stripe.test_clock'),
                ['frozen_time' => $subscriptionStartDate->addMonths(1)->timestamp]
            );
         }

        /**
         * Perform mid-cycle upgrade on the 15th day of the 5th month
         */
        stripe()->testHelpers->testClocks->advance(
            config('services.stripe.test_clock'),
            ['frozen_time' => $this->currentClockTime()->addDays(14)->timestamp]
        );
        /**
         * Perform the mid-cycle upgrade with proration
         */
        stripe()->subscriptions->update($subscription->id, [
            'proration_behavior' => 'create_prorations',
            'items' => [
                [
                    'id' => $subscription->items->data[0]->id,
                    'price' => $this->pricePremiumId,
                ],
            ],
        ]);

        /**
         * Advance the clock to the end of the 5th month
         */
        stripe()->testHelpers->testClocks->advance(
            config('services.stripe.test_clock'),
            ['frozen_time' => $subscriptionStartDate->addMonths(5)->timestamp]
        );

        /**
         * In a real scenario this would be stored in the database
         * but I've been at this a while and ImTiredBoss.jpg
         */
        Cache::put('scenario_customer_id', $customer->id);
    }

    /**
     * Tear down the scenario
     *
     * @return void
     */
    public function teardown(): void
    {
        if($customerId = Cache::get('scenario_customer_id')) {
            stripe()->customers->delete($customerId);
        }

        Cache::forget('scenario_customer_id');
    }
}
