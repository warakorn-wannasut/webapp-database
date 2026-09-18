<?php

use App\Livewire\Admin\SalesReport;
use App\Livewire\Admin\StockManager;
use App\Livewire\Customer\Dashboard as CustomerDashboard;
use App\Livewire\Customer\FoodOrder;
use App\Livewire\Customer\SeatMap;
use App\Livewire\Customer\Topup;
use App\Livewire\Staff\KitchenQueue;
use App\Livewire\Staff\SeatMonitor;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    // Customer Portal
    Route::get('/dashboard', CustomerDashboard::class)->name('dashboard');
    Route::get('/seat-map', SeatMap::class)->name('customer.seat-map');
    Route::get('/food-order', FoodOrder::class)->name('customer.food-order');
    Route::get('/topup', Topup::class)->name('customer.topup');

    // Staff & Admin Portal
    Route::get('/staff/seat-monitor', SeatMonitor::class)->name('staff.seat-monitor');
    Route::get('/staff/kitchen-queue', KitchenQueue::class)->name('staff.kitchen-queue');
    Route::get('/admin/stock-manager', StockManager::class)->name('admin.stock-manager');
    Route::get('/admin/sales-report', SalesReport::class)->name('admin.sales-report');
});

require __DIR__.'/settings.php';
