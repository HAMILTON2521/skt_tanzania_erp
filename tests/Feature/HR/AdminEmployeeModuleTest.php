<?php

namespace Tests\Feature\HR;

use App\Models\HR\Department;
use App\Models\HR\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminEmployeeModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
    }

    public function test_admin_can_view_employee_page(): void
    {
        $department = Department::query()->create([
            'name' => 'Finance',
            'code' => 'FIN',
            'is_active' => true,
        ]);

        Employee::query()->create([
            'employee_code' => 'EMP-001',
            'first_name' => 'Asha',
            'last_name' => 'Mrema',
            'email' => 'asha@example.test',
            'phone' => '255700000001',
            'hire_date' => '2026-03-01',
            'department_id' => $department->id,
            'position' => 'Accountant',
            'salary' => 1200000,
            'tin_number' => 'TIN-001',
            'nssf_number' => 'NSSF-001',
            'status' => Employee::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->get(route('admin.hr.employees.index'));

        $response->assertOk();
        $response->assertSee('Employees');
        $response->assertSee('Asha Mrema');
        $response->assertSee('Finance');
    }

    public function test_admin_can_create_employee(): void
    {
        $department = Department::query()->create([
            'name' => 'Operations',
            'code' => 'OPS',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.hr.employees.store'), [
                'employee_code' => 'EMP-1002',
                'first_name' => 'John',
                'last_name' => 'Mushi',
                'email' => 'john.mushi@example.test',
                'phone' => '255700000222',
                'hire_date' => '2026-03-10',
                'department_id' => $department->id,
                'position' => 'Supervisor',
                'salary' => 950000,
                'tin_number' => 'TIN-1002',
                'nssf_number' => 'NSSF-1002',
                'status' => Employee::STATUS_ACTIVE,
            ]);

        $response->assertRedirect(route('admin.hr.employees.index'));
        $this->assertDatabaseHas('employees', [
            'employee_code' => 'EMP-1002',
            'email' => 'john.mushi@example.test',
        ]);
    }

    public function test_admin_can_view_employee_profile(): void
    {
        $department = Department::query()->create([
            'name' => 'HR',
            'code' => 'HR',
            'is_active' => true,
        ]);

        $employee = Employee::query()->create([
            'employee_code' => 'EMP-2003',
            'first_name' => 'Neema',
            'last_name' => 'Joseph',
            'email' => 'neema@example.test',
            'phone' => '255700000333',
            'hire_date' => '2026-03-08',
            'department_id' => $department->id,
            'position' => 'HR Officer',
            'salary' => 1100000,
            'tin_number' => 'TIN-2003',
            'nssf_number' => 'NSSF-2003',
            'status' => Employee::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->get(route('admin.hr.employees.show', $employee));

        $response->assertOk();
        $response->assertSee('Neema Joseph');
        $response->assertSee('Estimated Net Pay');
    }

    public function test_non_admin_cannot_access_employee_pages(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();

        $this->actingAs($user)
            ->get(route('admin.hr.employees.index'))
            ->assertForbidden();
    }

    private function adminUser(): User
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $user->assignRole('Admin');

        return $user;
    }
}
