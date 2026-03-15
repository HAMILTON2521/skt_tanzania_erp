<?php

namespace Tests\Unit\HR;

use App\Models\HR\Department;
use App\Models\HR\Employee;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    public function test_fillable_attributes_include_department_fields(): void
    {
        $department = new Department();

        $this->assertSame([
            'name',
            'code',
            'description',
            'manager_id',
            'parent_id',
            'is_active',
        ], $department->getFillable());
    }

    public function test_manager_relationship_is_belongs_to_employee(): void
    {
        $department = new Department();

        $relation = $department->manager();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Employee::class, $relation->getRelated());
    }

    public function test_parent_and_children_relationships_are_self_referential(): void
    {
        $department = new Department();

        $this->assertInstanceOf(BelongsTo::class, $department->parent());
        $this->assertInstanceOf(Department::class, $department->parent()->getRelated());
        $this->assertInstanceOf(HasMany::class, $department->children());
        $this->assertInstanceOf(Department::class, $department->children()->getRelated());
    }

    public function test_employees_relationship_is_has_many(): void
    {
        $department = new Department();

        $relation = $department->employees();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertInstanceOf(Employee::class, $relation->getRelated());
    }
}
