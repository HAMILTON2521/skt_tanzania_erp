@extends('admin.layouts.app')

@section('page-title', 'Employees')
@section('page-subtitle', 'Maintain staff records, departments, payroll baselines and statutory details.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <span>People &amp; Finance</span>
            <span>/</span>
            <span class="text-white">Employees</span>
        </nav>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">{{ session('error') }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Employees</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['count'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Active</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['active'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300">On Leave</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['on_leave'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-fuchsia-300">Monthly Payroll</p><p class="mt-3 text-3xl font-semibold text-white">{{ number_format((float) $summary['monthly_payroll'], 2) }}</p></div>
        </div>

        <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Filter Employees</p>
            <form method="GET" action="{{ route('admin.hr.employees.index') }}" class="mt-6 grid gap-4 md:grid-cols-4">
                <label class="block text-sm text-slate-300">Search
                    <input name="search" value="{{ $filters['search'] }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" placeholder="Name, code or email">
                </label>
                <label class="block text-sm text-slate-300">Department
                    <select name="department" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">
                        <option value="">All departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected($filters['department'] == $department->id)>{{ $department->code ? $department->code.' | ' : '' }}{{ $department->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm text-slate-300">Status
                    <select name="status" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" @selected($filters['status'] === $statusValue)>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="flex items-end gap-3">
                    <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Apply filters</button>
                    <a href="{{ route('admin.hr.employees.index') }}" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-medium text-slate-200 transition hover:bg-white/10">Reset</a>
                </div>
            </form>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1fr_1.35fr]">
            <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Add Employee</p>
                <form method="POST" action="{{ route('admin.hr.employees.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm text-slate-300">Employee Code<input name="employee_code" value="{{ old('employee_code') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Department<select name="department_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required><option value="">Select department</option>@foreach ($departments as $department)<option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">First Name<input name="first_name" value="{{ old('first_name') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Last Name<input name="last_name" value="{{ old('last_name') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Email<input type="email" name="email" value="{{ old('email') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Phone<input name="phone" value="{{ old('phone') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Hire Date<input type="date" name="hire_date" value="{{ old('hire_date') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Date of Birth<input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                        <label class="block text-sm text-slate-300">Position<input name="position" value="{{ old('position') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Salary<input type="number" step="0.01" min="0" name="salary" value="{{ old('salary') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">TIN Number<input name="tin_number" value="{{ old('tin_number') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">NSSF Number<input name="nssf_number" value="{{ old('nssf_number') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">WCF Number<input name="wcf_number" value="{{ old('wcf_number') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                        <label class="block text-sm text-slate-300">Status<select name="status" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">@foreach ($statuses as $statusValue => $statusLabel)<option value="{{ $statusValue }}" @selected(old('status', 'active') === $statusValue)>{{ $statusLabel }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Bank Name<input name="bank_name" value="{{ old('bank_name') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                        <label class="block text-sm text-slate-300">Bank Account<input name="bank_account" value="{{ old('bank_account') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                        <label class="block text-sm text-slate-300">Emergency Contact<input name="emergency_contact" value="{{ old('emergency_contact') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                        <label class="block text-sm text-slate-300">Emergency Phone<input name="emergency_phone" value="{{ old('emergency_phone') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                    </div>
                    <label class="block text-sm text-slate-300">Address<textarea name="address" rows="3" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">{{ old('address') }}</textarea></label>
                    <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Save employee</button>
                </form>
            </section>

            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Employee Register</p>
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase tracking-[0.25em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Employee</th>
                                <th class="px-4 py-3">Department</th>
                                <th class="px-4 py-3">Position</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($employees as $employee)
                                <tr>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('admin.hr.employees.show', $employee) }}" class="font-medium text-white hover:text-cyan-200">{{ $employee->full_name }}</a>
                                        <p class="mt-1 text-xs text-slate-500">{{ $employee->employee_code }} · {{ $employee->email }}</p>
                                    </td>
                                    <td class="px-4 py-4">{{ $employee->department?->name ?: 'Unassigned' }}</td>
                                    <td class="px-4 py-4">{{ $employee->position ?: 'Not set' }}</td>
                                    <td class="px-4 py-4">{{ $statuses[$employee->status] ?? ucfirst((string) $employee->status) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-slate-400">No employees have been added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">{{ $employees->links() }}</div>
            </section>
        </div>
    </div>
@endsection
