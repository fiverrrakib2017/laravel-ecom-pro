<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Customer Management
            'customer.view',
            'customer.create',
            'customer.edit',
            'customer.delete',
            'customer.restore',
            'customer.logs.view',
            'customer.import',
            'customer.import.mikrotik',
            'menu.access.customers',

            // Hotspot Management
            'hotspot.user.view',
            'hotspot.user.active.view',
            'hotspot.user.recharge',
            'hotspot.user.expired.view',
            'hotspot.user.usage.view',
            'hotspot.voucher.manage',
            'menu.access.hotspot',

            // Billing & Payments
            'payment.history.view',
            'payment.bulk.recharge',
            'payment.credit.recharge.list',
            'payment.upcoming.expiry.view',
            'menu.access.billing_payments',

            // Packages
            'package.view',
            'ip_pool.view',
            'menu.access.customer_package',

            // Network Diagram
            'network.diagram.view',
            'menu.access.network_diagram',

            // POP/Branch
            'pop_branch.view',
            'pop_branch.area.view',
            'menu.access.pop_branch',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }
    }
}

