<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\ModulePageController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Finance\ExpenseController;
use App\Http\Controllers\Finance\InvoiceController;
use App\Http\Controllers\Finance\PaymentController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\DepartmentController;
use App\Http\Controllers\HR\EmployeeController as HREmployeeController;
use App\Http\Controllers\HR\LeaveController;
use App\Http\Controllers\HR\PayrollController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\StockController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\Procurement\GoodsReceiptController;
use App\Http\Controllers\Procurement\PurchaseOrderController;
use App\Http\Controllers\Procurement\PurchaseRequestController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\QuotationController;
use App\Http\Controllers\Sales\ReceiptController;
use App\Http\Controllers\Sales\SalesInvoiceController;
use App\Http\Controllers\Sales\SalesOrderController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::prefix('admin')
    ->as('admin.')
    ->middleware('guest')
    ->group(function (): void {
        Route::get('/login', [LoginController::class, 'showAdminLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'adminLogin'])->name('login.submit');
    });

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::prefix('finance')->as('finance.')->group(function (): void {
            Route::get('/chart-of-accounts', [FinanceController::class, 'chartOfAccounts'])->name('chart-of-accounts');
            Route::get('/journal-entries', [FinanceController::class, 'journalEntries'])->name('journal-entries');
            Route::get('/bank-accounts', [FinanceController::class, 'bankAccounts'])->name('bank-accounts.index');
            Route::post('/bank-accounts', [FinanceController::class, 'storeBankAccount'])->name('bank-accounts.store');
            Route::get('/tax-rates', [FinanceController::class, 'taxRates'])->name('tax-rates.index');
            Route::post('/tax-rates', [FinanceController::class, 'storeTaxRate'])->name('tax-rates.store');
            Route::resource('invoices', InvoiceController::class)->only(['index', 'store']);
            Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
            Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
            Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
            Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
            Route::get('/reports', [FinanceController::class, 'reports'])->name('reports');
        });
        Route::prefix('sales')->as('sales.')->group(function (): void {
            Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
            Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
            Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
            Route::get('/orders', [SalesOrderController::class, 'index'])->name('orders.index');
            Route::post('/orders', [SalesOrderController::class, 'store'])->name('orders.store');
            Route::resource('invoices', SalesInvoiceController::class)->only(['index', 'store']);
            Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
            Route::post('/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
        });
        Route::prefix('inventory')->as('inventory.')->group(function (): void {
            Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::get('/products', [ProductController::class, 'index'])->name('products.index');
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
            Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
            Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
            Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
            Route::post('/stock', [StockController::class, 'store'])->name('stock.store');
        });
        Route::prefix('hr')->as('hr.')->group(function (): void {
            Route::resource('departments', DepartmentController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::resource('employees', HREmployeeController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::resource('attendance', AttendanceController::class)->only(['index', 'store']);
            Route::resource('leaves', LeaveController::class)->only(['index', 'store', 'update']);
            Route::resource('payroll', PayrollController::class)->only(['index', 'store']);
        });
        Route::prefix('procurement')->as('procurement.')->group(function (): void {
            Route::resource('purchase-requests', PurchaseRequestController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::resource('purchase-orders', PurchaseOrderController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::resource('goods-receipts', GoodsReceiptController::class)->only(['index', 'store', 'show']);
        });
        Route::prefix('reports')->as('reports.')->group(function (): void {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/export/pdf/{type}', [ReportController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export/excel/{type}', [ReportController::class, 'exportExcel'])->name('export.excel');
        });
        Route::prefix('settings')->as('settings.')->group(function (): void {
            Route::get('/company', [SettingsController::class, 'company'])->name('company');
            Route::get('/users', [SettingsController::class, 'users'])->name('users.index');
            Route::get('/roles', [SettingsController::class, 'roles'])->name('roles.index');
            Route::get('/permissions', [SettingsController::class, 'permissions'])->name('permissions.index');
            Route::get('/backup', [SettingsController::class, 'backup'])->name('backup');
            Route::get('/system', [SettingsController::class, 'system'])->name('system');
        });
        Route::prefix('notifications')->as('notifications.')->group(function (): void {
            Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
            Route::post('/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::post('/{notification}/mark-read', [AdminNotificationController::class, 'markAsRead'])->name('mark-read');
        });
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/modules/{module}', ModulePageController::class)->name('modules.show');
    });

Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware(['auth', 'admin'])
    ->name('dashboard');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
