<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Finance\Expense;
use App\Models\Finance\Invoice;
use App\Models\HR\Employee;
use App\Models\Inventory\Product;
use App\Models\Inventory\StockMovement;
use App\Models\Sales\Customer;
use App\Models\Sales\SalesInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(): View
    {
        $now = now();

        return view('admin.reports.index', [
            'navigation' => config('admin.navigation', []),
            'cards' => [
                [
                    'title' => 'Financial',
                    'route' => route('admin.reports.financial'),
                    'summary' => 'Revenue, expense mix and monthly income trends.',
                    'metric' => Invoice::query()->where('status', 'paid')->sum('total_amount'),
                    'metricLabel' => 'paid revenue',
                ],
                [
                    'title' => 'Inventory',
                    'route' => route('admin.reports.inventory'),
                    'summary' => 'Stock valuation, low stock exposure and outbound movements.',
                    'metric' => Product::query()->count(),
                    'metricLabel' => 'products',
                ],
                [
                    'title' => 'Sales',
                    'route' => route('admin.reports.sales'),
                    'summary' => 'Invoice performance, customer concentration and daily billing trend.',
                    'metric' => SalesInvoice::query()->count(),
                    'metricLabel' => 'sales invoices',
                ],
                [
                    'title' => 'People',
                    'route' => route('admin.hr.employees.index'),
                    'summary' => 'Employee headcount and current payroll base.',
                    'metric' => Employee::query()->count(),
                    'metricLabel' => 'employees',
                ],
            ],
            'summary' => [
                'month' => $now->format('F Y'),
                'revenue' => Invoice::query()->where('status', 'paid')->sum('total_amount'),
                'expenses' => Expense::query()->sum('amount'),
                'employees' => Employee::query()->count(),
                'products' => Product::query()->count(),
            ],
        ]);
    }

    public function financial(Request $request): View
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $revenue = Invoice::query()
            ->whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', 'paid')
            ->sum('total_amount');

        $expenses = Expense::query()
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('amount');

        $netIncome = $revenue - $expenses;

        $monthlyData = Invoice::query()
            ->selectRaw('MONTH(issue_date) as month_number, SUM(total_amount) as revenue')
            ->whereYear('issue_date', $startDate->year)
            ->where('status', 'paid')
            ->groupByRaw('MONTH(issue_date)')
            ->orderByRaw('MONTH(issue_date)')
            ->get()
            ->map(fn ($row) => [
                'month' => Carbon::create()->month((int) $row->month_number)->format('M'),
                'revenue' => (float) $row->revenue,
            ]);

        $expenseByCategory = Expense::query()
            ->select('category', DB::raw('SUM(amount) as total'))
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('admin.reports.financial', [
            'navigation' => config('admin.navigation', []),
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'revenue' => $revenue,
            'expenses' => $expenses,
            'netIncome' => $netIncome,
            'monthlyData' => $monthlyData,
            'expenseByCategory' => $expenseByCategory,
        ]);
    }

    public function inventory(Request $request): View
    {
        $totalValue = Product::query()->sum(DB::raw('stock_on_hand * unit_price'));
        $totalRetailValue = $totalValue;

        $lowStock = Product::query()
            ->whereColumn('stock_on_hand', '<=', 'reorder_level')
            ->orderBy('stock_on_hand')
            ->get();

        $topSellers = StockMovement::query()
            ->join('products', 'stock_movements.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(stock_movements.quantity) as total_sold'))
            ->where('stock_movements.movement_type', 'out')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        return view('admin.reports.inventory', [
            'navigation' => config('admin.navigation', []),
            'totalValue' => $totalValue,
            'totalRetailValue' => $totalRetailValue,
            'lowStock' => $lowStock,
            'topSellers' => $topSellers,
        ]);
    }

    public function sales(Request $request): View
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $totalSales = SalesInvoice::query()
            ->whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', 'paid')
            ->sum('total_amount');

        $totalInvoices = SalesInvoice::query()
            ->whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->count();

        $averageOrderValue = $totalInvoices > 0 ? $totalSales / $totalInvoices : 0;

        $topCustomers = Customer::query()
            ->withSum(['salesInvoices as total_spent' => function ($query) use ($startDate, $endDate): void {
                $query->whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->where('status', 'paid');
            }], 'total_amount')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        $dailySales = SalesInvoice::query()
            ->selectRaw('DATE(issue_date) as report_date, COUNT(*) as invoice_count, SUM(total_amount) as total')
            ->whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupByRaw('DATE(issue_date)')
            ->orderBy('report_date')
            ->get();

        return view('admin.reports.sales', [
            'navigation' => config('admin.navigation', []),
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'totalSales' => $totalSales,
            'totalInvoices' => $totalInvoices,
            'averageOrderValue' => $averageOrderValue,
            'topCustomers' => $topCustomers,
            'dailySales' => $dailySales,
        ]);
    }

    public function exportPdf(string $type, Request $request): Response
    {
        $data = $this->getReportData($type, $request);
        $pdf = Pdf::loadView('admin.reports.pdf.'.$type, $data);

        return $pdf->download('report-'.$type.'-'.now()->format('Y-m-d').'.pdf');
    }

    public function exportExcel(string $type, Request $request): StreamedResponse
    {
        $data = $this->getReportData($type, $request);
        $rows = $this->csvRows($type, $data);

        return response()->streamDownload(function () use ($rows): void {
            $stream = fopen('php://output', 'wb');

            foreach ($rows as $row) {
                fputcsv($stream, $row);
            }

            fclose($stream);
        }, 'report-'.$type.'-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function getReportData(string $type, Request $request): array
    {
        return match ($type) {
            'financial' => $this->financialData($request),
            'inventory' => $this->inventoryData(),
            'sales' => $this->salesData($request),
            default => [],
        };
    }

    private function financialData(Request $request): array
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        return [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'revenue' => Invoice::query()->whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()])->where('status', 'paid')->sum('total_amount'),
            'expenses' => Expense::query()->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])->sum('amount'),
            'expenseLines' => Expense::query()->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])->orderByDesc('expense_date')->get(['expense_number', 'expense_date', 'category', 'vendor_name', 'amount', 'status']),
        ];
    }

    private function inventoryData(): array
    {
        return [
            'products' => Product::query()->with('category')->orderBy('name')->get(),
            'topSellers' => StockMovement::query()
                ->join('products', 'stock_movements.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('SUM(stock_movements.quantity) as total_sold'))
                ->where('stock_movements.movement_type', 'out')
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_sold')
                ->limit(10)
                ->get(),
        ];
    }

    private function salesData(Request $request): array
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        return [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'invoices' => SalesInvoice::query()
                ->with('customer:id,name,customer_code')
                ->whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->orderByDesc('issue_date')
                ->get(),
        ];
    }

    private function csvRows(string $type, array $data): Collection
    {
        return match ($type) {
            'financial' => collect([
                ['Metric', 'Value'],
                ['Start Date', $data['startDate']],
                ['End Date', $data['endDate']],
                ['Revenue', (string) $data['revenue']],
                ['Expenses', (string) $data['expenses']],
            ])->concat(
                collect($data['expenseLines'])->map(fn ($line) => [
                    $line->expense_number,
                    optional($line->expense_date)->format('Y-m-d'),
                    $line->category,
                    $line->vendor_name,
                    (string) $line->amount,
                    $line->status,
                ])->prepend(['Expense Number', 'Date', 'Category', 'Vendor', 'Amount', 'Status'])
            ),
            'inventory' => collect($data['products'])->map(fn ($product) => [
                $product->sku,
                $product->name,
                $product->category?->name,
                (string) $product->stock_on_hand,
                (string) $product->unit_price,
            ])->prepend(['SKU', 'Product', 'Category', 'Stock On Hand', 'Unit Price']),
            'sales' => collect($data['invoices'])->map(fn ($invoice) => [
                $invoice->invoice_number,
                optional($invoice->issue_date)->format('Y-m-d'),
                $invoice->customer?->name,
                (string) $invoice->total_amount,
                $invoice->status,
            ])->prepend(['Invoice Number', 'Issue Date', 'Customer', 'Total Amount', 'Status']),
            default => collect([['No data available']]),
        };
    }

    private function resolveDateRange(Request $request): array
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->string('start_date')->toString())->startOfDay()
            : now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->string('end_date')->toString())->endOfDay()
            : now()->endOfMonth();

        return [$startDate, $endDate];
    }
}
