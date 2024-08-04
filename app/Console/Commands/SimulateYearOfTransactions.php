<?php

namespace App\Console\Commands;

use App\Http\Controllers\SubscriptionsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class SimulateYearOfTransactions extends Command
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
    protected $signature = 'app:simulate-year-of-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws ApiErrorException
     */
    public function handle(): void
    {
        // The test clock time is using my local timezone MST instead of UTC, which causes problems where the
        // timestamp can be ahead
        $currentDateTime = Carbon::yesterday()->startOfDay();
        echo $currentDateTime . "/n";

        for ($i = 1; $i <= 12; $i++) {
            if ($i == 6) {
                // Update the test user's subscription before advancing to the next month
                $subscriptionController = new SubscriptionsController();
                $subscriptionController->updateTestSubscription();
            }

            // Check for 5th month
            if ($i == 5) {
                // Advance to the 15th of that month
                $fifteenth = $currentDateTime->addMonths($i)->startOfMonth()->setDay(15)->timestamp;
                $this->advanceTime($fifteenth);
            } else {
                // Advance 1-month at a time
                $nextMonth = $currentDateTime->addMonths($i)->timestamp;
                $this->advanceTime($nextMonth);
                sleep(env('STRIPE_ADVANCE_CLOCK_SLEEP_TIME'));
            }
        }

       $subscriptionController = new SubscriptionsController();
       $subscriptionController->updateTestSubscription();
    }

    /**
     * Through manual testing, I have found that even adding 2-minutes of time between requests, you will still
     * eventually hit the race condition. I highly recommend advancing the test clock through Stripe's dashboard
     *
     * @param int $timestamp
     * @return void
     * @throws ApiErrorException
     */
    private function advanceTime(int $timestamp): void
    {
        try {
            $advanceTime = $this->stripe->testHelpers->testClocks->advance(env('STRIPE_TEST_CLOCK'), ['frozen_time' => $timestamp]);
            var_dump($advanceTime);
        } catch (ApiErrorException $exception) {
            var_dump($exception->getMessage());
            // Wait to allow the previous request to finish before retrying
            sleep(env('STRIPE_ADVANCE_CLOCK_SLEEP_TIME'));
            $advanceTime = $this->stripe->testHelpers->testClocks->advance(env('STRIPE_TEST_CLOCK'), ['frozen_time' => $timestamp]);
            var_dump($advanceTime);
            return;
        }
    }
}
