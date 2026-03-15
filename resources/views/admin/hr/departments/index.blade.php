@extends('admin.layouts.app')

@section('page-title', 'Departments')
@section('page-subtitle', 'Manage organizational structure, managers and team allocation.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400"><a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a><span>/</span><span>People & Finance</span><span>/</span><span class="text-white">Departments</span></nav>
        @if (session('status'))<div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>@endif
        @if (session('error'))<div class="rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">{{ session('error') }}</div>@endif
        <div class="grid gap-4 md:grid-cols-3"><div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Departments</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['count'] }}</p></div><div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Active</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['active'] }}</p></div><div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300">Headcount</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['headcount'] }}</p></div></div>
        <div class="grid gap-6 xl:grid-cols-[1fr_1.25fr]">
            <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Create Department</p>
                <form method="POST" action="{{ route('admin.hr.departments.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm text-slate-300">Name<input name="name" value="{{ old('name') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Code<input name="code" value="{{ old('code') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Manager<select name="manager_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="">Select manager</option>@foreach ($managers as $manager)<option value="{{ $manager->id }}">{{ $manager->full_name }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Parent Department<select name="parent_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="">No parent</option>@foreach ($parentDepartments as $parentDepartment)<option value="{{ $parentDepartment->id }}">{{ $parentDepartment->code }} | {{ $parentDepartment->name }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Status<select name="is_active" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="1">Active</option><option value="0">Inactive</option></select></label>
                    </div>
                    <label class="block text-sm text-slate-300">Description<textarea name="description" rows="4" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">{{ old('description') }}</textarea></label>
                    <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Save department</button>
                </form>
                @if (! empty($selectedDepartment))
                    <div class="mt-8 rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Selected Department</p>
                        <h3 class="mt-3 text-xl font-semibold text-white">{{ $selectedDepartment->name }}</h3>
                        <p class="mt-2 text-sm text-slate-300">{{ $selectedDepartment->description ?: 'No description provided.' }}</p>
                        <div class="mt-4 grid gap-3 text-sm text-slate-300 md:grid-cols-2"><div>Code: <span class="text-white">{{ $selectedDepartment->code }}</span></div><div>Manager: <span class="text-white">{{ $selectedDepartment->manager?->full_name ?: 'Unassigned' }}</span></div><div>Status: <span class="text-white">{{ $selectedDepartment->is_active ? 'Active' : 'Inactive' }}</span></div><div>Employees: <span class="text-white">{{ $selectedDepartment->employees->count() }}</span></div></div>
                    </div>
                @endif
            </section>
            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                <form method="GET" class="grid gap-4 md:grid-cols-[1fr_180px]"><input name="search" value="{{ $filters['search'] }}" placeholder="Search departments" class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><select name="status" class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="">All statuses</option><option value="active" @selected($filters['status'] === 'active')>Active</option><option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option></select></form>
                <div class="mt-6 overflow-x-auto"><table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300"><thead class="text-xs uppercase tracking-[0.25em] text-slate-500"><tr><th class="px-4 py-3">Department</th><th class="px-4 py-3">Manager</th><th class="px-4 py-3">Employees</th><th class="px-4 py-3">Status</th></tr></thead><tbody class="divide-y divide-white/5">@forelse ($departments as $department)<tr><td class="px-4 py-4"><a href="{{ route('admin.hr.departments.show', $department) }}" class="font-medium text-white hover:text-cyan-200">{{ $department->name }}</a><p class="mt-1 text-xs text-slate-500">{{ $department->code }}</p></td><td class="px-4 py-4">{{ $department->manager?->full_name ?: 'Unassigned' }}</td><td class="px-4 py-4">{{ $department->employees_count }}</td><td class="px-4 py-4">{{ $department->is_active ? 'Active' : 'Inactive' }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-slate-400">No departments have been created yet.</td></tr>@endforelse</tbody></table></div>
                <div class="mt-4">{{ $departments->links() }}</div>
            </section>
        </div>
    </div>
@endsection
