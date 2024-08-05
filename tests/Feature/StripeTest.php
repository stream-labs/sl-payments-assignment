<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Reports\ProductReport;
use Tests\TestCase;
use App\Reports\SubscriptionsReport;

class StripeTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $expected = json_decode(file_get_contents(base_path('tests/fixtures/report.json')), true);
        $report = SubscriptionsReport::make()->toArray();
        $this->assertEquals($expected, $report);
    }
}
