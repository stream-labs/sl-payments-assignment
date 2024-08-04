<?php

namespace App\Console\Commands;


use Carbon\Carbon;
use Illuminate\Console\Command;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class CreateExampleCustomer extends Command
{
    private StripeClient $stripe;

    public function __construct()
    {
        parent::__construct();
        $this->stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-example-customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // The test clock time is using my local timezone MST instead of UTC, which causes problems where the
        // timestamp can be ahead
        $currentDateTime = Carbon::yesterday()->startOfDay();
        echo $currentDateTime . "/n";

        // Note: I was going to originally create a customer and subscription, but opted to use the fixture method,
        // so that I don't send test payment info to the payment method API
//        $customerController = new CustomersController();
//        $newCustomer = $customerController->createTestStripeCustomer();

//        $subscriptionController = new SubscriptionsController();
//        $newSubscription = $subscriptionController->createTestStripeSubscription($newCustomer);

        // There is a race condition here, where the next request is sent before the previous one has finished advancing.
        // Advance 2-months (2 total)
        $twoMonthsFromNow = $currentDateTime->addMonths(2)->timestamp;
//        echo $twoMonthsFromNow . "<br />";
        $this->advanceTime($twoMonthsFromNow);
//        sleep(10);
//
//        // Advance 2-months (4 total)
//        $fourMonthsFromNow = $currentDateTime->addMonths(4)->timestamp;
//        $this->advanceTime($fourMonthsFromNow);
//        sleep(10);
//
//        // Advance 2-months (6 total)
//        $sixMonthsFromNow = $currentDateTime->addMonths(6)->timestamp;
//        $this->advanceTime($sixMonthsFromNow);
//        sleep(10);
//
//        // Advance 2-months (8 total)
//        $eightMonthsFromNow = $currentDateTime->addMonths(8)->timestamp;
//        $this->advanceTime($eightMonthsFromNow);
//        sleep(10);
//
//        // Advance 2-months (10 total)
//        $tenMonthsFromNow = $currentDateTime->addMonths(10)->timestamp;
//        $this->advanceTime($tenMonthsFromNow);
//        sleep(10);
//
//        // Advance 2-months (12 total)
//        $twelveMonthsFromNow = $currentDateTime->addMonths(11)->timestamp;
//        $this->advanceTime($twelveMonthsFromNow);
//        sleep(10);

        // Advance 1.5-months (5.5 total)

        // Upgrade Subscription

        // Advance 2-months (7.5 total)

        // Advance 2-months (9.5 total)

        // Advance 2-months (11.5 total)

        // Advance to 1-year from the current day (12 total)

        // Re-run DB

//        var_dump($newCustomer);
//        $test = Customers::first();
//        var_dump($test);
//        $customer = Customers:;
//        var_dump($customer);x
//        $customer = $this->createStripeTestCustomer();
//        $subscription = $this->createStipeTestSubscription($customer);

        // Example advancing 2 months on the CLU
        // stripe test_helpers test_clocks advance clock_1Pjpm6DIyydFQzXc8tcFJ1IU --frozen-time 1727991961
    }

    private function advanceTime(int $timestamp): void
    {
        try {
            $this->stripe->testHelpers->testClocks->advance(env('STRIPE_TEST_CLOCK'), ['frozen_time' => $timestamp]);
        } catch (ApiErrorException $exception) {
            var_dump($exception->getMessage());
            return;
        }

    }
}
