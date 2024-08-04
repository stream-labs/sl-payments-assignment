<?php

namespace Tests\Feature\App;

use App\Customers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomersTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_customer_by_email(): void
    {
        // Create customers
        $customers = Customers::factory()->count(3)->create();
        $customerEmail = $customers[0]->email;
        $secondCustomerEmail = $customers[1]->email;
        $thirdCustomerEmail = $customers[2]->email;

        // Find customer
        $foundCustomer = Customers::findCustomerByEmail($customerEmail);

        // Go Right - Verify that we found the correct customer
        $this->assertEquals($customerEmail, $foundCustomer->email);

        // Go Wrong - Confirm that other customers created do not match the found email
        $this->assertNotEquals($secondCustomerEmail, $foundCustomer->email);
        $this->assertNotEquals($thirdCustomerEmail, $foundCustomer->email);

        $this->assertTrue(true);
    }

    public function test_do_not_find_customer_by_email(): void
    {
        // Create customers
        Customers::factory()->count(1)->create();
        $randomCustomer = 'made-up-customer';

        // Find customer
        $foundCustomer = Customers::findCustomerByEmail($randomCustomer);

        // Go Right - Verify that foundCustomer is null
        $this->assertNull($foundCustomer);
    }
}
