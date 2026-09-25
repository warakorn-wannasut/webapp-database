<?php

use App\Models\Category;
use App\Models\Package;
use App\Models\Product;
use App\Models\Seat;
use App\Models\SeatSession;
use App\Models\User;
use App\Models\Zone;

test('authenticated user can access all controller endpoints', function () {
    $user = User::factory()->create(['role' => 'admin', 'balance' => 500.00]);
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertOk();
    $this->get(route('customer.seat-map'))->assertOk();
    $this->get(route('customer.food-order'))->assertOk();
    $this->get(route('customer.topup'))->assertOk();
    $this->get(route('staff.seat-monitor'))->assertOk();
    $this->get(route('staff.kitchen-queue'))->assertOk();
    $this->get(route('admin.stock-manager'))->assertOk();
    $this->get(route('admin.sales-report'))->assertOk();
});

test('controllers handle student actions via http post requests', function () {
    $user = User::factory()->create(['role' => 'admin', 'balance' => 200.00]);
    $this->actingAs($user);

    $zone = Zone::create(['name' => 'VIP', 'hourly_rate' => 80.00]);
    $seat = Seat::create(['zone_id' => $zone->id, 'seat_number' => 'PC-99', 'status' => 'available']);
    $package = Package::create(['zone_id' => $zone->id, 'name' => 'Pro Gamer', 'duration_hours' => 3, 'price' => 150.00]);

    // 1. Test Topup Controller
    $topupRes = $this->post(route('customer.do-topup'), [
        'amount' => 100.00,
        'topup_method' => 'qr',
    ]);
    $topupRes->assertSessionHas('success');
    $user->refresh();
    expect((float) $user->balance)->toBe(300.00);

    // 2. Test Buy Package Controller
    $buyPkgRes = $this->post(route('customer.buy-package'), [
        'package_id' => $package->id,
    ]);
    $buyPkgRes->assertSessionHas('success');
    $user->refresh();
    expect((float) $user->balance)->toBe(150.00);

    // 3. Test Check-in Controller
    $checkInRes = $this->post(route('customer.check-in'), [
        'seat_id' => $seat->id,
        'billing_mode' => 'pay_as_you_go',
    ]);
    $checkInRes->assertRedirect(route('dashboard'));
    $seat->refresh();
    expect($seat->status)->toBe('occupied');

    // 4. Test Check-out Controller
    $checkOutRes = $this->post(route('customer.check-out'));
    $checkOutRes->assertSessionHas('success');
    $seat->refresh();
    expect($seat->status)->toBe('available');

    // 5. Test Admin Stock Adjust Controller
    $cat = Category::create(['name' => 'Snacks']);
    $product = Product::create([
        'category_id' => $cat->id,
        'name' => 'Chips',
        'price' => 30.00,
        'stock_quantity' => 10,
    ]);

    $adjustRes = $this->post(route('admin.adjust-stock'), [
        'product_id' => $product->id,
        'delta' => 5,
    ]);
    $adjustRes->assertSessionHas('success');
    $product->refresh();
    expect($product->stock_quantity)->toBe(15);
});
