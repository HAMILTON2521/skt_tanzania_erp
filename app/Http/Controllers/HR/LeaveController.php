<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\HR\Leave;
use App\Models\HR\LeaveType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request): View
    {
        $query = Leave::query()->with(['employee:id,first_name,last_name', 'leaveType:id,name,code', 'approver:id,name']);

        if ($request->filled('employee')) {
            $query->where('employee_id', $request->integer('employee'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $leaves = $query->orderByDesc('start_date')->orderByDesc('id')->paginate(15)->withQueryString();
        $summaryLeaves = Leave::query()->get(['status', 'days_requested']);

        return view('admin.hr.leave.index', [
            'navigation' => config('admin.navigation', []),
            'leaves' => $leaves,
            'employees' => Employee::query()->orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name', 'employee_code']),
            'leaveTypes' => LeaveType::query()->orderBy('name')->get(['id', 'name', 'code']),
            'statuses' => $this->statuses(),
            'filters' => [
                'employee' => $request->string('employee')->toString(),
                'status' => $request->string('status')->toString(),
            ],
            'summary' => [
                'count' => $summaryLeaves->count(),
                'pending' => $summaryLeaves->where('status', 'pending')->count(),
                'approved_days' => $summaryLeaves->where('status', 'approved')->sum('days_requested'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'days_requested' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:'.implode(',', array_keys($this->statuses()))],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['status'] !== 'pending') {
            $validated['approved_by'] = $request->user()?->id;
            $validated['approved_at'] = now();
        }

        Leave::query()->create($validated);

        return redirect()->route('admin.hr.leaves.index')
            ->with('status', 'Leave request recorded successfully.');
    }

    public function update(Request $request, Leave $leave): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys($this->statuses()))],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['approved_by'] = $request->user()?->id;
        $validated['approved_at'] = now();

        $leave->update($validated);

        return redirect()->route('admin.hr.leaves.index')
            ->with('status', 'Leave status updated successfully.');
    }

    private function statuses(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }
}
