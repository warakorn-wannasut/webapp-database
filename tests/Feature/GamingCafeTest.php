<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Package;
use App\Models\Product;
use App\Models\Seat;
use App\Models\User;
use App\Models\Zone;
use App\Services\BillingService;
use App\Services\OrderService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GamingCafeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Zone $zone;
    protected Seat $seat;
    protected Package $package;
    protected Product $product;
    protected WalletService $walletService;
    protected BillingService $billingService;
    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletService = app(WalletService::class);
        $this->billingService = app(BillingService::class);
        $this->orderService = app(OrderService::class);

        $this->user = User::create([
            'name' => 'Player 1',
            'username' => 'player1',
            'email' => 'p1@test.com',
            'role' => 'customer',
            'balance' => 200.00,
            'password' => bcrypt('password'),
        ]);

        $this->zone = Zone::create([
            'name' => 'Standard Zone',
            'hourly_rate' => 60.00, // 1 THB per minute
        ]);

        $this->seat = Seat::create([
            'zone_id' => $this->zone->id,
            'seat_number' => 'PC-01',
            'status' => 'available',
        ]);

        $this->package = Package::create([
            'zone_id' => $this->zone->id,
            'name' => '2 Hours Promo',
            'duration_hours' => 2,
            'price' => 100.00,
        ]);

        $cat = Category::create(['name' => 'Food']);
        $this->product = Product::create([
            'category_id' => $cat->id,
            'name' => 'Ramyeon',
            'price' => 50.00,
            'stock_quantity' => 10,
        ]);
    }

    public function test_wallet_topup_and_deduct_creates_audit_log(): void
    {
        $this->walletService->topUp($this->user, 100.00, 'cash_topup');
        $this->user->refresh();
        $this->assertEquals(300.00, (float) $this->user->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $this->user->id,
            'type' => 'topup',
            'amount' => 100.00,
        ]);

        $this->walletService->deduct($this->user, 50.00, 'fee');
        $this->user->refresh();
        $this->assertEquals(250.00, (float) $this->user->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $this->user->id,
            'type' => 'deduct',
            'amount' => 50.00,
        ]);
    }

    public function test_buy_package_and_checkin_checkout_flow(): void
    {
        $now = Carbon::create(2026, 9, 18, 12, 0, 0);
        Carbon::setTestNow($now);

        // Buy package
        $userPkg = $this->billingService->buyPackage($this->user, $this->package->id);
        $this->user->refresh();
        $this->assertEquals(100.00, (float) $this->user->balance);
        $this->assertEquals(120, $userPkg->remaining_minutes);

        // Check-in using package
        $session = $this->billingService->checkIn($this->user, $this->seat->id, $userPkg->id);
        $this->seat->refresh();
        $this->assertEquals('occupied', $this->seat->status);
        $this->assertEquals('active', $session->status);

        // Advance time by 30 minutes
        Carbon::setTestNow($now->copy()->addMinutes(30));

        // Check-out
        $this->billingService->checkOut($session);
        $this->seat->refresh();
        $userPkg->refresh();
        $this->user->refresh();

        $this->assertEquals('available', $this->seat->status);
        $this->assertEquals(90, $userPkg->remaining_minutes); // 120 - 30 = 90
        $this->assertEquals(100.00, (float) $this->user->balance); // Package covered all, balance unchanged

        Carbon::setTestNow(); // reset
    }

    public function test_order_food_with_atomic_stock_decrement(): void
    {
        $order = $this->orderService->placeOrder(
            $this->user,
            $this->seat->id,
            [['product_id' => $this->product->id, 'quantity' => 2]],
            'wallet'
        );

        $this->product->refresh();
        $this->user->refresh();

        $this->assertEquals(8, $this->product->stock_quantity); // 10 - 2 = 8
        $this->assertEquals(100.00, (float) $order->total_amount); // 50 * 2 = 100
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals(100.00, (float) $this->user->balance); // 200 - 100 = 100

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 50.00,
        ]);
    }

    public function test_cash_order_requires_staff_confirmation(): void
    {
        $order = $this->orderService->placeOrder(
            $this->user,
            $this->seat->id,
            [['product_id' => $this->product->id, 'quantity' => 1]],
            'cash'
        );

        $this->assertEquals('pending_payment', $order->payment_status);

        $this->orderService->confirmCashPayment($order);
        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
    }

    public function test_pay_as_you_go_billing_deducts_wallet(): void
    {
        $now = Carbon::create(2026, 9, 18, 14, 0, 0);
        Carbon::setTestNow($now);

        // Check in without package (pay as you go)
        $session = $this->billingService->checkIn($this->user, $this->seat->id, null);

        // Advance by 60 minutes (Zone rate is 60 THB/hr -> 60 THB)
        Carbon::setTestNow($now->copy()->addMinutes(60));

        $this->billingService->checkOut($session);
        $this->user->refresh();

        $this->assertEquals(140.00, (float) $this->user->balance); // 200 - 60 = 140
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $this->user->id,
            'type' => 'deduct',
            'amount' => 60.00,
            'ref_type' => 'session',
        ]);

        Carbon::setTestNow();
    }

    public function test_package_overtime_deducts_from_wallet(): void
    {
        $now = Carbon::create(2026, 9, 18, 16, 0, 0);
        Carbon::setTestNow($now);

        $userPkg = $this->billingService->buyPackage($this->user, $this->package->id); // Remaining: 120 mins
        $userPkg->update(['remaining_minutes' => 30]); // Simulate only 30 mins remaining

        $session = $this->billingService->checkIn($this->user, $this->seat->id, $userPkg->id);

        // Advance 50 minutes (30 mins covered by package, 20 mins overtime)
        // Rate is 60 THB/hr -> 20 mins = 20 THB
        Carbon::setTestNow($now->copy()->addMinutes(50));

        $this->billingService->checkOut($session);
        $userPkg->refresh();
        $this->user->refresh();

        $this->assertEquals(0, $userPkg->remaining_minutes);
        // User had 200 - 100 (pkg) = 100. 100 - 20 (overtime) = 80 THB
        $this->assertEquals(80.00, (float) $this->user->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $this->user->id,
            'type' => 'deduct',
            'amount' => 20.00,
            'ref_type' => 'session_overtime',
        ]);

        Carbon::setTestNow();
    }
}
