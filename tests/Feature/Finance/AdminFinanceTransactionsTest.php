<?php

namespace Tests\Feature\Finance;

use App\Models\Finance\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminFinanceTransactionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
    }

    public function test_admin_can_create_finance_invoice(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.finance.invoices.store'), [
                'invoice_number' => 'INV-1001',
                'customer_name' => 'Acme Tanzania',
                'customer_email' => 'finance@acme.test',
                'issue_date' => '2026-03-10',
                'due_date' => '2026-03-20',
                'subtotal' => 1000,
                'tax_amount' => 180,
                'status' => 'sent',
                'notes' => 'Monthly service billing',
            ]);

        $response->assertRedirect(route('admin.finance.invoices.index'));
        $this->assertDatabaseHas('invoices', [
            'invoice_number' => 'INV-1001',
            'customer_name' => 'Acme Tanzania',
            'total_amount' => 1180,
        ]);
    }

    public function test_admin_can_record_payment_against_invoice(): void
    {
        $invoice = Invoice::query()->create([
            'invoice_number' => 'INV-2001',
            'customer_name' => 'Beta Logistics',
            'issue_date' => '2026-03-10',
            'due_date' => '2026-03-25',
            'subtotal' => 2000,
            'tax_amount' => 360,
            'total_amount' => 2360,
            'status' => 'sent',
        ]);

        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.finance.payments.store'), [
                'payment_number' => 'PAY-3001',
                'invoice_id' => $invoice->id,
                'payment_date' => '2026-03-12',
                'amount' => 1200,
                'method' => 'bank_transfer',
                'reference' => 'TRX-9981',
                'status' => 'completed',
                'notes' => 'First installment',
            ]);

        $response->assertRedirect(route('admin.finance.payments.index'));
        $this->assertDatabaseHas('payments', [
            'payment_number' => 'PAY-3001',
            'invoice_id' => $invoice->id,
            'amount' => 1200,
        ]);
    }

    public function test_admin_can_record_expense(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.finance.expenses.store'), [
                'expense_number' => 'EXP-4001',
                'expense_date' => '2026-03-10',
                'category' => 'Utilities',
                'vendor_name' => 'Tanesco',
                'amount' => 450000,
                'status' => 'approved',
                'notes' => 'Power bill',
            ]);

        $response->assertRedirect(route('admin.finance.expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'expense_number' => 'EXP-4001',
            'vendor_name' => 'Tanesco',
            'amount' => 450000,
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
