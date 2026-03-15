<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\Invoice;
use App\Models\Finance\Payment;
use App\Models\Finance\Expense;
use App\Models\HR\Employee;
use App\Models\Inventory\Product;
use App\Models\Procurement\PurchaseRequest;
use App\Models\Sales\SalesInvoice;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $navigation = config('admin.navigation', []);
        $user = $request->user();

        $topLevelModules = collect($navigation)->sum(fn (array $section) => count($section['items'] ?? []));
        $linkedPages = collect($navigation)
            ->flatMap(fn (array $section) => $section['items'] ?? [])
            ->sum(fn (array $item) => count($item['children'] ?? []));

        $salesRevenue = Schema::hasTable('sales_invoices')
            ? (float) SalesInvoice::query()->where('status', 'paid')->sum('total_amount')
            : 0;
        $financeRevenue = Schema::hasTable('invoices')
            ? (float) Invoice::query()->where('status', 'paid')->sum('total_amount')
            : 0;
        $expenseTotal = Schema::hasTable('expenses')
            ? (float) Expense::query()->sum('amount')
            : 0;
        $paymentsTotal = Schema::hasTable('payments')
            ? (float) Payment::query()->sum('amount')
            : 0;

        $stats = [
            [
                'label' => 'Platform Users',
                'value' => $this->countTable('users'),
                'accent' => 'text-cyan-300',
                'description' => 'All authenticated ERP accounts across admin and operations.',
            ],
            [
                'label' => 'Product Records',
                'value' => $this->countTable('products'),
                'accent' => 'text-emerald-300',
                'description' => 'Inventory master data currently stored in PostgreSQL.',
            ],
            [
                'label' => 'Customers',
                'value' => $this->countTable('customers'),
                'accent' => 'text-amber-300',
                'description' => 'Customer accounts available for quotations, orders and invoicing.',
            ],
            [
                'label' => 'Employees',
                'value' => $this->countTable('employees'),
                'accent' => 'text-fuchsia-300',
                'description' => 'HR records ready for leave and payroll workflows.',
            ],
        ];

        $heroMetrics = [
            [
                'label' => 'Paid Revenue',
                'value' => number_format($salesRevenue + $financeRevenue, 2),
                'tone' => 'text-cyan-100',
            ],
            [
                'label' => 'Payments Logged',
                'value' => number_format($paymentsTotal, 2),
                'tone' => 'text-emerald-100',
            ],
            [
                'label' => 'Expenses Logged',
                'value' => number_format($expenseTotal, 2),
                'tone' => 'text-amber-100',
            ],
            [
                'label' => 'Unread Alerts',
                'value' => number_format($user?->unreadNotifications()->count() ?? 0),
                'tone' => 'text-fuchsia-100',
            ],
        ];

        $quickActions = [
            [
                'title' => 'Review Quotations',
                'summary' => 'Jump directly into the sales quotation register.',
                'route' => route('admin.sales.quotations.index'),
            ],
            [
                'title' => 'View Products',
                'summary' => 'Check inventory records and stock readiness.',
                'route' => route('admin.inventory.products.index'),
            ],
            [
                'title' => 'Open Employees',
                'summary' => 'Manage staff, leave and payroll operations.',
                'route' => route('admin.hr.employees.index'),
            ],
            [
                'title' => 'System Settings',
                'summary' => 'Inspect system health and access configuration.',
                'route' => route('admin.settings.system'),
            ],
        ];

        $moduleCards = [
            [
                'title' => 'Users & Access',
                'route' => route('admin.settings.users.index'),
                'metric' => $this->countTable('users'),
                'metric_label' => 'user accounts',
                'description' => 'Manage admins, roles, permissions and access boundaries.',
                'eyebrow' => 'Administration',
            ],
            [
                'title' => 'Inventory',
                'route' => route('admin.inventory.products.index'),
                'metric' => $this->countTable('products'),
                'metric_label' => 'products',
                'description' => 'Product master records, warehouses and stock movement activity.',
                'eyebrow' => 'Operations',
            ],
            [
                'title' => 'Sales',
                'route' => route('admin.sales.invoices.index'),
                'metric' => $this->countTable('sales_invoices'),
                'metric_label' => 'sales invoices',
                'description' => 'Track customer billing, invoice flow and collections readiness.',
                'eyebrow' => 'Revenue',
            ],
            [
                'title' => 'Procurement',
                'route' => route('admin.procurement.purchase-requests.index'),
                'metric' => $this->countTable('purchase_requests'),
                'metric_label' => 'purchase requests',
                'description' => 'Monitor internal demand, supplier ordering and receipt confirmation.',
                'eyebrow' => 'Supply Chain',
            ],
            [
                'title' => 'HR & Payroll',
                'route' => route('admin.hr.payroll.index'),
                'metric' => $this->countTable('payrolls'),
                'metric_label' => 'payroll entries',
                'description' => 'Employee records, payroll runs and statutory calculations.',
                'eyebrow' => 'People',
            ],
            [
                'title' => 'Finance',
                'route' => route('admin.finance.chart-of-accounts'),
                'metric' => $this->countTable('journal_entries'),
                'metric_label' => 'journal entries',
                'description' => 'General ledger setup, transactions and financial oversight.',
                'eyebrow' => 'Accounting',
            ],
        ];

        $financeSnapshot = [
            [
                'title' => 'Chart Of Accounts',
                'value' => $this->countTable('chart_of_accounts'),
                'note' => 'Ledger structure currently defined.',
            ],
            [
                'title' => 'Journal Entries',
                'value' => $this->countTable('journal_entries'),
                'note' => 'Financial postings captured so far.',
            ],
            [
                'title' => 'Finance Invoices',
                'value' => $this->countTable('invoices'),
                'note' => 'Finance-side invoice records available.',
            ],
            [
                'title' => 'Payments',
                'value' => $this->countTable('payments'),
                'note' => 'Payment records stored in the system.',
            ],
            [
                'title' => 'Expenses',
                'value' => $this->countTable('expenses'),
                'note' => 'Expense records awaiting approval or settlement.',
            ],
        ];

        $workflowCards = [
            [
                'title' => 'Procurement Pipeline',
                'value' => $this->countTable('purchase_orders'),
                'description' => 'Purchase orders currently flowing toward supplier fulfillment.',
            ],
            [
                'title' => 'Sales Pipeline',
                'value' => $this->countTable('sales_orders'),
                'description' => 'Sales orders available as the basis for fulfillment and invoicing flows.',
            ],
            [
                'title' => 'Inventory Activity',
                'value' => $this->countTable('stock_movements'),
                'description' => 'Stock movement records driving availability and warehouse changes.',
            ],
        ];

        $healthCards = [
            [
                'title' => 'Operational Coverage',
                'value' => $linkedPages,
                'description' => 'Live linked pages currently available from the admin sidebar.',
            ],
            [
                'title' => 'Employees Ready',
                'value' => Employee::query()->where('status', Employee::STATUS_ACTIVE)->count(),
                'description' => 'Active employees currently available for attendance, leave and payroll.',
            ],
            [
                'title' => 'Purchase Demand',
                'value' => PurchaseRequest::query()->where('status', PurchaseRequest::STATUS_PENDING)->count(),
                'description' => 'Pending purchase requests waiting for the next procurement action.',
            ],
        ];

        $recentUsers = User::query()
            ->latest('created_at')
            ->take(5)
            ->get(['name', 'email', 'created_at']);

        return view('admin.dashboard.index', [
            'navigation' => $navigation,
            'stats' => $stats,
            'heroMetrics' => $heroMetrics,
            'quickActions' => $quickActions,
            'systemSummary' => [
                'sections' => count($navigation),
                'module_groups' => $topLevelModules,
                'linked_pages' => $linkedPages,
            ],
            'moduleCards' => $moduleCards,
            'financeSnapshot' => $financeSnapshot,
            'workflowCards' => $workflowCards,
            'healthCards' => $healthCards,
            'recentUsers' => $recentUsers,
        ]);
    }

    private function countTable(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return DB::table($table)->count();
    }
}
