<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Catalog
            'view-products',
            'manage-products',
            'manage-categories',

            // Order
            'place-order',
            'view-own-orders',
            'cancel-own-order',

            // Vendor
            'view-own-vendor-orders',
            'fulfill-own-orders',
            'view-own-earnings',

            // Admin
            'approve-vendors',
            'view-all-orders',
            'manage-users',
            'view-stats',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        $customer = Role::findOrCreate('customer', 'web');
        $customer->syncPermissions([
            'view-products',
            'place-order',
            'view-own-orders',
            'cancel-own-order',
        ]);

        $vendor = Role::findOrCreate('vendor', 'web');
        $vendor->syncPermissions([
            'view-products',
            'place-order',
            'view-own-orders',
            'cancel-own-order',
            'manage-products',
            'view-own-vendor-orders',
            'fulfill-own-orders',
            'view-own-earnings',
        ]);

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions(Permission::all());
    }
}
