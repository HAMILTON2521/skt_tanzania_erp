@extends('admin.layouts.app')

@section('page-title', $employee->full_name)
@section('page-subtitle', 'Employee profile, department assignment and statutory payroll breakdown.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.hr.employees.index') }}" class="hover:text-white">Employees</a>
            <span>/</span>
            <span class="text-white">{{ $employee->full_name }}</span>
        </nav>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">{{ session('error') }}</div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Employee Profile</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white">{{ $employee->full_name }}</h2>
                    </div>
                    <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200">{{ ucfirst(str_replace('_', ' ', $employee->status)) }}</span>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Employee Code</p><p class="mt-2 font-medium text-white">{{ $employee->employee_code }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Department</p><p class="mt-2 font-medium text-white">{{ $employee->department?->name ?: 'Unassigned' }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Email</p><p class="mt-2 font-medium text-white">{{ $employee->email }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Phone</p><p class="mt-2 font-medium text-white">{{ $employee->phone }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Position</p><p class="mt-2 font-medium text-white">{{ $employee->position ?: 'Not set' }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Hire Date</p><p class="mt-2 font-medium text-white">{{ optional($employee->hire_date)->format('Y-m-d') ?: 'Not set' }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">TIN Number</p><p class="mt-2 font-medium text-white">{{ $employee->tin_number ?: 'Not set' }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">NSSF Number</p><p class="mt-2 font-medium text-white">{{ $employee->nssf_number ?: 'Not set' }}</p></div>
                </div>

                <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Address</p>
                    <p class="mt-2 text-slate-200">{{ $employee->address ?: 'No address recorded.' }}</p>
                </div>

                <form method="POST" action="{{ route('admin.hr.employees.destroy', $employee) }}" class="mt-6">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-2xl border border-rose-400/30 bg-rose-400/10 px-5 py-3 text-sm font-medium text-rose-100 transition hover:bg-rose-400/20">Delete employee</button>
                </form>
            </section>

            <section class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Payroll Breakdown</p>
                    <div class="mt-6 space-y-4 text-sm text-slate-300">
                        <div class="flex items-center justify-between"><span>Gross Pay</span><span class="font-medium text-white">{{ number_format($payrollBreakdown['gross'], 2) }}</span></div>
                        <div class="flex items-center justify-between"><span>PAYE</span><span class="font-medium text-white">{{ number_format($payrollBreakdown['paye'], 2) }}</span></div>
                        <div class="flex items-center justify-between"><span>NSSF</span><span class="font-medium text-white">{{ number_format($payrollBreakdown['nssf'], 2) }}</span></div>
                        <div class="flex items-center justify-between"><span>WCF</span><span class="font-medium text-white">{{ number_format($payrollBreakdown['wcf'], 2) }}</span></div>
                        <div class="flex items-center justify-between border-t border-white/10 pt-4 text-base"><span>Estimated Net Pay</span><span class="font-semibold text-white">{{ number_format($payrollBreakdown['net'], 2) }}</span></div>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Bank & Emergency</p>
                    <div class="mt-6 space-y-4 text-sm text-slate-300">
                        <div><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Bank</p><p class="mt-1 text-white">{{ $employee->bank_name ?: 'Not set' }}</p></div>
                        <div><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Bank Account</p><p class="mt-1 text-white">{{ $employee->bank_account ?: 'Not set' }}</p></div>
                        <div><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Emergency Contact</p><p class="mt-1 text-white">{{ $employee->emergency_contact ?: 'Not set' }}</p></div>
                        <div><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Emergency Phone</p><p class="mt-1 text-white">{{ $employee->emergency_phone ?: 'Not set' }}</p></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
