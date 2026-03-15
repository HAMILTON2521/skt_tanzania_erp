<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\HR\Payroll;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payroll::query()->with(['employee:id,first_name,last_name,employee_code', 'processor:id,name']);

        if ($request->filled('employee')) {
            $query->where('employee_id', $request->integer('employee'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $payrolls = $query->orderByDesc('payment_date')->orderByDesc('id')->paginate(15)->withQueryString();
        $summaryPayrolls = Payroll::query()->get(['status', 'net_pay']);

        return view('admin.hr.payroll.index', [
            'navigation' => config('admin.navigation', []),
            'payrolls' => $payrolls,
            'employees' => Employee::query()->orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name', 'employee_code', 'salary']),
            'statuses' => $this->statuses(),
            'filters' => [
                'employee' => $request->string('employee')->toString(),
                'status' => $request->string('status')->toString(),
            ],
            'summary' => [
                'count' => $summaryPayrolls->count(),
                'processed' => $summaryPayrolls->where('status', 'processed')->count(),
                'net_pay' => $summaryPayrolls->sum('net_pay'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'payroll_period' => [
                'required',
                'string',
                'max:20',
                Rule::unique('payrolls')->where(fn ($query) => $query->where('employee_id', $request->integer('employee_id'))),
            ],
            'payment_date' => ['required', 'date'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'overtime' => ['nullable', 'numeric', 'min:0'],
            'bonus' => ['nullable', 'numeric', 'min:0'],
            'other_deductions' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', array_keys($this->statuses()))],
        ]);

        $employee = Employee::query()->findOrFail($validated['employee_id']);
        $basicSalary = (float) $employee->salary;
        $allowances = (float) ($validated['allowances'] ?? 0);
        $overtime = (float) ($validated['overtime'] ?? 0);
        $bonus = (float) ($validated['bonus'] ?? 0);
        $grossPay = $basicSalary + $allowances + $overtime + $bonus;
        $paye = $employee->calculatePAYE($grossPay);
        $nssf = $employee->calculateNSSF($grossPay);
        $wcf = $employee->calculateWCF($grossPay);
        $otherDeductions = (float) ($validated['other_deductions'] ?? 0);
        $totalDeductions = $paye + $nssf + $wcf + $otherDeductions;

        Payroll::query()->create([
            'employee_id' => $employee->id,
            'payroll_period' => $validated['payroll_period'],
            'payment_date' => $validated['payment_date'],
            'basic_salary' => $basicSalary,
            'allowances' => $allowances,
            'overtime' => $overtime,
            'bonus' => $bonus,
            'gross_pay' => $grossPay,
            'paye' => $paye,
            'nssf' => $nssf,
            'wcf' => $wcf,
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,
            'net_pay' => $grossPay - $totalDeductions,
            'status' => $validated['status'],
            'processed_by' => $validated['status'] === 'processed' ? $request->user()?->id : null,
            'processed_at' => $validated['status'] === 'processed' ? now() : null,
        ]);

        return redirect()->route('admin.hr.payroll.index')
            ->with('status', 'Payroll entry generated successfully.');
    }

    private function statuses(): array
    {
        return [
            'draft' => 'Draft',
            'processed' => 'Processed',
            'paid' => 'Paid',
        ];
    }
}
