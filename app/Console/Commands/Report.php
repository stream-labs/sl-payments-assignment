<?php

namespace App\Console\Commands;

use App\Reports\SubscriptionsReport;
use Illuminate\Console\Command;

class Report extends Command
{
    protected $signature = 'report:stripe';
    protected $description = 'Generate and display Stripe subscription reports';

    public function handle()
    {
        $this->info('Generating report...');
        $reports = [];
        $report = SubscriptionsReport::make();

    
        $productReports = $report->run();

        foreach ($productReports as $productReport) {
            $this->info($productReport->product->name . ' Report');
            $this->table(
                $productReport->headers, 
                array_merge($productReport->rows, [$productReport->footer])
            );
            $this->newLine();
        }

        $this->info('Successfully generated ' . count($reports) . ' reports.');
    }
}
