<?php

namespace App\Reports;

use Illuminate\Support\Carbon;
use App\Data\TableData;
use Stripe\Product;
use Stripe\Subscription;
use App\Util\ExchangeRate;

class ProductReport
{
    /**
     * The product to generate a report for.
     * @var Product
     */
    public Product $product;

    /**
     * The subscriptions to generate a report for.
     * @var array
     */
    public array $subscriptions;

    /**
     * The start date to generate a report for.
     * @var Carbon
     */
    public Carbon $startDate;

    /**
     * The end date to generate a report for.
     * @var Carbon
     */
    public Carbon $endDate;

    /**
     * The headers for the report.
     * @var array
     */
    public array $headers = [];

    /**
     * The rows for the report.
     * @var array
     */
    public array $rows = [];

    /**
     * The footer for the report.
     * @var array
     */
    public array $footer = [];

    /**
     * The totals for the report.
     * @var array
     */
    public array $totals = [];

    /**
     * Create a new product report instance.
     *
     * @param Product $product
     * @param array $subscriptions
     * @param Carbon $startDate
     * @param Carbon $endDate
     */
    public function __construct(Product $product, array $subscriptions, Carbon $startDate, Carbon $endDate)
    {
        $this->product = $product;
        $this->subscriptions = $subscriptions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Generate the report.
     *
     * @return void
     */
    public function generate(): void
    {
        $this->generateHeaders();
        $this->generateRows();
        $this->generateFooter();
    }

    /**
     * Generate the headers for the report.
     *
     * @return void
     */
    protected function generateHeaders(): void
    {
        $months = [];
        $period = \Carbon\CarbonPeriod::create($this->startDate, '1 month', $this->endDate);
        foreach ($period as $month) {
            $months[] = $month->endOfMonth()->format('M d');
        }

        $this->headers = array_merge(['Customer Email', 'Product Name'], $months, ['Life Time Value']);
    }

    protected function generateRows(): void
    {
        $this->rows = [];
   
        $totalColumns = count($this->headers) - 2;
        // initialize totals array with zeros for each month and lifetime value
        $totals = array_fill(0, $totalColumns, 0);

        foreach ($this->subscriptions as $subscription) {
            $row = $this->generateRowForSubscription($subscription);
            $this->rows[] = $row;

            foreach ($row as $index => $value) {
                if ($index < 2) continue; // Skip customer email and product name
                $numericValue = (float) str_replace(['$', ','], '', $value);
                $totals[$index - 2] += $numericValue;
            }
        }

        $this->totals = $totals;
    }

    /**
     * Calculate the pro rata amount for a subscription and month.
     *
     * @param Subscription $subscription
     * @param Carbon $monthDate
     * @return float
     */
    protected function calculateProRataAmount(Subscription $subscription, Carbon $monthDate)
    {
        $price = $subscription->items->data[0]->price;

        $daysInMonth = $monthDate->daysInMonth;

        $priceInUsd = ExchangeRate::toUsd($price->currency, $price->unit_amount);

        return $priceInUsd / $daysInMonth;
    }

    /**
     * Generate a row for a subscription.
     *
     * @param Subscription $subscription
     * @return array
     */
    protected function generateRowForSubscription(Subscription $subscription): array
    {
        $customer = $subscription->customer;
        $row = [
            $customer->email,
            $this->product->name,
        ];

        $lifetimeValue = 0;

        $startDate = Carbon::createFromTimestamp($subscription->start_date);

        $endDate = $subscription->cancel_at
            ? Carbon::createFromTimestamp($subscription->cancel_at)
            : $this->endDate;

        $period = \Carbon\CarbonPeriod::create($this->startDate, '1 month', $this->endDate);

        foreach ($period as $monthDate) {

            $amount = 0;

            if ($subscription->status === 'active' &&
                $monthDate->gte($startDate) &&
                $monthDate->lt($endDate)) {
                
                // Check for proration or plan changes
                $relevantInvoice = $this->findRelevantInvoice($subscription, $monthDate);
                
                if ($relevantInvoice) {

                    $amount = (float) str_replace(['$', ','], '', $relevantInvoice->amount_paid);
                    $currency = $relevantInvoice->currency;
                    $amount = ExchangeRate::toUsd($currency, $amount) / 100;

                } else {
                    $amount = $this->calculateProRataAmount($subscription, $monthDate);
                }
            }

            $row[] = '$' . number_format($amount, 2);
            $lifetimeValue += $amount;
        }

        $row[] = '$' . number_format($lifetimeValue, 2);

        return $row;
    
    }

    /**
     * Find the relevant invoice for a subscription and month.
     *
     * @param Subscription $subscription
     * @param Carbon $monthDate
     * @return \Stripe\Invoice|null
     */
    protected function findRelevantInvoice(Subscription $subscription, Carbon $monthDate): ?\Stripe\Invoice
    {
        $invoices = stripe()->invoices->all([
            'subscription' => $subscription->id,
            'limit' => 100, // Adjust this value based on your needs
        ]);

        foreach ($invoices->data as $invoice) {
            $invoiceDate = Carbon::createFromTimestamp($invoice->created);
            if ($invoiceDate->isSameMonth($monthDate)) {
                return $invoice;
            }
        }

        return null;
    }


    protected function generateFooter(): void
    {
        $this->footer = array_merge(
            ['Totals', ''],
            array_map(fn ($total) => '$' . number_format($total, 2), $this->totals)
        );
    }

    public function toTableData(): TableData
    {
        return new TableData(
            rows: $this->rows,
            headers: $this->headers,
            footer: $this->footer
        );
    }

    public function toArray(): array
    {
        return [
            'headers' => $this->headers,
            'rows' => $this->rows,
            'footer' => $this->footer
        ];
    }
}
