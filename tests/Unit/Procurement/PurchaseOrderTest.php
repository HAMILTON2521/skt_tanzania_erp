<?php

namespace Tests\Unit\Procurement;

use App\Models\Inventory\Supplier;
use App\Models\Procurement\GoodsReceipt;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseOrderItem;
use App\Models\Procurement\PurchaseRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    public function test_fillable_attributes_include_purchase_order_fields(): void
    {
        $purchaseOrder = new PurchaseOrder();

        $this->assertSame([
            'po_number',
            'order_date',
            'purchase_request_id',
            'supplier_id',
            'expected_delivery_date',
            'shipping_address',
            'subtotal',
            'tax_amount',
            'total_amount',
            'status',
            'approved_by',
            'approved_at',
            'notes',
        ], $purchaseOrder->getFillable());
    }

    public function test_purchase_request_supplier_and_approver_relationships_are_belongs_to(): void
    {
        $purchaseOrder = new PurchaseOrder();

        $this->assertInstanceOf(BelongsTo::class, $purchaseOrder->purchaseRequest());
        $this->assertInstanceOf(PurchaseRequest::class, $purchaseOrder->purchaseRequest()->getRelated());
        $this->assertInstanceOf(BelongsTo::class, $purchaseOrder->supplier());
        $this->assertInstanceOf(Supplier::class, $purchaseOrder->supplier()->getRelated());
        $this->assertInstanceOf(BelongsTo::class, $purchaseOrder->approver());
        $this->assertInstanceOf(User::class, $purchaseOrder->approver()->getRelated());
    }

    public function test_items_and_receipts_relationships_are_has_many(): void
    {
        $purchaseOrder = new PurchaseOrder();

        $this->assertInstanceOf(HasMany::class, $purchaseOrder->items());
        $this->assertInstanceOf(PurchaseOrderItem::class, $purchaseOrder->items()->getRelated());
        $this->assertInstanceOf(HasMany::class, $purchaseOrder->receipts());
        $this->assertInstanceOf(GoodsReceipt::class, $purchaseOrder->receipts()->getRelated());
    }

    public function test_status_labels_are_available(): void
    {
        $this->assertSame([
            PurchaseOrder::STATUS_DRAFT => 'Draft',
            PurchaseOrder::STATUS_PENDING => 'Pending',
            PurchaseOrder::STATUS_APPROVED => 'Approved',
            PurchaseOrder::STATUS_SENT => 'Sent',
            PurchaseOrder::STATUS_PARTIALLY_RECEIVED => 'Partially Received',
            PurchaseOrder::STATUS_RECEIVED => 'Received',
            PurchaseOrder::STATUS_CANCELLED => 'Cancelled',
        ], PurchaseOrder::getStatuses());
    }
}
