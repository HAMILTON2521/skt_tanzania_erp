<?php

namespace Tests\Feature\Finance;

use App\Models\Finance\BankAccount;
use App\Models\Finance\TaxRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminFinanceReferenceDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
    }

    public function test_admin_can_create_bank_account(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.finance.bank-accounts.store'), [
                'account_name' => 'Collections Account',
                'bank_name' => 'CRDB',
                'account_number' => '001234567890',
                'currency' => 'TZS',
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.finance.bank-accounts.index'));
        $this->assertDatabaseHas('bank_accounts', ['account_number' => '001234567890']);
    }

    public function test_admin_can_create_tax_rate(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->post(route('admin.finance.tax-rates.store'), [
                'name' => 'Value Added Tax',
                'code' => 'VAT18',
                'rate' => 18,
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.finance.tax-rates.index'));
        $this->assertDatabaseHas('tax_rates', ['code' => 'VAT18']);
    }

    public function test_payment_page_loads_reference_data(): void
    {
        BankAccount::query()->create([
            'account_name' => 'Collections Account',
            'bank_name' => 'NMB',
            'account_number' => '00990011',
            'currency' => 'TZS',
            'is_active' => true,
        ]);

        TaxRate::query()->create([
            'name' => 'VAT',
            'code' => 'VAT18',
            'rate' => 18,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->get(route('admin.finance.payments.index'));

        $response->assertOk();
        $response->assertSee('Collections Account');
    }

    private function adminUser(): User
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $user->assignRole('Admin');

        return $user;
    }
}
