<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Attendance;
use App\Models\HR\Employee;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Attendance::query()->with('employee:id,first_name,last_name,department_id');

        if ($request->filled('employee')) {
            $query->where('employee_id', $request->integer('employee'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date('date'));
        }

        $attendanceRecords = $query->orderByDesc('date')->orderByDesc('id')->paginate(15)->withQueryString();
        $summaryAttendance = Attendance::query()->get(['status', 'hours_worked']);

        return view('admin.hr.attendance.index', [
            'navigation' => config('admin.navigation', []),
            'attendanceRecords' => $attendanceRecords,
            'employees' => Employee::query()->orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name', 'employee_code']),
            'statuses' => $this->statuses(),
            'filters' => [
                'employee' => $request->string('employee')->toString(),
                'status' => $request->string('status')->toString(),
                'date' => $request->string('date')->toString(),
            ],
            'summary' => [
                'count' => $summaryAttendance->count(),
                'present' => $summaryAttendance->where('status', 'present')->count(),
                'hours' => $summaryAttendance->sum('hours_worked'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => [
                'required',
                'date',
                Rule::unique('attendances')->where(fn ($query) => $query->where('employee_id', $request->integer('employee_id'))),
            ],
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i', 'after:check_in'],
            'status' => ['required', 'in:'.implode(',', array_keys($this->statuses()))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['hours_worked'] = $this->calculateHours($validated['check_in'] ?? null, $validated['check_out'] ?? null);

        Attendance::query()->create($validated);

        return redirect()->route('admin.hr.attendance.index')
            ->with('status', 'Attendance record saved successfully.');
    }

    private function statuses(): array
    {
        return [
            'present' => 'Present',
            'absent' => 'Absent',
            'half_day' => 'Half Day',
            'leave' => 'Leave',
        ];
    }

    private function calculateHours(?string $checkIn, ?string $checkOut): float
    {
        if (! $checkIn || ! $checkOut) {
            return 0;
        }

        return round(Carbon::createFromFormat('H:i', $checkOut)->diffInMinutes(Carbon::createFromFormat('H:i', $checkIn)) / 60, 2);
    }
}
