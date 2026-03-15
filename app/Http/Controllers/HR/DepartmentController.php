<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Department;
use App\Models\HR\Employee;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Department::query()
            ->with(['manager:id,first_name,last_name', 'parent:id,name,code'])
            ->withCount('employees');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->toString() === 'active');
        }

        $departments = $query
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $summaryDepartments = Department::query()->withCount('employees')->get();

        return view('admin.hr.departments.index', [
            'navigation' => config('admin.navigation', []),
            'departments' => $departments,
            'managers' => Employee::query()->orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name']),
            'parentDepartments' => Department::query()->orderBy('name')->get(['id', 'name', 'code']),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
            ],
            'summary' => [
                'count' => $summaryDepartments->count(),
                'active' => $summaryDepartments->where('is_active', true)->count(),
                'headcount' => $summaryDepartments->sum('employees_count'),
            ],
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.hr.departments.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:departments,code'],
            'description' => ['nullable', 'string', 'max:2000'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'parent_id' => ['nullable', 'exists:departments,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        try {
            $department = DB::transaction(fn () => Department::query()->create($validated));

            if (function_exists('activity')) {
                activity()->performedOn($department)->log('Created department: '.$department->name);
            }

            return redirect()->route('admin.hr.departments.index')
                ->with('status', 'Department created successfully.');
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'Error creating department: '.$exception->getMessage());
        }
    }

    public function show(Department $department): View
    {
        $department->load(['manager:id,first_name,last_name', 'parent:id,name,code', 'employees:id,department_id,first_name,last_name,position,status']);

        return view('admin.hr.departments.index', [
            'navigation' => config('admin.navigation', []),
            'departments' => Department::query()->with(['manager:id,first_name,last_name', 'parent:id,name,code'])->withCount('employees')->orderBy('name')->paginate(12),
            'managers' => Employee::query()->orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name']),
            'parentDepartments' => Department::query()->orderBy('name')->get(['id', 'name', 'code']),
            'filters' => ['search' => '', 'status' => ''],
            'summary' => [
                'count' => Department::query()->count(),
                'active' => Department::query()->where('is_active', true)->count(),
                'headcount' => Employee::query()->count(),
            ],
            'selectedDepartment' => $department,
        ]);
    }

    public function edit(Department $department): RedirectResponse
    {
        return redirect()->route('admin.hr.departments.show', $department);
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:departments,code,'.$department->id],
            'description' => ['nullable', 'string', 'max:2000'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'parent_id' => ['nullable', 'exists:departments,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        try {
            DB::transaction(fn () => $department->update($validated));

            if (function_exists('activity')) {
                activity()->performedOn($department)->log('Updated department: '.$department->name);
            }

            return redirect()->route('admin.hr.departments.show', $department)
                ->with('status', 'Department updated successfully.');
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'Error updating department: '.$exception->getMessage());
        }
    }

    public function destroy(Department $department): RedirectResponse
    {
        try {
            $name = $department->name;

            DB::transaction(fn () => $department->delete());

            if (function_exists('activity')) {
                activity()->log('Deleted department: '.$name);
            }

            return redirect()->route('admin.hr.departments.index')
                ->with('status', 'Department deleted successfully.');
        } catch (\Throwable $exception) {
            return back()->with('error', 'Error deleting department: '.$exception->getMessage());
        }
    }
}
