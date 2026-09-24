<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Wallet Transactions (Audit log for wallet balance changes)
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->name('fk_wt_user')
                ->restrictOnDelete();
            $table->string('type', 20); // topup, deduct, refund
            $table->decimal('amount', 10, 2);
            $table->string('ref_type', 50)->nullable(); // session, order, package_purchase, cash_topup, qr_topup
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->timestamps();
        });

        // 2. Zones (PC areas with different hourly rates)
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('hourly_rate', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Seats (Computers/Stations)
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')
                ->constrained('zones')
                ->name('fk_seats_zone')
                ->restrictOnDelete();
            $table->string('seat_number', 20)->unique();
            $table->string('status', 20)->default('available'); // available, occupied, maintenance
            $table->timestamps();
        });

        // 4. Packages (Time packages available for purchase)
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')
                ->nullable()
                ->constrained('zones')
                ->name('fk_pkg_zone')
                ->restrictOnDelete();
            $table->string('name', 100);
            $table->integer('duration_hours');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        // 5. User Packages (Purchased packages per customer)
        Schema::create('user_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->name('fk_upkg_user')
                ->restrictOnDelete();
            $table->foreignId('package_id')
                ->constrained('packages')
                ->name('fk_upkg_pkg')
                ->restrictOnDelete();
            $table->integer('remaining_minutes');
            $table->timestamp('purchased_at')->useCurrent();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });

        // 6. Seat Sessions (Check-in / Usage sessions)
        Schema::create('seat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->name('fk_ss_user')
                ->restrictOnDelete();
            $table->foreignId('seat_id')
                ->constrained('seats')
                ->name('fk_ss_seat')
                ->restrictOnDelete();
            $table->foreignId('user_package_id')
                ->nullable()
                ->constrained('user_packages')
                ->name('fk_ss_upkg')
                ->restrictOnDelete();
            $table->decimal('rate_snapshot', 10, 2);
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->decimal('total_cost', 10, 2)->default(0.00);
            $table->string('status', 20)->default('active'); // active, paused, completed
            $table->timestamps();
        });

        // 7. Categories (Food and drink categories)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
        });

        // 8. Products (Food, snack, drink items)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->name('fk_prod_cat')
                ->restrictOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->string('image', 255)->nullable();
            $table->timestamps();
        });

        // 9. Orders (Food delivery orders to seat)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->name('fk_ord_user')
                ->restrictOnDelete();
            $table->foreignId('seat_id')
                ->constrained('seats')
                ->name('fk_ord_seat')
                ->restrictOnDelete();
            $table->foreignId('session_id')
                ->nullable()
                ->constrained('seat_sessions')
                ->name('fk_ord_ss')
                ->restrictOnDelete();
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('payment_method', 20); // wallet, promptpay, cash
            $table->string('payment_status', 20)->default('pending'); // pending, pending_payment, paid, cancelled
            $table->string('order_status', 20)->default('pending'); // pending, preparing, served, cancelled
            $table->timestamps();
        });

        // 10. Order Items (Line items for each order)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->name('fk_oi_ord')
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained('products')
                ->name('fk_oi_prod')
                ->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('seat_sessions');
        Schema::dropIfExists('user_packages');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('seats');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('wallet_transactions');
    }
};
