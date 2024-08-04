<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoicesRequest;
use App\Http\Requests\UpdateInvoicesRequest;
use App\Invoices;
use App\Products;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoicesController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoicesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoices $invoices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoices $invoices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoicesRequest $request, Invoices $invoices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoices $invoices)
    {
        //
    }

    /**
     * I experimented with different methods of flattening the data, so that each row contained the totals for every
     * month, but ultimately flattening the data in SQL worked the best. The downside is that it relies on
     * hardcoded dates, but that is ok for a proof of concept.
     *
     * @return array
     */
    public function getInvoiceDataByProduct(): array
    {
        $nextYear = Carbon::today()->addDays(364)->format('Y-m-d') . ' 00:00:00';
        // I tried different methods of flattening the data by month, but using case statements was the most reliable
        $flattenedInvoices = DB::table('invoices')
            ->select(
                DB::raw('customer_email, product_name, currency'),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2024-08' THEN total ELSE 0 END) AS `2024-08`"),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2024-09' THEN total ELSE 0 END) AS `2024-09`"),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2024-10' THEN total ELSE 0 END) AS `2024-10`"),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2024-11' THEN total ELSE 0 END) AS `2024-11`"),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2024-12' THEN total ELSE 0 END) AS `2024-12`"),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2025-01' THEN total ELSE 0 END) AS `2025-01`"),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2025-02' THEN total ELSE 0 END) AS `2025-02`"),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2025-03' THEN total ELSE 0 END) AS `2025-03`"),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2025-04' THEN total ELSE 0 END) AS `2025-04`"),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2025-05' THEN total ELSE 0 END) AS `2025-05`"),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2025-06' THEN total ELSE 0 END) AS `2025-06`"),
                DB::raw("SUM(CASE WHEN DATE_FORMAT(invoice_date, '%Y-%m') = '2025-07' THEN total ELSE 0 END) AS `2025-07`"),
                DB::raw("SUM(total) as 'lifeTimeValue'")
            )
            ->where('invoice_date', '<', $nextYear)
            ->groupBy('customer_email', 'product_name', 'currency')
            ->orderBy('customer_email')
            ->orderBy('product_name')
            ->get();

        $products = Products::all();
        $productInvoices = collect();
        $totals = collect();

        // Create a collection of invoices stored by product
        foreach ($products as $product) {
            $invoicesByProduct = $flattenedInvoices->where('product_name', $product->name);
            $productInvoices->push(['product_name' => $product->name, 'invoices' => $invoicesByProduct]);

            $totalProduct = [
                '2024-08' => $invoicesByProduct->sum('2024-08'),
                '2024-09' => $invoicesByProduct->sum('2024-09'),
                '2024-10' => $invoicesByProduct->sum('2024-10'),
                '2024-11' => $invoicesByProduct->sum('2024-11'),
                '2024-12' => $invoicesByProduct->sum('2024-12'),
                '2025-01' => $invoicesByProduct->sum('2025-01'),
                '2025-02' => $invoicesByProduct->sum('2025-02'),
                '2025-03' => $invoicesByProduct->sum('2025-03'),
                '2025-04' => $invoicesByProduct->sum('2025-04'),
                '2025-05' => $invoicesByProduct->sum('2025-05'),
                '2025-06' => $invoicesByProduct->sum('2025-06'),
                '2025-07' => $invoicesByProduct->sum('2025-07'),
                'lifeTimeValue' => $invoicesByProduct->sum('lifeTimeValue')
            ];

            $totals->put($product->name, $totalProduct);
        }

        return [$productInvoices, $totals];
    }
}
