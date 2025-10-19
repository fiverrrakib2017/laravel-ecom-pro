<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Admin\AdminController;
use App\Http\Controllers\Backend\Api\BkashSmsController;

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AdminController::class, 'api_login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('admin/logout', [AdminController::class, 'api_logout']);
    });

    /*auto bkash send money customer recharge route*/
    Route::post('bkash/sms-receiver', [BkashSmsController::class, 'receive']);
});
