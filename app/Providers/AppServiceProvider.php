<?php

namespace App\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function (Application $app): StripeClient {

            /**
             * @var array<string, string|null> $config
             */
            $config = [
                'api_key' => config('services.stripe.secret'),
            ];

            // allow api_base to be overridden for testing
            if(config('services.stripe.api_base')) {
                $config['api_base'] = config('services.stripe.api_base');
            }

            return new StripeClient($config);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}