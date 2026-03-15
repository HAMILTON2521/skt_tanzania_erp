<?php

namespace Tests\Feature\Sales;

use App\Models\Sales\Customer;
use App\Models\Sales\Quotation;
use App\Models\Sales\SalesInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminSalesModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
    }

    public function test_admin_can_create_customer(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.sales.customers.store'), [
                'customer_code' => 'CUST-1001',
                'name' => 'Safari Logistics',
                'email' => 'accounts@safari.test',
                'phone' => '255700000001',
                'address' => 'Dar es Salaam',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.sales.customers.index'));
        $this->assertDatabaseHas('customers', ['customer_code' => 'CUST-1001']);
    }

    public function test_admin_can_create_quotation(): void
    {
        $customer = Customer::query()->create([
            'customer_code' => 'CUST-2001',
            'name' => 'Zebra Supply',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.sales.quotations.store'), [
                'quotation_number' => 'QUO-3001',
                'customer_id' => $customer->id,
                'issue_date' => '2026-03-10',
                'valid_until' => '2026-03-20',
                'amount' => 250000,
                'status' => 'approved',
                'notes' => 'Bulk supply quote',
            ]);

        $response->assertRedirect(route('admin.sales.quotations.index'));
        $this->assertDatabaseHas('quotations', ['quotation_number' => 'QUO-3001']);
    }

    public function test_admin_can_create_sales_invoice(): void
    {
        $customer = Customer::query()->create([
            'customer_code' => 'CUST-4001',
            'name' => 'Meru Traders',
            'status' => 'active',
        ]);

        $quotation = Quotation::query()->create([
            'quotation_number' => 'QUO-4001',
            'customer_id' => $customer->id,
            'issue_date' => '2026-03-10',
            'valid_until' => '2026-03-25',
            'amount' => 150000,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.sales.invoices.store'), [
                'invoice_number' => 'SINV-5001',
                'customer_id' => $customer->id,
                'quotation_id' => $quotation->id,
                'issue_date' => '2026-03-11',
                'due_date' => '2026-03-21',
                'total_amount' => 150000,
                'status' => 'sent',
                'notes' => 'Converted from approved quote',
            ]);

        $response->assertRedirect(route('admin.sales.invoices.index'));
        $this->assertDatabaseHas('sales_invoices', ['invoice_number' => 'SINV-5001']);
    }

    public function test_admin_can_create_sales_order(): void
    {
        $customer = Customer::query()->create([
            'customer_code' => 'CUST-5001',
            'name' => 'Nile Stores',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.sales.orders.store'), [
                'order_number' => 'SO-6001',
                'customer_id' => $customer->id,
                'order_date' => '2026-03-10',
                'expected_delivery_date' => '2026-03-18',
                'total_amount' => 99000,
                'status' => 'confirmed',
                'notes' => 'Priority order',
            ]);

        $response->assertRedirect(route('admin.sales.orders.index'));
        $this->assertDatabaseHas('sales_orders', ['order_number' => 'SO-6001']);
    }

    public function test_admin_can_record_receipt(): void
    {
        $customer = Customer::query()->create([
            'customer_code' => 'CUST-7001',
            'name' => 'Lake Hub',
            'status' => 'active',
        ]);

        $invoice = SalesInvoice::query()->create([
            'invoice_number' => 'SINV-7001',
            'customer_id' => $customer->id,
            'issue_date' => '2026-03-10',
            'due_date' => '2026-03-15',
            'total_amount' => 50000,
            'status' => 'sent',
        ]);

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.sales.receipts.store'), [
                'receipt_number' => 'RCT-8001',
                'sales_invoice_id' => $invoice->id,
                'receipt_date' => '2026-03-11',
                'amount' => 50000,
                'payment_method' => 'cash',
                'status' => 'received',
                'notes' => 'Paid in full',
            ]);

        $response->assertRedirect(route('admin.sales.receipts.index'));
        $this->assertDatabaseHas('receipts', ['receipt_number' => 'RCT-8001']);
    }

    public function test_non_admin_cannot_access_sales_pages(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();

        $this->actingAs($user)
            ->get(route('admin.sales.customers.index'))
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
