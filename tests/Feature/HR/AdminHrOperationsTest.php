<?php

namespace Tests\Feature\HR;

use App\Models\HR\Department;
use App\Models\HR\Employee;
use App\Models\HR\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminHrOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
    }

    public function test_admin_can_create_department(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.hr.departments.store'), [
                'name' => 'Compliance',
                'code' => 'CMP',
                'description' => 'Internal control and compliance',
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.hr.departments.index'));
        $this->assertDatabaseHas('departments', ['code' => 'CMP']);
    }

    public function test_admin_can_record_attendance(): void
    {
        $employee = $this->employee();

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.hr.attendance.store'), [
                'employee_id' => $employee->id,
                'date' => '2026-03-12',
                'check_in' => '08:00',
                'check_out' => '17:00',
                'status' => 'present',
                'notes' => 'On time',
            ]);

        $response->assertRedirect(route('admin.hr.attendance.index'));
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'status' => 'present',
        ]);
    }

    public function test_admin_can_create_leave_request(): void
    {
        $employee = $this->employee();
        $leaveType = LeaveType::query()->create([
            'name' => 'Annual Leave',
            'code' => 'AL',
            'days_per_year' => 28,
            'is_paid' => true,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.hr.leaves.store'), [
                'employee_id' => $employee->id,
                'leave_type_id' => $leaveType->id,
                'start_date' => '2026-03-15',
                'end_date' => '2026-03-19',
                'days_requested' => 5,
                'reason' => 'Scheduled leave',
                'status' => 'pending',
            ]);

        $response->assertRedirect(route('admin.hr.leaves.index'));
        $this->assertDatabaseHas('leaves', [
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
        ]);
    }

    public function test_admin_can_generate_payroll_entry(): void
    {
        $employee = $this->employee();

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.hr.payroll.store'), [
                'employee_id' => $employee->id,
                'payroll_period' => '2026-03',
                'payment_date' => '2026-03-31',
                'allowances' => 100000,
                'overtime' => 50000,
                'bonus' => 25000,
                'other_deductions' => 10000,
                'status' => 'processed',
            ]);

        $response->assertRedirect(route('admin.hr.payroll.index'));
        $this->assertDatabaseHas('payrolls', [
            'employee_id' => $employee->id,
            'payroll_period' => '2026-03',
            'status' => 'processed',
        ]);
    }

    private function employee(): Employee
    {
        $department = Department::query()->create([
            'name' => 'Operations',
            'code' => 'OPS',
            'is_active' => true,
        ]);

        return Employee::query()->create([
            'employee_code' => 'EMP-OPS-1',
            'first_name' => 'Anna',
            'last_name' => 'Mollel',
            'email' => 'anna.mollel@example.test',
            'phone' => '255700000901',
            'hire_date' => '2026-03-01',
            'department_id' => $department->id,
            'position' => 'Coordinator',
            'salary' => 1200000,
            'tin_number' => 'TIN-OPS-1',
            'nssf_number' => 'NSSF-OPS-1',
            'status' => Employee::STATUS_ACTIVE,
        ]);
    }

    private function adminUser(): User
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $user->assignRole('Admin');

        return $user;
    }
}
