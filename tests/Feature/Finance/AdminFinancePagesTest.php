<?php

namespace Tests\Feature\Finance;

use App\Models\Finance\ChartOfAccount;
use App\Models\Finance\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminFinancePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
    }

    public function test_admin_can_view_chart_of_accounts_page(): void
    {
        $account = ChartOfAccount::query()->create([
            'code' => '1000',
            'name' => 'Cash at Bank',
            'type' => 'Asset',
            'category' => 'Current Asset',
            'is_active' => true,
            'description' => 'Primary operating account.',
        ]);

        JournalEntry::query()->create([
            'reference' => 'JE-1000',
            'entry_date' => '2026-03-10',
            'description' => 'Opening balance',
            'debit' => 25000,
            'credit' => 0,
            'status' => 'posted',
            'chart_of_account_id' => $account->id,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->get(route('admin.finance.chart-of-accounts'));

        $response->assertOk();
        $response->assertSee('Chart of Accounts');
        $response->assertSee('1000');
        $response->assertSee('Cash at Bank');
    }

    public function test_admin_can_view_journal_entries_page(): void
    {
        $account = ChartOfAccount::query()->create([
            'code' => '4000',
            'name' => 'Service Revenue',
            'type' => 'Revenue',
            'category' => 'Income',
            'is_active' => true,
        ]);

        JournalEntry::query()->create([
            'reference' => 'JE-2000',
            'entry_date' => '2026-03-10',
            'description' => 'Monthly revenue recognition',
            'debit' => 0,
            'credit' => 12500,
            'status' => 'posted',
            'chart_of_account_id' => $account->id,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->get(route('admin.finance.journal-entries'));

        $response->assertOk();
        $response->assertSee('Journal Entries');
        $response->assertSee('JE-2000');
        $response->assertSee('Service Revenue');
    }

    public function test_non_admin_cannot_access_finance_pages(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)
            ->get(route('admin.finance.reports'));

        $response->assertForbidden();
    }

    private function adminUser(): User
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $user->assignRole('Admin');

        return $user;
    }
}
