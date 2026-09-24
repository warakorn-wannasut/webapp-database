<div class="space-y-6">
    <!-- Notifications -->
    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-sm flex items-center gap-2">
            <span class="text-base">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-xl bg-red-950/60 border border-red-500/40 text-red-300 text-sm flex items-center gap-2">
            <span class="text-base">⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Active Seat Banner -->
    @if ($activeSeat)
        <div class="p-3.5 rounded-2xl bg-gradient-to-r from-red-950/40 via-[#141824] to-[#0a0c10] border border-red-500/30 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                <p class="text-xs font-semibold text-zinc-200">
                    กำลังเล่นอยู่ที่เครื่อง <span class="font-black text-white underline">{{ $activeSeat->seat_number }}</span> ({{ $activeSeat->zone->name }}) &bull; อาหารจะถูกเสิร์ฟมาที่โต๊ะนี้โดยอัตโนมัติ
                </p>
            </div>
            <span class="salai-badge-red text-[10px] hidden sm:inline-flex">AUTO DELIVER</span>
        </div>
    @else
        <div class="p-3.5 rounded-2xl bg-amber-950/40 border border-amber-500/40 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="text-base">⚠️</span>
                <p class="text-xs font-semibold text-amber-200">
                    คุณยังไม่ได้เปิดเครื่องคอมพิวเตอร์ในร้าน กรุณา Check-in เข้าเครื่องก่อนสั่งอาหาร
                </p>
            </div>
            <a href="{{ route('customer.seat-map') }}" class="salai-btn-primary text-xs py-1.5 px-3.5" wire:navigate>
                เลือกที่นั่ง &rarr;
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Products & Menu Section (Col span 2) -->
        <div class="lg:col-span-2 space-y-5">
            <!-- Header & Categories -->
            <div class="salai-card p-6 space-y-4">
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h2 class="text-2xl font-black text-white tracking-wide font-sans">
                        สั่งอาหารและเครื่องดื่ม (PC Bang Kitchen)
                    </h2>
                </div>
                <p class="text-xs text-zinc-400 pl-4">เลือกเมนูอาหารสไตล์เกาหลีและเครื่องดื่มเย็นฉ่ำ ส่งตรงถึงโต๊ะคอมพิวเตอร์ของคุณ</p>

                <!-- Category Filters (Salai Style Pills) -->
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-[#1e2430]">
                    <button
                        wire:click="selectCategory(null)"
                        class="salai-pill text-xs font-semibold transition {{ is_null($selectedCategoryId) ? 'bg-red-600 text-white shadow-lg shadow-red-600/30' : 'bg-[#141824] text-zinc-300 hover:text-white border border-[#232938]' }}"
                    >
                        🔥 เมนูทั้งหมด
                    </button>
                    @foreach ($categories as $cat)
                        <button
                            wire:click="selectCategory({{ $cat->id }})"
                            class="salai-pill text-xs font-semibold transition {{ $selectedCategoryId === $cat->id ? 'bg-red-600 text-white shadow-lg shadow-red-600/30' : 'bg-[#141824] text-zinc-300 hover:text-white border border-[#232938]' }}"
                        >
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse ($products as $prod)
                    <div class="salai-card p-5 flex flex-col justify-between group">
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <span class="salai-badge-red text-[10px]">
                                    {{ $prod->category->name }}
                                </span>
                                <span class="text-xs font-bold font-mono {{ $prod->stock_quantity > 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                    {{ $prod->stock_quantity > 0 ? 'คงเหลือ ' . $prod->stock_quantity . ' ชิ้น' : 'สินค้าหมด' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-950 to-zinc-900 border border-[#232938] flex items-center justify-center text-2xl group-hover:scale-105 transition-transform shrink-0">
                                    @if(str_contains(strtolower($prod->name), 'รามยอน') || str_contains(strtolower($prod->name), 'ramen')) 🍜
                                    @elseif(str_contains(strtolower($prod->name), 'ไก่ทอด') || str_contains(strtolower($prod->name), 'chicken')) 🍗
                                    @elseif(str_contains(strtolower($prod->name), 'ต๊อก') || str_contains(strtolower($prod->name), 'tteok')) 🍲
                                    @elseif(str_contains(strtolower($prod->name), 'คิมบับ') || str_contains(strtolower($prod->name), 'kimbap')) 🍱
                                    @elseif(str_contains(strtolower($prod->name), 'กาแฟ') || str_contains(strtolower($prod->name), 'coffee') || str_contains(strtolower($prod->name), 'americano')) ☕
                                    @elseif(str_contains(strtolower($prod->name), 'โซดา') || str_contains(strtolower($prod->name), 'soda') || str_contains(strtolower($prod->name), 'cola')) 🥤
                                    @else 🍽️
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-base text-white group-hover:text-red-400 transition-colors">{{ $prod->name }}</h4>
                                    @if ($prod->description)
                                        <p class="text-xs text-zinc-400 mt-0.5 line-clamp-2 leading-relaxed">{{ $prod->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-[#1e2430] flex items-center justify-between">
                            <span class="text-xl font-black text-white font-mono">
                                ฿{{ number_format($prod->price, 2) }}
                            </span>

                            @if ($prod->stock_quantity > 0)
                                <button
                                    wire:click="addToCart({{ $prod->id }})"
                                    class="salai-btn-primary text-xs py-1.5 px-3.5"
                                >
                                    + ใส่ตะกร้า
                                </button>
                            @else
                                <button disabled class="salai-card text-xs font-semibold px-3 py-1.5 text-zinc-500 border-[#232938] cursor-not-allowed">
                                    หมด
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="sm:col-span-2 p-10 text-center salai-card text-zinc-400 text-xs">
                        ไม่พบรายการอาหารในหมวดหมู่นี้
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Cart & Checkout Sidebar (Col span 1) -->
        <div class="space-y-6">
            <div class="salai-card-glow p-6 rounded-3xl sticky top-6 space-y-5">
                <div class="flex items-center justify-between border-b border-[#232938] pb-3">
                    <div class="salai-step-header">
                        <span class="salai-step-bar"></span>
                        <h3 class="text-lg font-black text-white font-sans">ตะกร้าของคุณ (Cart)</h3>
                    </div>
                    <span class="salai-badge-red text-xs font-bold">
                        {{ count($cart) }} รายการ
                    </span>
                </div>

                <!-- Seat Destination (Auto-bound to Computer) -->
                @if ($activeSeat)
                    <div class="p-3.5 bg-[#141824] border border-red-500/40 rounded-2xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-600/20 text-red-500 border border-red-500/30 flex items-center justify-center font-bold text-lg shrink-0">
                                🖥️
                            </div>
                            <div>
                                <span class="text-[10px] text-zinc-400 block font-medium">จัดส่งตรงถึงเครื่องของคุณ</span>
                                <span class="font-black text-base text-white font-sans">
                                    เครื่อง {{ $activeSeat->seat_number }}
                                </span>
                                <span class="text-[11px] text-red-400 font-semibold block">
                                    {{ $activeSeat->zone->name }}
                                </span>
                            </div>
                        </div>
                        <span class="salai-badge-red text-[10px]">
                            ผูกเครื่องแล้ว
                        </span>
                    </div>
                @else
                    <div class="p-4 bg-amber-950/40 border border-amber-500/40 rounded-2xl text-center space-y-2">
                        <div class="text-2xl">⚠️</div>
                        <p class="text-xs font-bold text-amber-300">คุณยังไม่ได้ Check-in เปิดเครื่อง</p>
                        <p class="text-[11px] text-zinc-400">ระบบจะจัดส่งอาหารไปยังเครื่องที่คุณนั่ง กรุณาเปิดเครื่องก่อนสั่งอาหาร</p>
                        <a href="{{ route('customer.seat-map') }}" class="salai-btn-primary text-xs py-2 px-4 w-full" wire:navigate>
                            🖥️ ไปที่ผังที่นั่งเพื่อ Check-in
                        </a>
                    </div>
                @endif

                <!-- Cart Items List -->
                <div class="space-y-3 max-h-72 overflow-y-auto divide-y divide-[#1e2430]">
                    @forelse ($cartItems as $item)
                        <div class="pt-3 first:pt-0 flex items-center justify-between gap-2 text-xs">
                            <div class="flex-1">
                                <p class="font-bold text-white">{{ $item['product']->name }}</p>
                                <p class="text-[11px] text-zinc-400 font-mono">฿{{ number_format($item['product']->price, 2) }} x {{ $item['quantity'] }}</p>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button wire:click="updateQuantity({{ $item['product']->id }}, -1)" class="w-6 h-6 rounded-lg bg-[#141824] border border-[#232938] text-zinc-200 flex items-center justify-center font-bold text-xs hover:border-red-500">&minus;</button>
                                <span class="w-6 text-center font-bold text-xs text-white">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity({{ $item['product']->id }}, 1)" class="w-6 h-6 rounded-lg bg-[#141824] border border-[#232938] text-zinc-200 flex items-center justify-center font-bold text-xs hover:border-red-500">+</button>
                                <button wire:click="removeFromCart({{ $item['product']->id }})" class="text-xs text-red-400 hover:text-red-300 ml-1">&times;</button>
                            </div>

                            <span class="font-bold text-xs text-white min-w-12 text-right font-mono">
                                ฿{{ number_format($item['subtotal'], 2) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-zinc-500 text-xs">
                            🛒 ยังไม่มีอาหารในตะกร้า
                        </div>
                    @endforelse
                </div>

                <!-- Payment Method Selection -->
                <div class="pt-3 border-t border-[#232938] space-y-2">
                    <label class="block text-xs font-bold text-zinc-300">ช่องทางการชำระเงิน:</label>

                    <label class="flex items-center gap-2.5 p-2.5 border rounded-xl cursor-pointer text-xs transition {{ $paymentMethod === 'wallet' ? 'border-red-500 bg-red-950/30' : 'border-[#232938] bg-[#141824]' }}">
                        <input type="radio" wire:model.live="paymentMethod" value="wallet" class="text-red-600 focus:ring-red-500">
                        <span class="font-bold text-white">ตัดเงินใน Wallet (คงเหลือ: <span class="text-amber-400">฿{{ number_format($user->balance, 2) }}</span>)</span>
                    </label>

                    <label class="flex items-center gap-2.5 p-2.5 border rounded-xl cursor-pointer text-xs transition {{ $paymentMethod === 'promptpay' ? 'border-red-500 bg-red-950/30' : 'border-[#232938] bg-[#141824]' }}">
                        <input type="radio" wire:model.live="paymentMethod" value="promptpay" class="text-red-600 focus:ring-red-500">
                        <span class="font-bold text-white">สแกน QR PromptPay (จำลอง)</span>
                    </label>

                    <label class="flex items-center gap-2.5 p-2.5 border rounded-xl cursor-pointer text-xs transition {{ $paymentMethod === 'cash' ? 'border-red-500 bg-red-950/30' : 'border-[#232938] bg-[#141824]' }}">
                        <input type="radio" wire:model.live="paymentMethod" value="cash" class="text-red-600 focus:ring-red-500">
                        <span class="font-bold text-white">เงินสดเก็บปลายทางที่โต๊ะ (Cash)</span>
                    </label>
                </div>

                <!-- Total & Checkout Button -->
                <div class="pt-4 border-t border-[#232938] space-y-3">
                    <div class="flex justify-between items-center text-sm font-bold">
                        <span class="text-zinc-300">ยอดรวมทั้งสิ้น:</span>
                        <span class="text-2xl text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-amber-400 font-black font-mono">฿{{ number_format($totalAmount, 2) }}</span>
                    </div>

                    <button
                        wire:click="placeOrder"
                        @disabled(empty($cart) || ! $activeSeat)
                        class="salai-btn-primary w-full py-3 text-xs disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        @if (! $activeSeat)
                            กรุณาเปิดเครื่องก่อนสั่งอาหาร
                        @else
                            🚀 ยืนยันการสั่งอาหาร (ส่งไปที่เครื่อง {{ $activeSeat->seat_number }})
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code PromptPay Simulation Modal (Salai Dark Glow) -->
    @if ($showQrModal)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="salai-card-glow max-w-sm w-full p-6 text-center space-y-4 shadow-2xl rounded-3xl">
                <div class="flex items-center justify-center gap-2">
                    <span class="salai-step-bar"></span>
                    <h3 class="text-lg font-black text-white font-sans">สแกน QR Code เพื่อชำระเงิน</h3>
                </div>
                <p class="text-xs text-zinc-400">บิลเลขที่ <span class="font-bold text-white">#{{ $lastPlacedOrderId }}</span> &bull; ยอดชำระ <span class="font-black text-emerald-400 text-sm">฿{{ number_format($totalAmount, 2) }}</span></p>

                <!-- Simulated QR Code SVG Box -->
                <div class="p-6 bg-[#0a0c10] rounded-2xl flex flex-col items-center justify-center border border-red-500/30">
                    <div class="w-44 h-44 bg-zinc-950 text-white rounded-xl flex flex-col items-center justify-center p-4 border border-[#232938]">
                        <div class="grid grid-cols-4 gap-2 w-full h-full p-2 bg-white rounded-lg">
                            <div class="bg-black col-span-2 row-span-2"></div>
                            <div class="bg-black"></div>
                            <div class="bg-black"></div>
                            <div class="bg-black col-span-2"></div>
                            <div class="bg-black col-span-2 row-span-2"></div>
                            <div class="bg-black"></div>
                            <div class="bg-black"></div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-red-400 mt-3 font-mono">PromptPay QR Code (จำลอง)</span>
                </div>

                <p class="text-[11px] text-zinc-500">ระบบจำลองการจ่ายเงินผ่านพร้อมเพย์ในโปรเจค</p>

                <button
                    wire:click="closeQrModal"
                    class="salai-btn-primary w-full py-2.5 text-xs font-bold"
                >
                    ✅ สแกนจ่ายสำเร็จ (จำลองการรับเงิน)
                </button>
            </div>
        </div>
    @endif
</div>
