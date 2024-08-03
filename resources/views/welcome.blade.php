<h1>Life Time Value</h1>

@php
    function formatCurrency($amount): string
    {
       return number_format(($amount / 100), 2, '.', '');
    }
@endphp

@foreach($productInvoices as $productInvoice)
@php
   $productName = $productInvoice['product_name'];
@endphp

    <h1>{{ $productName }}</h1>

    <table>
        <thead>
            <th>Customer Email</th>
            <th>Product Name</th>
            <th>2024-08</th>
            <th>2024-09</th>
            <th>2024-10</th>
            <th>2024-11</th>
            <th>2024-12</th>
            <th>2025-01</th>
            <th>2025-02</th>
            <th>2025-03</th>
            <th>2025-04</th>
            <th>2025-05</th>
            <th>2025-06</th>
            <th>Life Time Value</th>
        </thead>
        <tbody>
        @foreach($productInvoice['invoices'] as $invoice)
            <tr>
                <td>{{ $invoice->customer_email }}</td>
                <td>{{ $invoice->product_name }}</td>
                <td>{{ formatCurrency($invoice->{'2024-08'}) }}</td>
                <td>{{ formatCurrency($invoice->{'2024-09'}) }}</td>
                <td>{{ formatCurrency($invoice->{'2024-10'}) }}</td>
                <td>{{ formatCurrency($invoice->{'2024-11'}) }}</td>
                <td>{{ formatCurrency($invoice->{'2024-12'}) }}</td>
                <td>{{ formatCurrency($invoice->{'2025-01'}) }}</td>
                <td>{{ formatCurrency($invoice->{'2025-02'}) }}</td>
                <td>{{ formatCurrency($invoice->{'2025-03'}) }}</td>
                <td>{{ formatCurrency($invoice->{'2025-04'}) }}</td>
                <td>{{ formatCurrency($invoice->{'2025-05'}) }}</td>
                <td>{{ formatCurrency($invoice->{'2025-06'}) }}</td>
                <td>{{ formatCurrency($invoice->lifeTimeValue) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Totals</th>
                <th>&nbsp;</th>
                <th>{{ formatCurrency($totals[$productName]['2024-08']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['2024-09']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['2024-10']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['2024-11']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['2024-12']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['2025-01']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['2025-02']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['2025-03']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['2025-04']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['2025-05']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['2025-06']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['lifeTimeValue']) }}</th>
            </tr>
        </tfoot>
    </table>
@endforeach

<style>
    thead, tfoot {
        text-align: left;
    }
</style>