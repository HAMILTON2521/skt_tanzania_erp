<?php

namespace Tests\Unit\HR;

use App\Models\HR\Employee;
use PHPUnit\Framework\TestCase;

class EmployeeTest extends TestCase
{
    public function test_full_name_accessor_combines_first_and_last_name(): void
    {
        $employee = new Employee([
            'first_name' => 'Asha',
            'last_name' => 'Mrema',
        ]);

        $this->assertSame('Asha Mrema', $employee->full_name);
    }

    public function test_paye_is_zero_below_first_tax_band(): void
    {
        $employee = new Employee();

        $this->assertSame(0.0, $employee->calculatePAYE(270000));
    }

    public function test_paye_is_calculated_for_higher_band(): void
    {
        $employee = new Employee();

        $this->assertSame(145500.0, $employee->calculatePAYE(1050000));
    }

    public function test_nssf_is_capped(): void
    {
        $employee = new Employee();

        $this->assertSame(200000.0, $employee->calculateNSSF(3000000));
    }

    public function test_wcf_is_one_percent_of_gross_pay(): void
    {
        $employee = new Employee();

        $this->assertSame(12500.0, $employee->calculateWCF(1250000));
    }
}
