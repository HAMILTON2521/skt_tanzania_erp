<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::findOrCreate('Admin', 'web');
    }

    public function test_admin_login_page_is_available(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertOk();
        $response->assertSee('Access Admin Workspace');
    }

    public function test_admin_can_sign_in_from_admin_login_page(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);
        $user->assignRole('Admin');

        $response = $this->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_non_admin_cannot_sign_in_from_admin_login_page(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $response = $this->from(route('admin.login'))->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_area_redirects_guests_to_admin_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }
}
