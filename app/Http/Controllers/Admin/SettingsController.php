<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\BankAccount;
use App\Models\Finance\ChartOfAccount;
use App\Models\HR\Department;
use App\Models\HR\Employee;
use App\Models\Inventory\Product;
use App\Models\Sales\Customer;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SettingsController extends Controller
{
    public function company(): View
    {
        return view('admin.settings.company', [
            'navigation' => config('admin.navigation', []),
            'company' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
                'environment' => app()->environment(),
            ],
            'summary' => [
                'employees' => Employee::query()->count(),
                'customers' => Customer::query()->count(),
                'products' => Product::query()->count(),
            ],
        ]);
    }

    public function users(): View
    {
        return view('admin.settings.users', [
            'navigation' => config('admin.navigation', []),
            'users' => User::query()->with('roles')->orderBy('name')->paginate(15),
        ]);
    }

    public function roles(): View
    {
        return view('admin.settings.roles', [
            'navigation' => config('admin.navigation', []),
            'roles' => Role::query()->withCount('permissions')->orderBy('name')->get(),
        ]);
    }

    public function permissions(): View
    {
        return view('admin.settings.permissions', [
            'navigation' => config('admin.navigation', []),
            'permissions' => Permission::query()->withCount('roles')->orderBy('name')->get(),
        ]);
    }

    public function backup(): View
    {
        $backupConfig = config('backup');

        return view('admin.settings.backup', [
            'navigation' => config('admin.navigation', []),
            'backup' => [
                'source' => data_get($backupConfig, 'backup.source.files.include', []),
                'disks' => data_get($backupConfig, 'backup.destination.disks', []),
                'retention' => data_get($backupConfig, 'cleanup.default_strategy', []),
            ],
        ]);
    }

    public function system(): View
    {
        return view('admin.settings.system', [
            'navigation' => config('admin.navigation', []),
            'system' => [
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
                'database' => DB::getDriverName(),
                'cache' => config('cache.default'),
                'queue' => config('queue.default'),
            ],
            'counts' => [
                'accounts' => ChartOfAccount::query()->count(),
                'departments' => Department::query()->count(),
                'bank_accounts' => BankAccount::query()->count(),
            ],
        ]);
    }
}
