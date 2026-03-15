<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'Admin' => Permission::query()->pluck('name')->all(),
            'Finance Manager' => [
                'view dashboard',
                'view finance',
                'manage finance',
                'view invoices',
                'manage invoices',
                'view payments',
                'manage payments',
                'view reports',
            ],
            'HR Manager' => [
                'view dashboard',
                'view hr',
                'manage hr',
                'view employees',
                'manage employees',
                'view payroll',
                'manage payroll',
                'view reports',
            ],
            'Inventory Manager' => [
                'view dashboard',
                'view inventory',
                'manage inventory',
                'view products',
                'manage products',
                'view suppliers',
                'manage suppliers',
                'view reports',
            ],
            'Sales Manager' => [
                'view dashboard',
                'view sales',
                'manage sales',
                'view quotations',
                'manage quotations',
                'view reports',
            ],
            'Procurement Manager' => [
                'view dashboard',
                'view procurement',
                'manage procurement',
                'view reports',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::query()->firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }
    }
}
