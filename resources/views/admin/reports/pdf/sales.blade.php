<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Sales Report</title><style>body{font-family:DejaVu Sans,sans-serif;font-size:12px;color:#111}table{width:100%;border-collapse:collapse;margin-top:16px}th,td{border:1px solid #ddd;padding:8px;text-align:left}h1{margin:0 0 12px}</style></head>
<body>
    <h1>Sales Report</h1>
    <div>Period: {{ $startDate }} to {{ $endDate }}</div>
    <table><tr><th>Invoice</th><th>Date</th><th>Customer</th><th>Total</th><th>Status</th></tr>@foreach ($invoices as $invoice)<tr><td>{{ $invoice->invoice_number }}</td><td>{{ optional($invoice->issue_date)->format('Y-m-d') }}</td><td>{{ $invoice->customer?->name }}</td><td>{{ number_format((float) $invoice->total_amount, 2) }}</td><td>{{ $invoice->status }}</td></tr>@endforeach</table>
</body>
</html>
