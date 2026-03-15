<?php

namespace Tests\Feature\Procurement;

use App\Models\HR\Department;
use App\Models\Inventory\Supplier;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminProcurementModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
    }

    public function test_admin_can_view_purchase_requests_page(): void
    {
        $department = Department::query()->create([
            'name' => 'Operations',
            'code' => 'OPS',
            'is_active' => true,
        ]);

        PurchaseRequest::query()->create([
            'pr_number' => 'PR-001',
            'request_date' => '2026-03-11',
            'requester_id' => $this->adminUser()->id,
            'department_id' => $department->id,
            'description' => 'Printer toner restock',
            'total_amount' => 450000,
            'status' => PurchaseRequest::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->get(route('admin.procurement.purchase-requests.index'));

        $response->assertOk();
        $response->assertSee('Purchase Requests');
        $response->assertSee('PR-001');
    }

    public function test_admin_can_create_purchase_request(): void
    {
        $department = Department::query()->create([
            'name' => 'ICT',
            'code' => 'ICT',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.procurement.purchase-requests.store'), [
                'pr_number' => 'PR-1002',
                'request_date' => '2026-03-11',
                'department_id' => $department->id,
                'description' => 'Network switch replacement',
                'total_amount' => 1250000,
                'status' => PurchaseRequest::STATUS_PENDING,
            ]);

        $response->assertRedirect(route('admin.procurement.purchase-requests.index'));
        $this->assertDatabaseHas('purchase_requests', [
            'pr_number' => 'PR-1002',
            'description' => 'Network switch replacement',
        ]);
    }

    public function test_admin_can_view_purchase_orders_page(): void
    {
        $department = Department::query()->create([
            'name' => 'Finance',
            'code' => 'FIN',
            'is_active' => true,
        ]);

        $request = PurchaseRequest::query()->create([
            'pr_number' => 'PR-2001',
            'request_date' => '2026-03-10',
            'requester_id' => $this->adminUser()->id,
            'department_id' => $department->id,
            'description' => 'Office chairs',
            'total_amount' => 1800000,
            'status' => PurchaseRequest::STATUS_APPROVED,
        ]);

        $supplier = Supplier::query()->create([
            'supplier_code' => 'SUP-001',
            'name' => 'Tanzania Supplies Ltd',
            'email' => 'supplier@example.test',
            'phone' => '255700000444',
            'address' => 'Dar es Salaam',
            'status' => 'active',
        ]);

        PurchaseOrder::query()->create([
            'po_number' => 'PO-001',
            'order_date' => '2026-03-11',
            'purchase_request_id' => $request->id,
            'supplier_id' => $supplier->id,
            'subtotal' => 1800000,
            'tax_amount' => 324000,
            'total_amount' => 2124000,
            'status' => PurchaseOrder::STATUS_APPROVED,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->get(route('admin.procurement.purchase-orders.index'));

        $response->assertOk();
        $response->assertSee('Purchase Orders');
        $response->assertSee('PO-001');
        $response->assertSee('Tanzania Supplies Ltd');
    }

    public function test_admin_can_create_purchase_order(): void
    {
        $department = Department::query()->create([
            'name' => 'Admin',
            'code' => 'ADM',
            'is_active' => true,
        ]);

        $request = PurchaseRequest::query()->create([
            'pr_number' => 'PR-3001',
            'request_date' => '2026-03-09',
            'requester_id' => $this->adminUser()->id,
            'department_id' => $department->id,
            'description' => 'Stationery refill',
            'total_amount' => 275000,
            'status' => PurchaseRequest::STATUS_APPROVED,
        ]);

        $supplier = Supplier::query()->create([
            'supplier_code' => 'SUP-002',
            'name' => 'Mwenge Office Mart',
            'email' => 'mwenge@example.test',
            'phone' => '255700000555',
            'address' => 'Arusha',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.procurement.purchase-orders.store'), [
                'po_number' => 'PO-3001',
                'order_date' => '2026-03-11',
                'purchase_request_id' => $request->id,
                'supplier_id' => $supplier->id,
                'subtotal' => 275000,
                'tax_amount' => 49500,
                'status' => PurchaseOrder::STATUS_PENDING,
            ]);

        $response->assertRedirect(route('admin.procurement.purchase-orders.index'));
        $this->assertDatabaseHas('purchase_orders', [
            'po_number' => 'PO-3001',
            'supplier_id' => $supplier->id,
        ]);
    }

    public function test_non_admin_cannot_access_procurement_pages(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();

        $this->actingAs($user)
            ->get(route('admin.procurement.purchase-requests.index'))
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
