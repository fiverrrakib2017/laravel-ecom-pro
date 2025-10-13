<?php

use App\Http\Controllers\Backend\Accounts\Account_controller;
use App\Http\Controllers\Backend\Accounts\Balance_sheet_controller;
use App\Http\Controllers\Backend\Accounts\Income_controller;
use App\Http\Controllers\Backend\Accounts\Ledger_controller;
use App\Http\Controllers\Backend\Accounts\Transaction_controller;
use App\Http\Controllers\Backend\Accounts\Trial_balance_controller;
use App\Http\Controllers\Backend\Admin\AdminController;
use App\Http\Controllers\Backend\Client\Client_invoiceController;
use App\Http\Controllers\Backend\Customer\CustomerController;
use App\Http\Controllers\Backend\Supplier\Supplier_invoiceController;
use App\Http\Controllers\Backend\Supplier\Supplier_returnController;
use App\Http\Controllers\Backend\Customer\InvoiceController;
use App\Http\Controllers\Backend\Customer\PackageController;
use App\Http\Controllers\Backend\Customer\PoolController;
use App\Http\Controllers\Backend\Customer\TicketController;
use App\Http\Controllers\Backend\Pop\PopController;
use App\Http\Controllers\Backend\Pop\Area\AreaController;
use App\Http\Controllers\Backend\Product\BrandController;
use App\Http\Controllers\Backend\Product\CategoryController;
use App\Http\Controllers\Backend\Product\SubCateogryController;
use App\Http\Controllers\Backend\Product\ColorController;
use App\Http\Controllers\Backend\Product\ProductController;
use App\Http\Controllers\Backend\Product\TempImageController;
use App\Http\Controllers\Backend\Product\ChildCategoryController;
use App\Http\Controllers\Backend\Product\SizeController;
use App\Http\Controllers\Backend\Product\StockController;
use App\Http\Controllers\Backend\Product\StoreController;
use App\Http\Controllers\Backend\Product\UnitController;
use App\Http\Controllers\Backend\Router\RouterController;
use App\Http\Controllers\Backend\Settings\Others\SettingsController;
use App\Http\Controllers\Backend\Sms\SmsController;
use App\Http\Controllers\Backend\Client\ClientController;
use App\Http\Controllers\Backend\Hrm\Attendance_controller;
use App\Http\Controllers\Backend\Hrm\Department_controller;
use App\Http\Controllers\Backend\Hrm\Designation_controller;
use App\Http\Controllers\Backend\Hrm\Employee_controller;
use App\Http\Controllers\Backend\Hrm\Leave_controller;
use App\Http\Controllers\Backend\Hrm\Loan_controller;
use App\Http\Controllers\Backend\Hrm\Payroll_controller;
use App\Http\Controllers\Backend\Hrm\Salary_controller;
use App\Http\Controllers\Backend\Supplier\SupplierController;
use App\Http\Controllers\Backend\Tickets\Assign_controller;
use App\Http\Controllers\Backend\Tickets\Complain_typeController;
use App\Http\Controllers\Backend\Hrm\Shift_controller;
use App\Http\Controllers\Backend\Olt\Olt_controller;
use App\Http\Controllers\Backend\Onu\Onu_controller;
use App\Http\Controllers\Backend\Tickets\Ticket_controller;
use App\Http\Controllers\Backend\Admin\UserController;
use App\Http\Controllers\Backend\Role\RoleController;
use App\Models\Product_Category;
use App\Models\Router;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use RouterOS\Client;
use RouterOS\Query;
use function App\Helpers\formate_uptime;
/*Backend Route*/
Route::get('/admin/login', [AdminController::class, 'login_form'])->name('admin.login');
Route::post('login-functionality', [AdminController::class, 'login_functionality'])->name('login.functionality');
Route::group(['middleware' => 'admin'], function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::post('/admin/get_dashboard_data', [AdminController::class, 'get_data'])->name('admin.dashboard_get_all_data');
    Route::get('/server-information', [AdminController::class, 'server_info'])->name('admin.server_info');

    /** Tickets  Route **/
    Route::prefix('admin/ticket')->group(function () {
        /*Complain Type */
        Route::prefix('complain_type')->group(function () {
            Route::controller(Complain_typeController::class)->group(function () {
                Route::get('/list', 'index')->name('admin.tickets.complain_type.index');
                Route::get('/all-data', 'get_all_data')->name('admin.tickets.complain_type.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.tickets.complain_type.edit');
                Route::post('/delete', 'delete')->name('admin.tickets.complain_type.delete');
                Route::post('/store', 'store')->name('admin.tickets.complain_type.store');
                Route::post('/update/{id}', 'update')->name('admin.tickets.complain_type.update');
            });
        });
        /*Assign To */
        Route::prefix('assign')->group(function () {
            Route::controller(Assign_controller::class)->group(function () {
                Route::get('/list', 'index')->name('admin.tickets.assign.index');
                Route::get('/all-data', 'get_all_data')->name('admin.tickets.assign.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.tickets.assign.edit');
                Route::post('/delete', 'delete')->name('admin.tickets.assign.delete');
                Route::post('/store', 'store')->name('admin.tickets.assign.store');
                Route::post('/update/{id}', 'update')->name('admin.tickets.assign.update');
            });
        });
        /*Ticket Route To */
        Route::controller(Ticket_controller::class)->group(function () {
            Route::get('/list', 'index')->name('admin.tickets.index');
            Route::get('/all-data', 'get_all_data')->name('admin.tickets.get_all_data');
            Route::get('/edit/{id}', 'edit')->name('admin.tickets.edit');
            Route::get('/view/{id}', 'view')->name('admin.tickets.view');
            Route::post('/delete', 'delete')->name('admin.tickets.delete');
            Route::post('/store', 'store')->name('admin.tickets.store');
            Route::post('/update/{id}', 'update')->name('admin.tickets.update');
            Route::post('/change_status/{id}', 'change_status')->name('admin.tickets.change_status');
            Route::get('/get_tickets/{id}', 'get_customer_tickets')->name('admin.tickets.get_customer_tickets');
            Route::post('/add_ticekts_activity', 'add_ticekts_activity')->name('admin.tickets.add_ticekts_activity');
        });
    });


    /** Customer Route **/
    Route::prefix('admin/customer')->group(function () {
        Route::controller(CustomerController::class)->group(function () {
            Route::get('/create', 'create')->name('admin.customer.create');
            Route::get('/list', 'index')->name('admin.customer.index');
            Route::get('/all-data', 'get_all_data')->name('admin.customer.get_all_data');
            Route::get('/operation', 'customer_operation')->name('admin.customer.customer_operation');
            Route::get('/edit/{id}', 'edit')->name('admin.customer.edit');
            Route::get('/view/{id}', 'view')->name('admin.customer.view');
            Route::post('/delete', 'delete')->name('admin.customer.delete');
            Route::post('/bulk/delete', 'bulk_customer_delete')->name('admin.customer.bulk.delete');
            Route::post('/forge_delete', 'forge_delete')->name('admin.customer.forge_delete');
            Route::post('/check-username', 'check_customer_user')->name('admin.customer.check.username');
            Route::get('/search', 'customer_search')->name('admin.customer.search');

            Route::post('/store', 'store')->name('admin.customer.store');
            Route::post('/update/{id}', 'update')->name('admin.customer.update');
            Route::post('/change_expire_date', 'customer_change_expire_date')->name('admin.customer.expire_date.update');
            Route::post('/change_package', 'customer_change_pacakge')->name('admin.customer.bulk.package.update');
            Route::post('/bulk-re-connect', 'bulk_customer_re_connect')->name('admin.customer.bulk.re.connect');
            Route::post('/get_customer_info', 'get_customer_info')->name('admin.customer.get_customer_info');

            /*Customer Onu List*/
            Route::get('/get_customer_onu_list', 'onu_list')->name('admin.customer.onu_list');
            Route::get('/get_customer_onu_list_data', 'get_customer_onu_list_data')->name('admin.customer.get_customer_onu_list_data');

            /***** Customer Discountinue *******/
            Route::get('/discountinue/{customer_id}', 'customer_discountinue')->name('admin.customer.discountinue');
            /***** Customer Recharge *******/
            Route::post('/recharge/store', 'customer_recharge')->name('admin.customer.recharge.store');
            Route::get('/recharge/undo/{id}', 'customer_recharge_undo')->name('admin.customer.recharge.undo');
            Route::get('/recharge/print/{id}', 'customer_recharge_print')->name('admin.customer.recharge.print');
            Route::get('/recharge/bulk-recharge', 'customer_bulk_recharge')->name('admin.customer.bulk.recharge');
            Route::post('/recharge/bulk-recharge-store', 'customer_bulk_recharge_store')->name('admin.customer.bulk.recharge.store');
            Route::post('/recharge/grace-recharge-store', 'customer_grace_recharge_store')->name('admin.customer.grace.recharge.store');
            Route::get('/recharge/grace-recharge/remove/{customer_id}', 'customer_grace_recharge_remove')->name('admin.customer.grace.recharge.remove');

            /***** Customer comming expire *******/
            Route::get('/comming-expire', 'customer_comming_expire')->name('admin.customer.customer_comming_expire');

            /***** Customer Device expire *******/
            Route::get('/device/return/{id}', 'customer_device_return')->name('admin.customer.device.return');

            /***** Customer Payment History *******/
            Route::get('/payment/history', 'customer_payment_history')->name('admin.customer.payment.history');
            Route::get('/payment/history/get_all_data', 'customer_payment_history_get_all_data')->name('admin.customer.payment.history.get_all_data');
            Route::get('/bill/generate', 'customer_bill_generate')->name('admin.customer.bill.generate');
            /***** Customer Grace Recharge Logs *******/
            Route::get('/grace-recharge/logs', 'customer_grace_recharge_logs')->name('admin.customer.grace_recharge.logs');
            Route::get('/grace-recharge/logs/get_all_data', 'customer_grace_recharge_logs_get_all_data')->name('admin.customer.grace_recharge.logs.get_all_data');
            /***** Customer Log *******/
            Route::get('/customer/log', 'customer_log')->name('admin.customer.log.index');
            Route::get('/customer/log/get_all_data', 'customer_log_get_all_data')->name('admin.customer.log.get_all_data');
            /***** Customer Credit Recharge List *******/
            Route::get('/credit/recharge/list', 'customer_credit_recharge_list')->name('admin.customer.customer_credit_recharge_list');
            Route::get('/credit/recharge/get_all_data', 'show_credit_recharge_list_data')->name('admin.customer.show_credit_recharge_list_data');
            /***** Customer Backup *******/
            Route::get('/customer/restore', 'customer_restore')->name('admin.customer.restore.index');
            Route::get('/customer/restore/get_all_data', 'customer_restore_get_all_data')->name('admin.customer.restore.get_all_data');
            Route::post('/customer/restore/back', 'customer_restore_back')->name('admin.customer.restore.back');
            /***** Customer Import *******/
            Route::get('/import/index', 'customer_import')->name('admin.customer.customer_import');
            Route::post('/import/store', 'customer_csv_file_import')->name('admin.customer.customer_csv_file_import');
            Route::get('/delete-csv-file/{file}', [CustomerController::class, 'delete_csv_file'])->name('admin.customer.delete_csv_file');
            Route::get('/upload/csv-file', [CustomerController::class, 'upload_csv_file'])->name('admin.customer.upload_csv_file');
            Route::get('/import/mikrotik', 'customer_import_from_mikrotik')->name('admin.customer.import.mikrotik');
            Route::post('/import/mikrotik/store', 'customer_import_from_mikrotik_store')->name('admin.customer.import.mikrotik.store');
            /***** Customer Mikrotik Re-connect *******/
            Route::get('/mikrotik/reconnect/{customer_id}', 'customer_mikrotik_reconnect')->name('admin.customer.mikrotik.reconnect');
            /***** Customer Change Status *******/
            Route::post('/change/status', 'customer_change_status')->name('admin.customer.change_status');
            /***** Customer Live Bandwith With Her Profile *******/
            // Route::get('/live-bandwith-update/{customer_id}', 'customer_live_bandwith_update')->name('admin.customer.live_bandwith_update');
             /***** Onu Information *******/
             Route::post('/get-onu-information', 'get_onu_info')->name('admin.customer.get_onu_info');
             /*****Get Router name *******/
             Route::post('/get_router_name', 'get_router_name')->name('admin.customer.router.vendor');
        });
        /* IP POOL Route */
        Route::prefix('ip-pool')->group(function () {
            Route::controller(PoolController::class)->group(function () {
                Route::get('/list', 'index')->name('admin.customer.ip_pool.index');
                Route::get('/all-data', 'get_all_data')->name('admin.customer.ip_pool.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.customer.ip_pool.edit');
                Route::get('/view/{id}', 'pop_view')->name('admin.customer.ip_pool.view');
                Route::post('/delete', 'delete')->name('admin.customer.ip_pool.delete');
                Route::post('/store', 'store')->name('admin.customer.ip_pool.store');
                Route::post('/update/{id}', 'update')->name('admin.customer.ip_pool.update');
            });
        });
        /* Package Route */
        Route::prefix('package')->group(function () {
            Route::controller(PackageController::class)->group(function () {
                Route::get('/list', 'index')->name('admin.customer.package.index');
                Route::get('/all-data', 'get_all_data')->name('admin.customer.package.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.customer.package.edit');
                Route::get('/view/{id}', 'pop_view')->name('admin.customer.package.view');
                Route::post('/delete', 'delete')->name('admin.customer.package.delete');
                Route::post('/store', 'store')->name('admin.customer.package.store');
                Route::post('/update/{id}', 'update')->name('admin.customer.package.update');
            });
        });
    });
    /** CRM Route **/
    Route::prefix('admin/customer-relationship-management')->group(function () {
        /*Lead Route */
        Route::prefix('lead')->group(function () {
            Route::controller(App\Http\Controllers\Backend\Customer\LeadController::class)->group(function () {
                Route::get('/list', 'index')->name('admin.customer.lead.index');

                Route::get('/create', 'create')->name('admin.customer.lead.create');
                Route::post('/delete', 'delete')->name('admin.customer.lead.delete');
                Route::post('/store', 'store')->name('admin.customer.lead.store');

                Route::get('/edit/{id}', 'edit')->name('admin.customer.lead.edit');
                Route::get('/view/{id}', 'view')->name('admin.customer.lead.view');
                Route::post('/update/{id}', 'update')->name('admin.customer.lead.update');
            });
        });
        /*Deal Stage Route */
        Route::prefix('deal-stages')->group(function () {
            Route::controller(App\Http\Controllers\Backend\Customer\Deal_stageController::class)->group(function () {
                Route::get('/list', 'index')->name('admin.customer.deal_stages.index');

                Route::get('/create', 'create')->name('admin.customer.deal_stages.create');
                Route::post('/delete', 'delete')->name('admin.customer.deal_stages.delete');
                Route::post('/store', 'store')->name('admin.customer.deal_stages.store');

                Route::get('/edit/{id}', 'edit')->name('admin.customer.deal_stages.edit');
                Route::post('/update/{id}', 'update')->name('admin.customer.deal_stages.update');
            });
        });
        /*Deal Route */
        Route::prefix('deals')->group(function () {
            Route::controller(App\Http\Controllers\Backend\Customer\DealController::class)->group(function () {
                Route::get('/list', 'index')->name('admin.customer.deals.index');

                Route::get('/create', 'create')->name('admin.customer.deals.create');
                Route::post('/delete', 'delete')->name('admin.customer.deals.delete');
                Route::post('/store', 'store')->name('admin.customer.deals.store');

                Route::get('/edit/{id}', 'edit')->name('admin.customer.deals.edit');
                Route::get('/view/{id}', 'view')->name('admin.customer.deals.view');
                Route::post('/update/{id}', 'update')->name('admin.customer.deals.update');
            });
        });
        /*Client Route */
        Route::get('/list',[ClientController::class, 'index'])->name('admin.customer.lead.client.index');
    });
    /** Hotspot Route **/
    Route::prefix('admin/hotspot')->group(function () {
        Route::controller(App\Http\Controllers\Backend\Hotspot\HotspotController::class)->group(function () {
            /*--------Hotspot Profile Route-----------**/
            Route::get('/dashbaord', 'hotspot_dashbaord')->name('admin.hotspot.user.dashbaord');
            Route::get('/profile/create', 'hotspot_profile_create')->name('admin.hotspot.profile.create');
            Route::post('/profile/store', 'hotspot_profile_store')->name('admin.hotspot.profile.store');
            Route::get('/profile/edit/{id}', 'hotspot_profile_edit')->name('admin.hotspot.profile.edit');
            Route::put('/profiles/{id}', 'hotspot_profile_update')->name('admin.hotspot.profile.update');
            Route::delete('profiles/{id}', 'hotspot_profile_destroy')->name('admin.hotspot.profile.destroy');
            Route::get('/profile/list', 'hotspot_profile_index')->name('admin.hotspot.profile.index');
            Route::get('/profile/{id}', 'profilesByRouter')->name('admin.hotspot.profile.get_profile');
            /*--------Hotspot Users Route-----------**/
            Route::controller(App\Http\Controllers\Backend\Hotspot\HotspotUserController::class)->group(function () {
                Route::get('/user/index', 'hotspot_user_index')->name('admin.hotspot.user.index');
                Route::get('/user/create', 'hotspot_user_create')->name('admin.hotspot.user.create');
                Route::post('/user/store', 'hotspot_user_store')->name('admin.hotspot.user.store');

                Route::get('/user/edit/{id}', 'hotspot_user_edit')->name('admin.hotspot.user.edit');
                Route::put('/user/{id}', 'hotspot_user_update')->name('admin.hotspot.user.update');
                Route::delete('user/{id}', 'hotspot_user_destroy')->name('admin.hotspot.user.destroy');

                /*------Bulk User Create-------*/
                Route::get('/user/bulk/create', 'hotspot_user_bulk_create')->name('admin.hotspot.user.bulk.create');
                Route::post('/user/bulk/store', 'hotspot_user_bulk_store')->name('admin.hotspot.user.bulk.store');
                /*------Bulk import-------*/
                Route::get('/user/bulk/import', 'hotspot_user_bulk_import')->name('admin.hotspot.user.bulk.import');
                Route::post('/user/bulk/import/store', 'hotspot_user_bulk_import_store')->name('admin.hotspot.user.bulk.import.store');
            });

            /*--------Hotspot Batch Route-----------**/
            Route::controller(App\Http\Controllers\Backend\Hotspot\BatchController::class)->group(function () {
                Route::get('/voucher/batches', 'index')->name('admin.hotspot.vouchers.batch.index');
                Route::get('/voucher/batches/create', 'create')->name('admin.hotspot.vouchers.batch.create');
                Route::post('/voucher/batches', 'store')->name('admin.hotspot.vouchers.batch.store');
            });
            /*--------Hotspot Voucher Route-----------**/
            Route::controller(App\Http\Controllers\Backend\Hotspot\VoucherController::class)->group(function () {
                // Print
                Route::get('/vouchers/print', 'print')->name('admin.hotspot.vouchers.print');
                // Sales
                Route::get('/vouchers/sales', 'sales')->name('admin.hotspot.vouchers.sales');
                // Export CSV
                Route::post('/vouchers/export', 'export')->name('admin.hotspot.vouchers.export');
            });

        });
    });

    /** Supplier Route **/
    Route::prefix('admin/supplier')->group(function () {
        Route::controller(SupplierController::class)->group(function () {
            Route::get('/list', 'index')->name('admin.supplier.index');
            Route::get('/all-data', 'get_all_data')->name('admin.supplier.get_all_data');
            Route::get('/create', 'create')->name('admin.supplier.create');
            Route::get('/edit/{id}', 'edit')->name('admin.supplier.edit');
            Route::get('/view/{id}', 'view')->name('admin.supplier.view');
            Route::post('/delete', 'delete')->name('admin.supplier.delete');
            Route::post('/store', 'store')->name('admin.supplier.store');
            Route::post('/update/{id}', 'update')->name('admin.supplier.update');
        });
        /** Supplier Invoice Route **/
        Route::prefix('invoice')
            ->controller(Supplier_invoiceController::class)
            ->group(function () {
                Route::get('/create', 'create_invoice')->name('admin.supplier.invoice.create_invoice');
                Route::get('/get_all_data', 'show_invoice_data')->name('admin.supplier.invoice.show_invoice_data');
                Route::post('/search_data', 'search_product_data')->name('admin.supplier.invoice.search_product_data');
                Route::get('/show', 'show_invoice')->name('admin.supplier.invoice.show_invoice');
                Route::post('/pay', 'pay_due_amount')->name('admin.supplier.invoice.pay_due_amount');
                Route::post('/store', 'store_invoice')->name('admin.supplier.invoice.store_invoice');
                Route::get('/view/{id}', 'view_invoice')->name('admin.supplier.invoice.view_invoice');
                Route::get('/edit/{id}', 'edit_invoice')->name('admin.supplier.invoice.edit_invoice');
                Route::post('/update', 'update_invoice')->name('admin.supplier.invoice.update_invoice');
                Route::post('/delete', 'delete_invoice')->name('admin.supplier.invoice.delete_invoice');
            });
    });
    /** Client Route **/
    Route::prefix('admin/client')->group(function () {
        Route::controller(ClientController::class)->group(function () {
            Route::get('/list', 'index')->name('admin.client.index');
            Route::get('/all-data', 'get_all_data')->name('admin.client.get_all_data');
            Route::get('/create', 'create')->name('admin.client.create');
            Route::get('/edit/{id}', 'edit')->name('admin.client.edit');
            Route::get('/view/{id}', 'view')->name('admin.client.view');
            Route::post('/delete', 'delete')->name('admin.client.delete');
            Route::post('/store', 'store')->name('admin.client.store');
            Route::post('/update/{id}', 'update')->name('admin.client.update');
        });
        /** Client Invoice Route **/
        Route::prefix('invoice')
            ->controller(Client_invoiceController::class)
            ->group(function () {
                Route::get('/create', 'create_invoice')->name('admin.client.invoice.create_invoice');
                Route::get('/get_all_data', 'show_invoice_data')->name('admin.client.invoice.show_invoice_data');
                Route::post('/search_data', 'search_product_data')->name('admin.client.invoice.search_product_data');
                Route::get('/show', 'show_invoice')->name('admin.client.invoice.show_invoice');
                Route::post('/pay', 'pay_due_amount')->name('admin.client.invoice.pay_due_amount');
                Route::post('/store', 'store_invoice')->name('admin.client.invoice.store_invoice');
                Route::get('/view/{id}', 'view_invoice')->name('admin.client.invoice.view_invoice');
                Route::get('/edit/{id}', 'edit_invoice')->name('admin.client.invoice.edit_invoice');
                Route::post('/update', 'update_invoice')->name('admin.client.invoice.update_invoice');
                Route::post('/delete', 'delete_invoice')->name('admin.client.invoice.delete_invoice');
            });
    });

    /* Product Route */
    Route::prefix('admin/product')->group(function () {
        /* Sub Category Route */
        Route::prefix('sub-category')
            ->controller(SubCateogryController::class)
            ->group(function () {
                Route::get('/', 'index')->name('admin.subcategory.index');
                Route::post('/store', 'store')->name('admin.subcategory.store');
                Route::get('/edit/{id}', 'edit')->name('admin.subcategory.edit');
                Route::post('/delete', 'delete')->name('admin.subcategory.delete');
                Route::post('/update/{id}', 'update')->name('admin.subcategory.update');
                Route::get('/get-sub_category/{id}', 'get_sub_category');
            });

        /* Child Category Route */
        Route::prefix('child-category')
            ->controller(ChildCategoryController::class)
            ->group(function () {
                Route::get('/', 'index')->name('admin.childcategory.index');
                Route::post('/store', 'store')->name('admin.childcategory.store');
                Route::get('/edit/{id}', 'edit')->name('admin.childcategory.edit');
                Route::post('/delete', 'delete')->name('admin.childcategory.delete');
                Route::post('/update/{id}', 'update')->name('admin.childcategory.update');
                Route::get('/get-child_category/{id}', 'get_child_category');
            });

        /** Product Color Management Route **/
        Route::prefix('color')
            ->controller(ColorController::class)
            ->group(function () {
                Route::get('/', 'index')->name('admin.product.color.index');
                Route::get('/get_all_data', 'get_all_data')->name('admin.product.color.all_data');
                Route::post('/store', 'store')->name('admin.product.color.store');
                Route::get('/edit/{id}', 'edit')->name('admin.product.color.edit');
                Route::post('/update', 'update')->name('admin.product.color.update');
                Route::post('/delete', 'delete')->name('admin.product.color.delete');
            });

        /** Product Size Management Route **/
        Route::prefix('size')
            ->controller(SizeController::class)
            ->group(function () {
                Route::get('/', 'index')->name('admin.product.size.index');
                Route::get('/get_all_data', 'get_all_data')->name('admin.product.size.all_data');
                Route::post('/store', 'store')->name('admin.product.size.store');
                Route::get('/edit/{id}', 'edit')->name('admin.product.size.edit');
                Route::post('/update', 'update')->name('admin.product.size.update');
                Route::post('/delete', 'delete')->name('admin.product.size.delete');
            });
        /** Product Unit Management Route **/
        Route::prefix('unit')
            ->controller(UnitController::class)
            ->group(function () {
                Route::get('/list', 'index')->name('admin.unit.index');
                Route::get('/all-data', 'get_all_data')->name('admin.unit.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.unit.edit');
                Route::post('/delete', 'delete')->name('admin.unit.delete');
                Route::post('/store', 'store')->name('admin.unit.store');
                Route::post('/update/{id}', 'update')->name('admin.unit.update');
            });
        /** Product Category Management Route **/
        Route::prefix('category')
            ->controller(CategoryController::class)
            ->group(function () {
                Route::get('/list', 'index')->name('admin.category.index');
                Route::get('/all-data', 'get_all_data')->name('admin.category.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.category.edit');
                Route::post('/delete', 'delete')->name('admin.category.delete');
                Route::post('/store', 'store')->name('admin.category.store');
                Route::post('/update/{id}', 'update')->name('admin.category.update');
            });
        /** Product Brand Management Route **/
        Route::prefix('brand')
            ->controller(BrandController::class)
            ->group(function () {
                Route::get('/list', 'index')->name('admin.brand.index');
                Route::get('/all-data', 'get_all_data')->name('admin.brand.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.brand.edit');
                Route::post('/delete', 'delete')->name('admin.brand.delete');
                Route::post('/store', 'store')->name('admin.brand.store');
                Route::post('/update/{id}', 'update')->name('admin.brand.update');
            });
        /** Product Store Management Route **/
        Route::prefix('store')
            ->controller(StoreController::class)
            ->group(function () {
                Route::get('/list', 'index')->name('admin.store.index');
                Route::get('/all-data', 'get_all_data')->name('admin.store.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.store.edit');
                Route::post('/delete', 'delete')->name('admin.store.delete');
                Route::post('/store', 'store')->name('admin.store.store');
                Route::post('/update/{id}', 'update')->name('admin.store.update');
            });

        /* Product Route */
        Route::controller(ProductController::class)->group(function () {
            Route::get('/list', 'index')->name('admin.product.index');
            Route::get('/all-data', 'get_all_data')->name('admin.product.get_all_data');
            Route::get('/edit/{id}', 'edit')->name('admin.product.edit');
            Route::get('/view/{id}', 'product_view')->name('admin.product.view');
            Route::post('/delete', 'delete')->name('admin.product.delete');
            Route::post('/store', 'store')->name('admin.product.store');
            Route::post('/update/{id}', 'update')->name('admin.product.update');
            Route::post('/check_product_qty', 'check_product_qty')->name('admin.product.check_product_qty');
        });

        /* Product Image */
        Route::prefix('photo')
            ->controller(ProductController::class)
            ->group(function () {
                Route::post('/upload-temp-image', [TempImageController::class, 'create'])->name('tempimage.create');
                Route::post('/update', 'photo_update')->name('admin.product.photo.update');
                Route::post('/delete', 'delete_photo')->name('admin.product.delete.photo');
            });

        /* Stock Route */
        Route::get('/stock', [StockController::class, 'index'])->name('admin.product.stock.index');
    });
    /*  POP/Branch Route */
    Route::prefix('admin/pop-branch')->group(function () {
        /* POP/BRANCH Route */
        Route::controller(PopController::class)->group(function () {
            Route::get('/list', 'index')->name('admin.pop.index');
            Route::get('/all-data', 'get_all_data')->name('admin.pop.get_all_data');
            Route::get('/edit/{id}', 'edit')->name('admin.pop.edit');
            Route::get('/view/{id}', 'view')->name('admin.pop.view');
            Route::post('/delete', 'delete')->name('admin.pop.delete');
            Route::post('/store', 'store')->name('admin.pop.store');
            Route::post('/update', 'update')->name('admin.pop.update');

            Route::post('/change/status/{id}', 'pop_change_status')->name('admin.pop.change_status');

            /*****Branch Package *******/
            Route::post('/package/store', 'branch_package_store')->name('admin.pop.brnach.package.store');
            Route::get('/package/view/{id}', 'branch_package_edit')->name('admin.pop.branch.package.edit');
            Route::post('/package/update/{id}', 'branch_package_update')->name('admin.pop.branch.package.update');
            Route::post('/package/delete', 'branch_package_delete')->name('admin.pop.branch_package_delete');
            /*GET POP/BRANCH Area */
            Route::get('/package/{id}', 'get_pop_wise_package')->name('admin.pop.branch.get_pop_wise_package');
            Route::get('/package/price/{id}', 'get_pop_wise_package_price')->name('admin.pop.branch.get_pop_wise_package_price');

            /*****Branch Recharge *******/
            Route::post('/recharge/store', 'branch_recharge_store')->name('admin.pop.brnach.recharge.store');
            Route::get('/recharge/undo/{id}', 'branch_recharge_undo')->name('admin.pop.brnach.recharge.undo');
            /*****Branch Auto Login  *******/
            Route::get('/branch/login/{pop_id}', [PopController::class, 'auto_login'])->name('admin.pop.branch.auto_login');


        });

        /* POP/Area Route */
        Route::prefix('area')->group(function () {
            Route::controller(AreaController::class)->group(function () {
                Route::get('/list', 'index')->name('admin.pop.area.index');
                Route::get('/all-data', 'get_all_data')->name('admin.pop.area.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.pop.area.edit');
                Route::get('/view/{id}', 'view')->name('admin.pop.area.view');
                Route::post('/delete', 'delete')->name('admin.pop.area.delete');
                Route::post('/store', 'store')->name('admin.pop.area.store');
                Route::post('/update/{id}', 'update')->name('admin.pop.area.update');

                /*GET POP/BRANCH Area */
                Route::get('/pop-brnach/{id}', 'get_pop_wise_area')->name('admin.pop.area.get_pop_wise_area');
                /* Change Area Status */
                Route::post('/change/status/{id}', 'area_change_status')->name('admin.pop.area.change_status');
            });
        });
    });
    /* OLT Management Route */
    Route::prefix('admin/olt-management')->group(function () {
        /* OLT Device  Route */
        Route::controller(Olt_controller::class)->group(function () {
            Route::get('/list', 'index')->name('admin.olt.index');
            Route::get('/create', 'create')->name('admin.olt.create');

            Route::get('/edit/{id}', 'edit')->name('admin.olt.edit');
            Route::get('/update', 'update')->name('admin.olt.update');

            Route::post('/delete', 'delete')->name('admin.olt.delete');

            Route::post('/store', 'store')->name('admin.olt.store');
        });
        /* ONT Device  Route */
        Route::controller(Onu_controller::class)->group(function () {
            Route::get('/onu-list', 'index')->name('admin.onu.index');
        });
    });
    /* SMS Management Route */
    Route::prefix('admin/sms')->group(function () {
        /* SMS Configration Route */
        Route::prefix('configration')->group(function () {
            Route::controller(SmsController::class)->group(function () {
                Route::get('/config', 'config')->name('admin.sms.config');
                Route::post('/config_store', 'config_store')->name('admin.sms.config.store');
            });
        });
        /*SMS Logs*/
         Route::prefix('logs')->group(function () {
            Route::controller(SmsController::class)->group(function () {
                Route::get('/index', 'sms_logs')->name('admin.sms.logs');
                Route::get('/get_all_sms_logs_data', 'get_all_sms_logs_data')->name('admin.sms.get_all_sms_logs_data');
            });
        });
        /*SMS Report*/
         Route::prefix('report')->group(function () {
            Route::controller(SmsController::class)->group(function () {
                Route::get('/index', 'sms_report')->name('admin.sms.report');
            });
        });
        /* SMS Template Route */
        Route::prefix('template')->group(function () {
            Route::controller(SmsController::class)->group(function () {
                Route::get('/list', 'sms_template_list')->name('admin.sms.template_list');
                Route::get('/get_all_data', 'sms_template_get_all_data')->name('admin.sms.template_get_all_data');
                Route::post('/Store', 'sms_template_Store')->name('admin.sms.template_Store');
                Route::post('/delete', 'sms_template_delete')->name('admin.sms.template_delete');
                Route::get('/get/{id}', 'sms_template_get')->name('admin.sms.template_get');
                Route::post('/update', 'sms_template_update')->name('admin.sms.template_update');
            });
        });
        /* Auto SMS Template Route */
        Route::prefix('/auto/message/template')->group(function () {
            Route::controller(SmsController::class)->group(function () {
                Route::post('/store', 'send_auto_message_template_store')->name('admin.sms.auto.template.store');
                Route::post('/send/test/message', 'send_test_message')->name('admin.sms.send_test_message');
            });
        });
        /* Send SMS Template Route */
        Route::prefix('send_message')->group(function () {
            Route::controller(SmsController::class)->group(function () {
                Route::get('/list', 'message_send_list')->name('admin.sms.message_send_list');
                Route::get('/get_all_data', 'send_message_get_all_data')->name('admin.sms.send_message_get_all_data');
                Route::post('/store', 'send_message_store')->name('admin.sms.send_message_store');
                Route::post('/delete', 'send_message_delete')->name('admin.sms.send_message_delete');
            });
        });
        /* Send Bulk SMS Template Route */
        Route::prefix('bulk-message')->group(function () {
            Route::controller(SmsController::class)->group(function () {
                Route::get('/list', 'bulk_message_send_list')->name('admin.sms.bulk.message_send_list');
                Route::get('/get_all_data', 'bulk_message_get_all_data')->name('admin.sms.bulk.send_message_get_all_data');
                Route::post('/Store', 'bulk_message_store')->name('admin.sms.bulk.send_message_store');
                Route::post('/delete', 'bulk_message_delete')->name('admin.sms.bulk.send_message_delete');
            });
        });
    });
    /* HR & Admin Management Route */
    Route::prefix('admin/hr-management')->group(function () {
        /* Shift Route */
        Route::prefix('shift')->group(function(){
            Route::controller(Shift_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.hr.shift.index');
                 Route::get('/all_data','all_data')->name('admin.hr.shift.all_data');
                 Route::post('/store','store')->name('admin.hr.shift.store');
                 Route::post('/update','update')->name('admin.hr.shift.update');
                 Route::post('/delete','delete')->name('admin.hr.shift.delete');
                 Route::get('/get_shift/{id}','get_shift')->name('admin.hr.shift.get_shift');
            });
        });
        /* Department Route */
        Route::prefix('department')->group(function(){
            Route::controller(Department_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.hr.department.index');
                 Route::get('/all_data','all_data')->name('admin.hr.department.all_data');
                 Route::post('/store','store')->name('admin.hr.department.store');
                 Route::post('/update','update')->name('admin.hr.department.update');
                 Route::post('/delete','delete')->name('admin.hr.department.delete');
                 Route::get('/get_department/{id}','get_department')->name('admin.hr.department.get_department');
            });
        });
        /* Designation Route */
        Route::prefix('designation')->group(function(){
            Route::controller(Designation_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.hr.designation.index');
                Route::get('/all_data','all_data')->name('admin.hr.designation.all_data');
                Route::post('/store','store')->name('admin.hr.designation.store');
                Route::post('/update','update')->name('admin.hr.designation.update');
                Route::post('/delete','delete')->name('admin.hr.designation.delete');
                Route::get('/get_designation/{id}','get_designation')->name('admin.hr.designation.get_designation');
            });
        });
        /* Employee Route */
        Route::prefix('employee')->group(function(){
            Route::controller(Employee_controller::class)->group(function(){
                Route::get('/create','create')->name('admin.hr.employee.create');
                Route::get('/index','index')->name('admin.hr.employee.index');
                Route::get('/all_data','all_data')->name('admin.hr.employee.all_data');
                Route::post('/store','store')->name('admin.hr.employee.store');
                Route::post('/update','update')->name('admin.hr.employee.update');
                Route::post('/delete','delete')->name('admin.hr.employee.delete');
                Route::get('/edit/{id}','edit')->name('admin.hr.employee.edit');
                Route::get('/view/{id}','view')->name('admin.hr.employee.view');
                Route::get('/get_employee/{id}','get_employee')->name('admin.hr.employee.get_employee');
                Route::get('/id_card-print/{employee_ids?}','id_card_print')->name('admin.hr.employee.card.print');

            });
        });
        /*Employee Leave */
        Route::prefix('leave')->group(function(){
            Route::controller(Leave_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.hr.employee.leave.index');
                Route::get('/all_data','all_data')->name('admin.hr.employee.leave.all_data');
                Route::post('/store','store')->name('admin.hr.employee.leave.store');
                Route::post('/update','update')->name('admin.hr.employee.leave.update');
                Route::post('/delete','delete')->name('admin.hr.employee.leave.delete');
                Route::get('/get_leave/{id}','get_leave')->name('admin.hr.employee.leave.get_leave');
            });
        });
        /*Employee Attendence */
        Route::prefix('attendence')->group(function(){
            Route::controller(Attendance_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.student.attendence.index');
                Route::post('/store','store')->name('admin.student.attendence.store');
                Route::post('/update','update')->name('admin.student.attendence.update');
                Route::post('/delete','delete')->name('admin.student.attendence.delete');
                Route::get('/get_attendance/{id}','get_attendance')->name('admin.student.attendence.get_attendance');
                Route::get('/log','attendance_log')->name('admin.student.attendence.log');
                Route::post('/report','attendance_report')->name('admin.student.attendence.report');
            });
        });
        /*Employee Attendence */
        Route::prefix('employee-salary')->group(function(){
            Route::controller(Salary_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.hr.employee.salary.index');
                Route::get('/all_data','all_data')->name('admin.hr.employee.salary.all_data');
                Route::post('/get-salary', 'get_employee_salary')->name('admin.hr.employee.salary.get_employee_salary');
            });
        });
        /*Employee Advance Salary */
        Route::prefix('employee-salary-advance')->group(function(){
            Route::controller(Salary_controller::class)->group(function(){
                Route::get('/advance_salary','advance_salary')->name('admin.hr.employee.salary.advance.index');
                Route::get('/advance_salary_all_data','advance_salary_all_data')->name('admin.hr.employee.advance.salary.all_data');
                Route::post('/store','advance_salary_store')->name('admin.hr.employee.salary.advance.store');
                Route::get('/get_advance_salary/{id}','get_advance_salary')->name('admin.hr.employee.advance.get_advance_salary');

                Route::post('/update','update_advance_salary')->name('admin.hr.employee.advance.advance_salary');
                Route::post('/delete','delete')->name('admin.hr.employee.advance.delete');

                //Advance Salary Report
                Route::get('/report','advance_salary_report')->name('admin.hr.employee.salary.advance.report');
                Route::post('/fetch_report','fetch_advance_salary_report_data')->name('admin.hr.employee.salary.advance.fetch.report');
                Route::post('/get_advance_salary_by_month','get_advance_salary_by_month')->name('admin.hr.employee.advance.get_advance_salary_by_month');
            });
        });
        /*Employee Payroll Management */
        Route::prefix('employee-payroll-management')->group(function(){
            Route::controller(Payroll_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.hr.employee.payroll.index');
                Route::get('/all_data','all_data')->name('admin.hr.employee.payroll.all_data');
                Route::get('/create','create')->name('admin.hr.employee.payroll.create');
                Route::post('/store','store')->name('admin.hr.employee.payroll.store');
                Route::post('/delete','delete')->name('admin.hr.employee.payroll.delete');
            });
        });
        /*Employee Loan Management */
        Route::prefix('employee-loan-management')->group(function(){
            Route::controller(Loan_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.hr.employee.loan.index');
                Route::get('/all_data','all_data')->name('admin.hr.employee.loan.all_data');
                Route::get('/create','create')->name('admin.hr.employee.loan.create');
                Route::post('/store','store')->name('admin.hr.employee.loan.store');

                Route::get('/edit/{loan_id}','edit')->name('admin.hr.employee.loan.edit');
                Route::post('/update','update')->name('admin.hr.employee.loan.update');

                Route::post('/delete','delete')->name('admin.hr.employee.loan.delete');
            });
        });
    });
    /** Accounts Management  Route **/
    Route::prefix('admin/accounts')->group(function () {
        /* Account list Route */
        Route::prefix('account-list')->group(function(){
            Route::controller(Account_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.account.index');
                Route::get('/all_data','all_data')->name('admin.account.all_data');
                Route::post('/store','store')->name('admin.account.store');
                Route::get('/edit/{id}', 'get_account')->name('admin.account.edit');
                Route::post('/update','update')->name('admin.account.update');
                Route::post('/delete','delete')->name('admin.account.delete');
            });
        });
        /* Account Transaction Route */
        Route::prefix('account-transaction')->group(function(){
            Route::controller(Transaction_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.account.transaction.index');
                Route::get('/all_data','all_data')->name('admin.account.transaction.all_data');
                Route::post('/store','store')->name('admin.account.transaction.store');
                Route::get('/edit/{id}', 'get_account_transaction')->name('admin.account.transaction.edit');
                Route::post('/update','update')->name('admin.account.transaction.update');
                Route::post('/delete','delete')->name('admin.account.transaction.delete');
            });
        });
        /* Ledger Report Route */
        Route::prefix('accounts/ledger-report')->group(function(){
            Route::controller(Ledger_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.account.ledger.index');
                Route::post('/report','report')->name('admin.account.ledger.report');
            });
        });
        /* Ledger Report Route */
        Route::prefix('accounts/trial-balance')->group(function(){
            Route::controller(Trial_balance_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.account.trial_balance.index');
                Route::post('/report','report')->name('admin.account.trial_balance.report');
            });
        });
        /* Income Statment Route */
        Route::prefix('accounts/income-statment')->group(function(){
            Route::controller(Income_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.account.income_statment.index');
                Route::post('/report','report')->name('admin.account.income_statment.report');
            });
        });
        /* Balannce Sheet Route */
        Route::prefix('accounts/balance-sheet')->group(function(){
            Route::controller(Balance_sheet_controller::class)->group(function(){
                Route::get('/index','index')->name('admin.account.balance_sheet.index');
                Route::post('/report','report')->name('admin.account.balance_sheet.report');
            });
        });
    });

    /** Settings Management  Route **/
    Route::prefix('admin/settings/')->group(function () {
        Route::controller(SettingsController::class)->group(function () {
            /**Information Route **/
            Route::get('/information', 'index')->name('admin.settings.information.index');
            Route::post('/store', 'store')->name('admin.settings.information.store');
            /** Password Change  Route **/
            Route::get('/passowrd/change', 'password_change_index')->name('admin.settings.passowrd.change.index');
            Route::post('/passowrd/change/store', 'password_change_store')->name('admin.settings.passowrd.change.store');
            /** Payment Method Route **/
            Route::get('/payment-method/create', 'payment_method_create')->name('admin.settings.payment.method.index');
            Route::post('/payment-method/store','payment_method_store')->name('admin.settings.payment.method.store');
        });
    });
    /* Mikrotik Router Management Route */
    Route::prefix('admin/mikrotik')->group(function () {
        /* mikrotik Route */
        Route::prefix('router')->group(function () {
            Route::controller(RouterController::class)->group(function () {
                Route::get('/list', 'index')->name('admin.router.index');
                Route::get('/edit/{id}', 'edit')->name('admin.router.edit');
                Route::post('/update/{id}', 'update')->name('admin.router.update');
                Route::post('/delete', 'delete')->name('admin.router.delete');
                Route::post('/store', 'store')->name('admin.router.store');
                Route::get('/get_router_with_pop/{pop_id}', 'get_router_with_pop')->name('admin.router.get_router_with_pop');
                /**Mikrotik SYNC**/
                Route::get('/sync', 'router_sync')->name('admin.router.sync');
                Route::get('/get_mikrotik_user/{mikrotik_id}', 'get_mikrotik_user')->name('admin.mikrotik.get_user');
                Route::post('/sync/store', 'mikrotik_sync')->name('admin.mikrotik.sync.store');
                /* mikrotik Log */
                Route::get('/log', 'router_log')->name('admin.router.log.index');
                Route::get('/user-list/{router_id}', 'router_user_list')->name('admin.router.ppp.users.index');
                /* nas server */
                Route::get('/nas/server','show_nas_server')->name('admin.router.nas.show_nas_server');
            });
        });
    });

    /** User Management  Route **/
    Route::prefix('admin/user')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/list', 'index')->name('admin.user.index');
            Route::get('/get_user/{id}', 'get_user')->name('admin.user.get_user');
            Route::post('/store', 'store')->name('admin.user.store');
            Route::post('/update', 'update')->name('admin.user.update');
            Route::post('/delete', 'delete')->name('admin.user.delete');
        });
        /*Roles and Permission*/
        Route::controller(RoleController::class)->group(function(){
            Route::get('/role', 'index')->name('admin.user.role.index');
            Route::post('/role/store', 'role_store')->name('admin.user.role.store');
            Route::post('/role/delete', 'role_delete')->name('admin.role.delete');
            Route::get('/permission', 'permission')->name('admin.user.permission');
        });
    });
    /* Network Diagram Router Management Route */
    // Route::prefix('admin/network')->group(function() {

    //     Route::prefix('diagram')->group(function() {
    //         Route::controller(RouterController::class)->group(function() {
    //             Route::get('/list', 'index')->name('admin.router.index');
    //             Route::get('/edit/{id}', 'edit')->name('admin.router.edit');
    //             Route::post('/update/{id}', 'update')->name('admin.router.update');
    //             Route::post('/delete', 'delete')->name('admin.router.delete');
    //             Route::post('/store', 'store')->name('admin.router.store');
    //         });
    //     });
    // });

    Route::get('/migrate', function () {
        Artisan::call('migrate');
        return 'migrate  Completed';
    });
    Route::get('/lang/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'bn'])) {
            Session::put('locale', $locale);
        }
        return redirect()->back();
    });
    Route::get('/admin/network/diagram', function () {
        return view('Backend.Pages.Network.diagram');
    })->name('admin.network.diagram');

    Route::get('/admin/test', function () {
        $router = \App\Models\Router::where('status', 'active')->find(1);
            if ($router) {
                $client = new Client([
                    'host' => $router->ip_address,
                    'user' => $router->username,
                    'pass' => $router->password,
                    'port' => (int) $router->port ?? 8728,
                ]);
                 $activeList = collect($client->query(new Query('/ppp/secret/print'))->read());
                 return $activeList;
            }
    });
});
Route::get('/optimize', function () {
    Artisan::call('optimize:clear');
    return 'Optimize Clear Completed';
});

/*---- Route without middleware ---------*/
Route::get('admin/customer/live-bandwith-update/{customer_id}', [CustomerController::class,'customer_live_bandwith_update'])->name('admin.customer.live_bandwith_update');






/*----Customer Portal Route ---------*/
Route::group(['middleware'=>'customer'],function(){
    Route::get('/customer/portal', function () {
        return view('Portal.Dashboard');
    })->name('customer.portal');

    Route::post('/customer/logout', function (Request $request) {
        Auth::guard('customer')->logout();
        return redirect()->route('admin.login');
    })->name('customer.logout');

    /*-------Customer Recharge From Portal*/
    Route::post('/customer/portal/recharge',[App\Http\Controllers\Frontend\Customer\Recharge_controller::class,'customer_recharge'])->name('customer.portal.recharge');
});
