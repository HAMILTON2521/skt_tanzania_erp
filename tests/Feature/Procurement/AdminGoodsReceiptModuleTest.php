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

class AdminGoodsReceiptModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
    }

    public function test_admin_can_record_goods_receipt(): void
    {
        $admin = $this->adminUser();
        $department = Department::query()->create([
            'name' => 'Procurement',
            'code' => 'PROC',
            'is_active' => true,
        ]);

        $purchaseRequest = PurchaseRequest::query()->create([
            'pr_number' => 'PR-GR-1',
            'request_date' => '2026-03-10',
            'requester_id' => $admin->id,
            'department_id' => $department->id,
            'description' => 'New office desks',
            'total_amount' => 3000000,
            'status' => PurchaseRequest::STATUS_APPROVED,
        ]);

        $supplier = Supplier::query()->create([
            'supplier_code' => 'SUP-GR-1',
            'name' => 'Desk Masters',
            'status' => 'active',
        ]);

        $purchaseOrder = PurchaseOrder::query()->create([
            'po_number' => 'PO-GR-1',
            'order_date' => '2026-03-11',
            'purchase_request_id' => $purchaseRequest->id,
            'supplier_id' => $supplier->id,
            'subtotal' => 3000000,
            'tax_amount' => 540000,
            'total_amount' => 3540000,
            'status' => PurchaseOrder::STATUS_APPROVED,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.procurement.goods-receipts.store'), [
                'purchase_order_id' => $purchaseOrder->id,
                'receipt_number' => 'GRN-1001',
                'receipt_date' => '2026-03-12',
                'status' => 'received',
                'notes' => 'Received in good condition',
            ]);

        $response->assertRedirect(route('admin.procurement.goods-receipts.index'));
        $this->assertDatabaseHas('goods_receipts', ['receipt_number' => 'GRN-1001']);
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => PurchaseOrder::STATUS_RECEIVED,
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
