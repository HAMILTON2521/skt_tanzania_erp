<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Financial Report</title><style>body{font-family:DejaVu Sans,sans-serif;font-size:12px;color:#111}table{width:100%;border-collapse:collapse;margin-top:16px}th,td{border:1px solid #ddd;padding:8px;text-align:left}h1{margin:0 0 12px} .meta{margin-bottom:16px}</style></head>
<body>
    <h1>Financial Report</h1>
    <div class="meta">Period: {{ $startDate }} to {{ $endDate }}</div>
    <table><tr><th>Revenue</th><th>Expenses</th><th>Net</th></tr><tr><td>{{ number_format((float) $revenue, 2) }}</td><td>{{ number_format((float) $expenses, 2) }}</td><td>{{ number_format((float) ($revenue - $expenses), 2) }}</td></tr></table>
</body>
</html>
