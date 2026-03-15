<?php

namespace Tests\Unit\Procurement;

use App\Models\HR\Department;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseRequest;
use App\Models\Procurement\PurchaseRequestItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    public function test_fillable_attributes_include_purchase_request_fields(): void
    {
        $purchaseRequest = new PurchaseRequest();

        $this->assertSame([
            'pr_number',
            'request_date',
            'requester_id',
            'department_id',
            'description',
            'total_amount',
            'status',
            'approved_by',
            'approved_at',
            'notes',
        ], $purchaseRequest->getFillable());
    }

    public function test_items_relationship_is_has_many_purchase_request_items(): void
    {
        $purchaseRequest = new PurchaseRequest();

        $relation = $purchaseRequest->items();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertInstanceOf(PurchaseRequestItem::class, $relation->getRelated());
    }

    public function test_requester_and_approver_relationships_belong_to_users(): void
    {
        $purchaseRequest = new PurchaseRequest();

        $this->assertInstanceOf(BelongsTo::class, $purchaseRequest->requester());
        $this->assertInstanceOf(User::class, $purchaseRequest->requester()->getRelated());
        $this->assertInstanceOf(BelongsTo::class, $purchaseRequest->approver());
        $this->assertInstanceOf(User::class, $purchaseRequest->approver()->getRelated());
    }

    public function test_department_relationship_belongs_to_department(): void
    {
        $purchaseRequest = new PurchaseRequest();

        $relation = $purchaseRequest->department();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Department::class, $relation->getRelated());
    }

    public function test_purchase_order_relationship_is_has_one(): void
    {
        $purchaseRequest = new PurchaseRequest();

        $relation = $purchaseRequest->purchaseOrder();

        $this->assertInstanceOf(HasOne::class, $relation);
        $this->assertInstanceOf(PurchaseOrder::class, $relation->getRelated());
    }

    public function test_status_labels_are_available(): void
    {
        $this->assertSame([
            PurchaseRequest::STATUS_DRAFT => 'Draft',
            PurchaseRequest::STATUS_PENDING => 'Pending',
            PurchaseRequest::STATUS_APPROVED => 'Approved',
            PurchaseRequest::STATUS_REJECTED => 'Rejected',
            PurchaseRequest::STATUS_ORDERED => 'Ordered',
        ], PurchaseRequest::getStatuses());
    }
}
