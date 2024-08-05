<?php

namespace App\Reports;


use Illuminate\Support\Carbon;

class SubscriptionsReport
{
    protected Carbon $startDate;
    protected Carbon $endDate;
    protected array $productReports = [];

    public function __construct(?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $this->startDate = $startDate ?? Carbon::now()->startOfMonth();
        $this->endDate = $endDate ?? Carbon::now()->addYear()->endOfMonth();
    }

    public function build(): self
    {
        $products = $this->fetchProducts();
        $subscriptions = $this->fetchSubscriptions();

        foreach ($products as $product) {
            $productSubscriptions = array_filter($subscriptions, function($sub) use ($product) {
                return $sub->items->data[0]->price->product === $product->id;
            });

            $report = new ProductReport($product, $productSubscriptions, $this->startDate, $this->endDate);
            $report->generate();
            $this->productReports[$product->id] = $report;
        }

        return $this;
    }

    public function fetchProducts(): array
    {
        return stripe()->products->all(['active' => true])->data;
    }

    public function fetchSubscriptions(): array
    {
        return stripe()->subscriptions->all([
            'test_clock' => config('services.stripe.test_clock'),
            'status' => 'all',
            'expand' => ['data.customer'],
        ])->data;
    }

    public static function make(?Carbon $startDate = null, ?Carbon $endDate = null): self
    {
        return app(static::class, [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function getReports(): array
    {
        return $this->productReports;
    }

    public function run(): array
    {
        $this->build();
        return $this->getReports();
    }

    public function toArray(): array
    {
        $this->build();
        return array_map(function($report) {
            return $report->toArray();
        }, $this->getReports());
    }
}