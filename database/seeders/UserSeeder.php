<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::query()->updateOrCreate(
            ['email' => 'admin@skt.co.tz'],
            [
                'name' => 'System Administrator',
                'password' => 'Admin@123456',
                'email_verified_at' => now(),
            ]
        );

        if (Role::query()->where('name', 'Admin')->exists()) {
            $adminUser->syncRoles(['Admin']);
        }

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );
    }
}
