<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminSupportPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin', 'web');
    }

    public function test_admin_can_view_support_pages(): void
    {
        Permission::findOrCreate('view reports', 'web');

        $admin = $this->adminUser();

        $this->actingAs($admin)->get(route('admin.settings.company'))->assertOk()->assertSee('Company Settings');
        $this->actingAs($admin)->get(route('admin.settings.users.index'))->assertOk()->assertSee('Users');
        $this->actingAs($admin)->get(route('admin.settings.roles.index'))->assertOk()->assertSee('Roles');
        $this->actingAs($admin)->get(route('admin.settings.permissions.index'))->assertOk()->assertSee('Permissions');
        $this->actingAs($admin)->get(route('admin.settings.system'))->assertOk()->assertSee('System Health');
        $this->actingAs($admin)->get(route('admin.settings.backup'))->assertOk()->assertSee('Backup');
    }

    public function test_admin_can_update_profile(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)
            ->put(route('admin.profile.update'), [
                'name' => 'Updated Admin',
                'email' => 'updated-admin@example.test',
            ]);

        $response->assertRedirect(route('admin.profile.edit'));
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Updated Admin',
            'email' => 'updated-admin@example.test',
        ]);
    }

    public function test_admin_can_view_notifications_and_audit_logs(): void
    {
        $admin = $this->adminUser();

        $admin->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\SystemAlert',
            'data' => ['title' => 'System Alert', 'message' => 'A test alert was generated.'],
        ]);

        activity()->causedBy($admin)->log('Viewed support area');

        $this->actingAs($admin)->get(route('admin.notifications.index'))->assertOk()->assertSee('System Alert');
        $this->actingAs($admin)->get(route('admin.audit-logs.index'))->assertOk()->assertSee('Viewed support area');
    }

    private function adminUser(): User
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $user->assignRole('Admin');

        return $user;
    }
}
