<div class="space-y-6">
    <!-- Notifications -->
    @if (session()->has('success'))
        <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-zinc-800 dark:text-green-400 border border-green-200 dark:border-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-zinc-800 dark:text-red-400 border border-red-200 dark:border-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Products & Menu Section (Col span 2) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Header & Categories -->
            <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">สั่งอาหารและเครื่องดื่ม (PC Bang Kitchen)</h2>
                <p class="text-sm text-zinc-500 mt-1">เลือกเมนูอาหารสไตล์เกาหลี ส่งตรงถึงโต๊ะคอมพิวเตอร์ของคุณ</p>

                <!-- Category Filters -->
                <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <button
                        wire:click="selectCategory(null)"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ is_null($selectedCategoryId) ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 hover:bg-zinc-200' }}"
                    >
                        ทั้งหมด
                    </button>
                    @foreach ($categories as $cat)
                        <button
                            wire:click="selectCategory({{ $cat->id }})"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $selectedCategoryId === $cat->id ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 hover:bg-zinc-200' }}"
                        >
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse ($products as $prod)
                    <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex flex-col justify-between shadow-sm">
                        <div>
                            <div class="flex justify-between items-start">
                                <span class="text-[11px] px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-500 font-medium">
                                    {{ $prod->category->name }}
                                </span>
                                <span class="text-xs font-semibold {{ $prod->stock_quantity > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $prod->stock_quantity > 0 ? 'คงเหลือ ' . $prod->stock_quantity . ' ชิ้น' : 'สินค้าหมด' }}
                                </span>
                            </div>

                            <h4 class="font-bold text-base text-zinc-900 dark:text-white mt-2">{{ $prod->name }}</h4>
                            @if ($prod->description)
                                <p class="text-xs text-zinc-500 mt-1 leading-relaxed">{{ $prod->description }}</p>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between">
                            <span class="text-lg font-extrabold text-zinc-900 dark:text-white">
                                ฿{{ number_format($prod->price, 2) }}
                            </span>

                            @if ($prod->stock_quantity > 0)
                                <button
                                    wire:click="addToCart({{ $prod->id }})"
                                    class="px-3.5 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-sm"
                                >
                                    + ใส่ตะกร้า
                                </button>
                            @else
                                <button disabled class="px-3.5 py-1.5 text-xs font-semibold text-zinc-400 bg-zinc-100 dark:bg-zinc-800 rounded-xl cursor-not-allowed">
                                    หมด
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="sm:col-span-2 p-8 text-center bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 text-zinc-400">
                        ไม่พบรายการอาหารในหมวดหมู่นี้
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Cart & Checkout Sidebar (Col span 1) -->
        <div class="space-y-6">
            <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl sticky top-6 space-y-5">
                <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">ตะกร้าของคุณ (Cart)</h3>
                    <span class="text-xs font-bold text-zinc-500 bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded-md">
                        {{ count($cart) }} รายการ
                    </span>
                </div>

                <!-- Seat Destination -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300 mb-1">
                        ระบุที่นั่งคอมพิวเตอร์ที่ต้องการให้ไปเสิร์ฟ:
                    </label>
                    <select wire:model.live="seat_id" class="w-full text-sm rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 p-2.5 font-medium">
                        <option value="">-- เลือกหมายเลขเครื่อง --</option>
                        @foreach ($allSeats as $s)
                            <option value="{{ $s->id }}">
                                เครื่อง {{ $s->seat_number }} ({{ $s->zone->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Cart Items List -->
                <div class="space-y-3 max-h-72 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($cartItems as $item)
                        <div class="pt-3 first:pt-0 flex items-center justify-between gap-2 text-sm">
                            <div class="flex-1">
                                <p class="font-bold text-zinc-900 dark:text-white text-xs">{{ $item['product']->name }}</p>
                                <p class="text-[11px] text-zinc-500">฿{{ number_format($item['product']->price, 2) }} x {{ $item['quantity'] }}</p>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button wire:click="updateQuantity({{ $item['product']->id }}, -1)" class="w-6 h-6 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center font-bold text-xs hover:bg-zinc-200">&minus;</button>
                                <span class="w-6 text-center font-bold text-xs text-zinc-800 dark:text-zinc-200">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity({{ $item['product']->id }}, 1)" class="w-6 h-6 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center font-bold text-xs hover:bg-zinc-200">+</button>
                                <button wire:click="removeFromCart({{ $item['product']->id }})" class="text-xs text-red-500 hover:text-red-700 ml-1">&times;</button>
                            </div>

                            <span class="font-bold text-xs text-zinc-900 dark:text-white min-w-12 text-right">
                                ฿{{ number_format($item['subtotal'], 2) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-6 text-center text-zinc-400 text-xs">
                            ยังไม่มีอาหารในตะกร้า
                        </div>
                    @endforelse
                </div>

                <!-- Payment Method Selection -->
                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 space-y-2">
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300">วิธีชำระเงิน:</label>

                    <label class="flex items-center gap-2 p-2 border rounded-xl cursor-pointer text-xs transition {{ $paymentMethod === 'wallet' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/40' : 'border-zinc-200 dark:border-zinc-800' }}">
                        <input type="radio" wire:model.live="paymentMethod" value="wallet" class="text-emerald-600">
                        <span class="font-bold text-zinc-800 dark:text-zinc-200">ตัดผ่าน Wallet (คงเหลือ: ฿{{ number_format($user->balance, 2) }})</span>
                    </label>

                    <label class="flex items-center gap-2 p-2 border rounded-xl cursor-pointer text-xs transition {{ $paymentMethod === 'promptpay' ? 'border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/40' : 'border-zinc-200 dark:border-zinc-800' }}">
                        <input type="radio" wire:model.live="paymentMethod" value="promptpay" class="text-indigo-600">
                        <span class="font-bold text-zinc-800 dark:text-zinc-200">สแกน QR PromptPay (จำลอง)</span>
                    </label>

                    <label class="flex items-center gap-2 p-2 border rounded-xl cursor-pointer text-xs transition {{ $paymentMethod === 'cash' ? 'border-amber-500 bg-amber-50/40 dark:bg-amber-950/40' : 'border-zinc-200 dark:border-zinc-800' }}">
                        <input type="radio" wire:model.live="paymentMethod" value="cash" class="text-amber-600">
                        <span class="font-bold text-zinc-800 dark:text-zinc-200">เงินสดเก็บปลายทาง (Cash on Delivery)</span>
                    </label>
                </div>

                <!-- Total & Checkout Button -->
                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 space-y-3">
                    <div class="flex justify-between items-center text-base font-bold">
                        <span class="text-zinc-800 dark:text-zinc-200">ยอดรวมทั้งสิ้น:</span>
                        <span class="text-xl text-emerald-600 dark:text-emerald-400 font-extrabold">฿{{ number_format($totalAmount, 2) }}</span>
                    </div>

                    <button
                        wire:click="placeOrder"
                        @disabled(empty($cart) || ! $seat_id)
                        class="w-full py-3 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow transition"
                    >
                        ยืนยันการสั่งซื้ออาหาร
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code PromptPay Simulation Modal -->
    @if ($showQrModal)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-sm w-full p-6 text-center space-y-4 shadow-2xl">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">สแกน QR Code เพื่อชำระเงิน</h3>
                <p class="text-xs text-zinc-500">บิลเลขที่ #{{ $lastPlacedOrderId }} ยอดชำระ ฿{{ number_format($totalAmount, 2) }}</p>

                <!-- Simulated QR Code SVG Box -->
                <div class="p-6 bg-zinc-50 dark:bg-zinc-800 rounded-2xl flex flex-col items-center justify-center border-2 border-dashed border-indigo-300 dark:border-indigo-800">
                    <div class="w-44 h-44 bg-zinc-900 text-white rounded-lg flex flex-col items-center justify-center p-4">
                        <div class="grid grid-cols-4 gap-2 w-full h-full p-2 bg-white rounded">
                            <div class="bg-black col-span-2 row-span-2"></div>
                            <div class="bg-black"></div>
                            <div class="bg-black"></div>
                            <div class="bg-black col-span-2"></div>
                            <div class="bg-black col-span-2 row-span-2"></div>
                            <div class="bg-black"></div>
                            <div class="bg-black"></div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mt-2">Thai QR Payment (จำลอง)</span>
                </div>

                <p class="text-xs text-zinc-400">ระบบจำลองการจ่ายเงินผ่านพร้อมเพย์</p>

                <button
                    wire:click="closeQrModal"
                    class="w-full py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition shadow"
                >
                    จำลองว่าลูกค้าสแกนจ่ายสำเร็จแล้ว
                </button>
            </div>
        </div>
    @endif
</div>
