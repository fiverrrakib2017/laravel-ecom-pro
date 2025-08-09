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

            // Ticket Management
            'ticket.list.view',
            'ticket.complain_type.manage',
            'ticket.assign.manage',
            'menu.access.tickets',

            // SMS Management
            'sms.send',
            'sms.bulk.send',
            'sms.template.manage',
            'sms.logs.view',
            'sms.report.view',
            'sms.config.manage',
            'menu.access.sms',

            // HR Management - Employee
            'hr.employee.view',
            'hr.employee.create',
            'hr.employee.edit',
            'hr.employee.delete',

            // Leave Management
            'hr.employee.leave.view',

            // Attendance
            'hr.attendance.view',
            'hr.attendance.report.view',

            // Salary Management
            'hr.salary.view',

            // Advance Salary
            'hr.salary.advance.view',
            'hr.salary.advance.report.view',

            // Loans
            'hr.loan.view',
            'hr.loan.create',
            'hr.loan.edit',
            'hr.loan.show',

            // Payroll
            'hr.payroll.view',
            'hr.payroll.create',

            // Designation
            'hr.designation.view',

            // Department
            'hr.department.view',

            // Shift Management
            'hr.shift.view',

            // Other HR actions
            'hr.promotion.manage',
            'hr.transfer.manage',
            'hr.resignation.manage',
            'hr.performance.manage',
            'hr.training.manage',
            'hr.notice_board.manage',

            // Menu Access
            'menu.access.hr_management',



            // ================= Inventory =================
            'inventory.sale.view', // Sale
            'inventory.sale_invoice.view', // Sale Invoice
            'inventory.purchase.view', // Purchase
            'inventory.purchase_invoice.view', // Purchase Invoice
            'inventory.brand.view',
            'inventory.category.view',
            'inventory.unit.view',
            'inventory.store.view',
            'inventory.product.view',
            'inventory.supplier.view',
            'inventory.client.view',

            // Menu Access
            'menu.access.inventory',

            // ================= Accounts =================
            'accounts.list.view',
            'accounts.transaction.view',
            'accounts.ledger.view',
            'accounts.trial_balance.view',
            'accounts.profit_loss.view',
            'accounts.balance_sheet.view',

            // Menu Access
            'menu.access.accounts',


            // ================= Settings =================
            'settings.information.view', // Application Information
            'settings.password.change',  // Change Password
            'menu.access.settings',

            // ================= Server Management =================
            'server.router.add',
            'server.router.sync',
            'server.nas.view',
            'menu.access.server',

            // ================= User Management =================
            'user.view',
            'role.view',
            'permission.view',
            'menu.access.user_management',


        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }
    }
}

