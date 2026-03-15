<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'manage users',
            'manage roles',
            'manage permissions',
            'view finance',
            'manage finance',
            'view invoices',
            'manage invoices',
            'view payments',
            'manage payments',
            'view inventory',
            'manage inventory',
            'view products',
            'manage products',
            'view suppliers',
            'manage suppliers',
            'view sales',
            'manage sales',
            'view quotations',
            'manage quotations',
            'view hr',
            'manage hr',
            'view employees',
            'manage employees',
            'view payroll',
            'manage payroll',
            'view procurement',
            'manage procurement',
            'view reports',
            'manage reports',
        ];

        foreach ($permissions as $permissionName) {
            Permission::query()->firstOrCreate(['name' => $permissionName]);
        }
    }
}
