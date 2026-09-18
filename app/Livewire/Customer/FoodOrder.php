<?php

namespace App\Livewire\Customer;

use App\Models\Category;
use App\Models\Product;
use App\Models\Seat;
use App\Models\SeatSession;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Food & Drink Ordering')]
class FoodOrder extends Component
{
    #[Url]
    public ?int $seat_id = null;

    public ?int $selectedCategoryId = null;
    public array $cart = []; // [product_id => quantity]
    public string $paymentMethod = 'wallet'; // wallet, promptpay, cash
    public bool $showQrModal = false;
    public ?int $lastPlacedOrderId = null;

    public function mount()
    {
        $user = Auth::user();
        if (! $this->seat_id) {
            $activeSession = SeatSession::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();
            if ($activeSession) {
                $this->seat_id = $activeSession->seat_id;
            }
        }
    }

    public function selectCategory(?int $catId)
    {
        $this->selectedCategoryId = $catId;
    }

    public function addToCart(int $productId)
    {
        $product = Product::findOrFail($productId);
        if ($product->stock_quantity <= 0) {
            session()->flash('error', "สินค้า {$product->name} สินค้าหมด");
            return;
        }

        $currentQty = $this->cart[$productId] ?? 0;
        if ($currentQty >= $product->stock_quantity) {
            session()->flash('error', "ไม่สามารถเพิ่มได้เกินสต็อกคงเหลือ ({$product->stock_quantity} ชิ้น)");
            return;
        }

        $this->cart[$productId] = $currentQty + 1;
    }

    public function updateQuantity(int $productId, int $delta)
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        $newQty = $this->cart[$productId] + $delta;
        if ($newQty <= 0) {
            unset($this->cart[$productId]);
        } else {
            $product = Product::findOrFail($productId);
            if ($newQty > $product->stock_quantity) {
                session()->flash('error', "สต็อกคงเหลือไม่พอ ({$product->stock_quantity} ชิ้น)");
                return;
            }
            $this->cart[$productId] = $newQty;
        }
    }

    public function removeFromCart(int $productId)
    {
        unset($this->cart[$productId]);
    }

    public function placeOrder(OrderService $orderService)
    {
        if (empty($this->cart)) {
            session()->flash('error', 'ตะกร้าสินค้าว่างเปล่า');
            return;
        }

        if (! $this->seat_id) {
            session()->flash('error', 'กรุณาระบุที่นั่งคอมพิวเตอร์ที่ต้องการให้ไปเสิร์ฟ');
            return;
        }

        $user = Auth::user();

        // Prepare items array
        $items = [];
        foreach ($this->cart as $pId => $qty) {
            $items[] = [
                'product_id' => $pId,
                'quantity' => $qty,
            ];
        }

        try {
            $order = $orderService->placeOrder($user, $this->seat_id, $items, $this->paymentMethod);
            $this->cart = [];
            $this->lastPlacedOrderId = $order->id;

            if ($this->paymentMethod === 'promptpay') {
                $this->showQrModal = true;
            } else {
                session()->flash('success', "สั่งอาหารสำเร็จ! บิลหมายเลข #{$order->id} พนักงานกำลังเตรียมอาหารไปเสิร์ฟที่โต๊ะ");
            }
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function closeQrModal()
    {
        $this->showQrModal = false;
        session()->flash('success', 'ชำระเงินผ่าน PromptPay จำลองเรียบร้อยแล้ว พนักงานกำลังจัดเตรียมอาหาร');
    }

    public function render()
    {
        $categories = Category::withCount('products')->get();

        $productsQuery = Product::with('category');
        if ($this->selectedCategoryId) {
            $productsQuery->where('category_id', $this->selectedCategoryId);
        }
        $products = $productsQuery->orderBy('name')->get();

        $allSeats = Seat::orderBy('seat_number')->get();

        // Calculate Cart Items
        $cartItems = [];
        $totalAmount = 0.00;
        if (! empty($this->cart)) {
            $cartProducts = Product::whereIn('id', array_keys($this->cart))->get();
            foreach ($cartProducts as $cp) {
                $qty = $this->cart[$cp->id] ?? 0;
                $subtotal = round((float) $cp->price * $qty, 2);
                $totalAmount += $subtotal;
                $cartItems[] = [
                    'product' => $cp,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];
            }
        }

        return view('livewire.customer.food-order', [
            'categories' => $categories,
            'products' => $products,
            'allSeats' => $allSeats,
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'user' => Auth::user(),
        ]);
    }
}
