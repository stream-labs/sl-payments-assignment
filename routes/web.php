<?php

Route::get('/', function () {
    $invoices = new \App\Http\Controllers\InvoicesController();
    list($productInvoices, $totals) = $invoices->getInvoiceDataByProduct();

    return view('welcome', compact('productInvoices', 'totals'));
});