<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodOrderController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    // -------------------------------------------------------------
    // Customer Portal (สมาชิกคนที่ 1 - 4)
    // -------------------------------------------------------------
    // สมาชิกคนที่ 1: Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/check-out', [DashboardController::class, 'checkOut'])->name('customer.check-out');

    // สมาชิกคนที่ 2: Seat Map & Sessions
    Route::get('/seat-map', [SeatController::class, 'index'])->name('customer.seat-map');
    Route::post('/check-in', [SeatController::class, 'checkIn'])->name('customer.check-in');

    // สมาชิกคนที่ 3: Food & Beverage POS
    Route::get('/food-order', [FoodOrderController::class, 'index'])->name('customer.food-order');
    Route::post('/food-order', [FoodOrderController::class, 'placeOrder'])->name('customer.place-order');

    // สมาชิกคนที่ 4: Wallet & Packages
    Route::get('/topup', [WalletController::class, 'index'])->name('customer.topup');
    Route::post('/topup', [WalletController::class, 'topUp'])->name('customer.do-topup');
    Route::post('/buy-package', [WalletController::class, 'buyPackage'])->name('customer.buy-package');

    // -------------------------------------------------------------
    // Staff & Admin Portal (สมาชิกคนที่ 5)
    // -------------------------------------------------------------
    // ระบบพนักงานหน้าร้านและครัว
    Route::get('/staff/seat-monitor', [StaffController::class, 'seatMonitor'])->name('staff.seat-monitor');
    Route::post('/staff/force-end', [StaffController::class, 'forceEnd'])->name('staff.force-end');
    Route::post('/staff/toggle-seat', [StaffController::class, 'toggleMaintenance'])->name('staff.toggle-seat');

    Route::get('/staff/kitchen-queue', [StaffController::class, 'kitchenQueue'])->name('staff.kitchen-queue');
    Route::post('/staff/orders/status', [StaffController::class, 'updateOrderStatus'])->name('staff.update-order-status');
    Route::post('/staff/orders/confirm-cash', [StaffController::class, 'confirmCashPayment'])->name('staff.confirm-cash');

    // ระบบผู้ดูแลร้าน (Admin)
    Route::get('/admin/stock-manager', [AdminController::class, 'stockManager'])->name('admin.stock-manager');
    Route::post('/admin/stock/adjust', [AdminController::class, 'adjustStock'])->name('admin.adjust-stock');
    Route::post('/admin/products', [AdminController::class, 'createProduct'])->name('admin.create-product');

    Route::get('/admin/sales-report', [AdminController::class, 'salesReport'])->name('admin.sales-report');
});

require __DIR__.'/settings.php';
