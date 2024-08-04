<?php

namespace App\Console\Commands;

use App\Http\Controllers\SubscriptionsController;
use Illuminate\Console\Command;

class UpdateTestCustomerSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-test-customer-subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptionController = new SubscriptionsController();
        $subscriptionController->updateTestSubscription();
    }
}
