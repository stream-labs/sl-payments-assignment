<?php

namespace Tests\Feature\App;

use App\Prices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_find_price_by_name(): void
    {
        // Create prices
        $prices = Prices::factory()->count(3)->create();
        $priceName = $prices[0]->name;
        $secondPriceName = $prices[1]->name;
        $thirdPriceName = $prices[2]->name;

        // Find price
        $foundPrice = Prices::findPriceByName($priceName);

        // Go Right - Verify that we found the correct price
        $this->assertEquals($priceName, $foundPrice->name);

        // Go Wrong - Confirm that other prices created do not match the found email
        $this->assertNotEquals($secondPriceName, $foundPrice->name);
        $this->assertNotEquals($thirdPriceName, $foundPrice->name);
    }

    public function test_do_not_find_price_by_name(): void
    {
        // Create prices
        Prices::factory()->count(1)->create();
        $randomName = 'made-up-name';

        // Find price
        $foundPrice = Prices::findPriceByName($randomName);

        // Go Right - Verify that foundPrice is null
        $this->assertNull($foundPrice);
    }
}
