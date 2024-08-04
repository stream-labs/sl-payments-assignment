<?php

namespace Database\Seeders;

use App\Customers;
use App\Products;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class InvoicesSeeder extends Seeder
{
    private Collection $products;
    private Collection $customers;

    /**
     * Run the database seeds.
     *
     * @throws ApiErrorException
     */
    public function run(): void
    {
        // Clear table before re-seeding it
        DB::table('invoices')->truncate();

        $this->products = Products::all();
        $this->customers = Customers::all();
        $invoices = $this->getStripeInvoices();

        foreach ($invoices as $invoice) {
            DB::table('invoices')->insert([
                'customer_email' => $invoice['customer_email'],
                'product_name' => $invoice['product_name'],
                'total' => $invoice['total'],
                'currency' => $invoice['currency'],
                'invoice_date' => $invoice['invoice_date'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Note: Making a call to $stripe->invoices->all() was returning no results & the GET Invoices endpoint does not
     * support passing test_clock as a parameter. The workaround is to pass the customer ID as a parameter, which
     * requires one request per customer & two levels of loops.
     *
     * @throws ApiErrorException
     */
    private function getStripeInvoices(): array
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
        $formattedInvoices = [];

        // Loop through to get all invoices for each customer. Doing this because $strip->invoice->all() returns []
        foreach ($this->customers as $customer) {
            $invoices = $stripe->invoices->all(['customer' => $customer->payment_service_customer_id]);

            foreach ($invoices->data as $invoice) {
                $productId = $invoice->lines->data[0]->plan->product;

                $formattedInvoice = [
                    'customer_email' => $invoice->customer_email,
                    // Only grabbing the first product for proof of concept. Would use a lookup table for multiple products
                    'product_name' => $this->getProductName($productId),
                    'total' => $invoice->total,
                    'currency' => $invoice->currency,
                    'invoice_date' => $this->convertTimestampToHumanReadable($invoice->created),
                ];

                $formattedInvoices[] = $formattedInvoice;
            }
        }

        return $formattedInvoices ?? [];
    }

    /**
     * Converts the dates in timestamp format to Y-m-d H:i:s
     *
     * @param $timestamp
     * @return string
     */
    private function convertTimestampToHumanReadable($timestamp): string
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * Gets the product name for the Stripe product ID
     *
     * @param $productId
     * @return string
     */
    private function getProductName($productId): string
    {
        foreach ($this->products as $product) {
            if ($product->payment_service_product_id == $productId) {
                return $product->name;
            }
        }

        return 'N/A';
    }
}
