## Notes About Stripe Test Clocks
I have been working on a command that simulates a year's worth of transactions on Stripe, but I ran into the following issues:

1. When you advance a Stripe test clock it can take several minutes to finish processing.
2. You can only advance the test clock as much as 2-months at a time, but the requests must be run synchronously & are prone to race conditions.
   1. You will eventually hit a race condition where the timestamp you provide exceeds their maximum allowable value:
   2. `The frozen time of this clock is 1732320000 (2024-11-23 00:00:00 UTC). You can only advance it up to 1737590400 (2025-01-23 00:00:00 UTC). You can only advance a test clock up to two intervals from the current frozen time at a time, based on the shortest subscription interval in the test clock. For example, if a test clock has a monthly and yearly subscription, you may only advance up to two months at a time. If the test clock has no subscriptions or subscription schedules, you can advance up to 2 years from the current frozen time.`
3. The issue is exacerbated by Stripe returning a 200 early & allowing the simulation to continue running on their servers.
4. Because of the early 200 response, I had to try some creative but not ideal solutions:
   1. Try simulating smaller spans of time & breaking this into more requests.
   2. Add a `sleep()` timer between API requests to allow previous requests to finish.
   3. Even when I simulated only a week per request & set the sleep timer to 60 seconds, it would still hit a race condition after a few request (e.g. 9).
5. To safely guarantee that the script would run to completion without hitting the race condition, I would need to add a sleep time of 2-3 minutes or set up a queueing & retry system.
   1. Running the automated script as it stands would take 2 to 4.5 hours run.
6. With that in mind, the faster solution is to run the simulations manually, wait for them to complete & then trigger the next simulation.
7. Then after advancing to the 15th of the 5th month, run a command to update the subscription of the user I created (Test Testington) & then continue on with running the simulation through the year.
8. Given that the limitation is on Stripe's side for the simulations taking up to a few minutes to run, this is a reasonable workaround.
9. I will still include an automated way to run this, but it is not the recommended way to run this.

## Setup

1. Generate an application key - `vendor/bin/sail artisan key:generate`
2. Install the Stripe CLI - `brew install stripe/stripe-cli/stripe`
3. Login to Stripe - `stripe login`

## Running Code
1. Create the Stripe test clock and add its value the .env value `STRIPE_TEST_CLOCK`
2. Run DB migrations - `vendor/bin/sail artisan migrate:refresh`
3. Run fixtures - `stripe fixtures fixtures/seed.json`
4. Seed DB with the records created from Stripe - `vendor/bin/sail artisan db:seed`
   1. This script pulls the data we created on Stripe directly from their APIs and stores it in the DB
   2. This way we have access to the internal Stripe IDs for resources like customers, products, invoices, etc
6. Warning: This command will take several hours to run & it is not the recommended way to proceed
   1. `vendor/bin/sail artisan app:simulate-year-of-transactions`
      1. This will make 52-requests to Stripe, each simulating 1-week of transactions
      2. There is a configurable sleep variable in the env that should be set to at least 120-seconds
      3. It will send out a request to update the user I added (Test Testington's) subscription for the 15th day of the 5th month
      4. Upon completion run `vendor/bin/sail artisan db:seed` to re-seed the DB with all transaction data from Stripe.
8. To work around the performance issues, I recommend running the Test Clock simulations manually.
   1. Simulate ahead 2-months (2-months total)
   2. Simulate ahead 2-months more (4-months total)
   3. Simulate ahead to the 15th day of the 5th month (5.5-months total)
   4. Run this command to update the subscription for the customer I added, `vendor/bin/sail artisan app:update-test-customer-subscription`
   5. Simulate ahead 2-months (7.5-months total)
   6. Simulate ahead 2-months (9.5-months total)
   7. Simulate ahead 2-months (11.5-months total)
   8. Simulate ahead 0.5 months to 1-year from the original date (1-year total)
9. Run `vendor/bin/sail artisan db:seed` to re-seed the DB with all transaction data
10. View the results with a table per product at http://localhost/