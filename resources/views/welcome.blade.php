<h1>Life Time Value</h1>

@php
    function formatCurrency($amount): string
    {
       return '$' . number_format(($amount / 100), 2, '.', '');
    }
@endphp

@foreach($productInvoices as $productInvoice)
@php
   $productName = $productInvoice['product_name'];
@endphp
<div>
    <table>
        <thead>
            <th>Customer Email</th>
            <th>Product Name</th>
            <th>2024-08 1</th>
            <th>2024-09 2</th>
            <th>2024-10 3</th>
            <th>2024-11 4</th>
            <th>2024-12 5</th>
            <th>2025-01 6</th>
            <th>2025-02 7</th>
            <th>2025-03 8</th>
            <th>2025-04 9</th>
            <th>2025-05 10</th>
            <th>2025-06 11</th>
            <th>2025-07 12</th>
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
                <td>{{ formatCurrency($invoice->{'2025-07'}) }}</td>
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
                <th>{{ formatCurrency($totals[$productName]['2025-07']) }}</th>
                <th>{{ formatCurrency($totals[$productName]['lifeTimeValue']) }}</th>
            </tr>
        </tfoot>
    </table>
@endforeach
</div>

<style>
    th, td {
        padding: 15px;
        text-align: left;
    }

    th {
        background-color: #04AA6D;
        color: white;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 40px;
    }

    tr:nth-child(even) {background-color: #f2f2f2;}
</style>