<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Inventory Report</title><style>body{font-family:DejaVu Sans,sans-serif;font-size:12px;color:#111}table{width:100%;border-collapse:collapse;margin-top:16px}th,td{border:1px solid #ddd;padding:8px;text-align:left}h1{margin:0 0 12px}</style></head>
<body>
    <h1>Inventory Report</h1>
    <table><tr><th>SKU</th><th>Product</th><th>Category</th><th>Stock</th><th>Unit Price</th></tr>@foreach ($products as $product)<tr><td>{{ $product->sku }}</td><td>{{ $product->name }}</td><td>{{ $product->category?->name }}</td><td>{{ $product->stock_on_hand }}</td><td>{{ number_format((float) $product->unit_price, 2) }}</td></tr>@endforeach</table>
</body>
</html>
