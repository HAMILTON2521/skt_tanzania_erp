<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Department;
use App\Models\HR\Employee;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Employee::query()->with('department:id,name,code');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($builder) use ($search): void {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->integer('department'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $employees = $query
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        $departments = Department::query()->orderBy('name')->get(['id', 'name', 'code']);
        $summaryEmployees = Employee::query()->get(['status', 'salary']);

        return view('admin.hr.employees.index', [
            'navigation' => config('admin.navigation', []),
            'employees' => $employees,
            'departments' => $departments,
            'statuses' => Employee::getStatuses(),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'department' => $request->string('department')->toString(),
                'status' => $request->string('status')->toString(),
            ],
            'summary' => [
                'count' => $summaryEmployees->count(),
                'active' => $summaryEmployees->where('status', Employee::STATUS_ACTIVE)->count(),
                'on_leave' => $summaryEmployees->where('status', Employee::STATUS_ON_LEAVE)->count(),
                'monthly_payroll' => $summaryEmployees->sum('salary'),
            ],
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.hr.employees.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_code' => ['required', 'string', 'max:50', 'unique:employees,employee_code'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'],
            'date_of_birth' => ['nullable', 'date'],
            'hire_date' => ['required', 'date'],
            'department_id' => ['required', 'exists:departments,id'],
            'position' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:100'],
            'tin_number' => ['required', 'string', 'max:100', 'unique:employees,tin_number'],
            'nssf_number' => ['required', 'string', 'max:100', 'unique:employees,nssf_number'],
            'wcf_number' => ['nullable', 'string', 'max:100'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:'.implode(',', array_keys(Employee::getStatuses()))],
        ]);

        $validated['status'] ??= Employee::STATUS_ACTIVE;

        try {
            $employee = DB::transaction(fn () => Employee::query()->create($validated));

            if (function_exists('activity')) {
                activity()
                    ->performedOn($employee)
                    ->log('Created employee: '.$employee->full_name);
            }

            return redirect()->route('admin.hr.employees.index')
                ->with('status', 'Employee created successfully.');
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->with('error', 'Error creating employee: '.$exception->getMessage());
        }
    }

    public function show(Employee $employee): View
    {
        $employee->load('department:id,name,code');

        $grossPay = (float) $employee->salary;
        $paye = $employee->calculatePAYE($grossPay);
        $nssf = $employee->calculateNSSF($grossPay);
        $wcf = $employee->calculateWCF($grossPay);

        return view('admin.hr.employees.show', [
            'navigation' => config('admin.navigation', []),
            'employee' => $employee,
            'payrollBreakdown' => [
                'gross' => $grossPay,
                'paye' => $paye,
                'nssf' => $nssf,
                'wcf' => $wcf,
                'net' => $grossPay - $paye - $nssf - $wcf,
            ],
        ]);
    }

    public function edit(Employee $employee): RedirectResponse
    {
        return redirect()->route('admin.hr.employees.show', $employee);
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'employee_code' => ['required', 'string', 'max:50', 'unique:employees,employee_code,'.$employee->id],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email,'.$employee->id],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'],
            'date_of_birth' => ['nullable', 'date'],
            'hire_date' => ['required', 'date'],
            'department_id' => ['required', 'exists:departments,id'],
            'position' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:100'],
            'tin_number' => ['required', 'string', 'max:100', 'unique:employees,tin_number,'.$employee->id],
            'nssf_number' => ['required', 'string', 'max:100', 'unique:employees,nssf_number,'.$employee->id],
            'wcf_number' => ['nullable', 'string', 'max:100'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:'.implode(',', array_keys(Employee::getStatuses()))],
        ]);

        try {
            DB::transaction(fn () => $employee->update($validated));

            if (function_exists('activity')) {
                activity()
                    ->performedOn($employee)
                    ->log('Updated employee: '.$employee->full_name);
            }

            return redirect()->route('admin.hr.employees.show', $employee)
                ->with('status', 'Employee updated successfully.');
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->with('error', 'Error updating employee: '.$exception->getMessage());
        }
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        try {
            $name = $employee->full_name;

            DB::transaction(fn () => $employee->delete());

            if (function_exists('activity')) {
                activity()->log('Deleted employee: '.$name);
            }

            return redirect()->route('admin.hr.employees.index')
                ->with('status', 'Employee deleted successfully.');
        } catch (\Throwable $exception) {
            return back()->with('error', 'Error deleting employee: '.$exception->getMessage());
        }
    }
}
