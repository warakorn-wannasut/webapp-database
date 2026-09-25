<x-layouts::app :title="__('Order Food & Beverages')">
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
            <div class="p-3.5 rounded-xl bg-[#141824] border border-[#232938] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <p class="text-xs font-medium text-zinc-200">
                        กำลังเล่นอยู่ที่เครื่อง <span class="font-bold text-white underline">{{ $activeSeat->seat_number }}</span> ({{ $activeSeat->zone->name }}) &bull; อาหารจะถูกเสิร์ฟมาที่โต๊ะนี้โดยอัตโนมัติ
                    </p>
                </div>
                <span class="salai-badge-red text-[10px] hidden sm:inline-flex">AUTO DELIVER</span>
            </div>
        @else
            <div class="p-3.5 rounded-xl bg-amber-950/30 border border-amber-500/30 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="text-base">⚠️</span>
                    <p class="text-xs font-semibold text-amber-200">
                        คุณยังไม่ได้เปิดเครื่องคอมพิวเตอร์ในร้าน กรุณา Check-in เข้าเครื่องก่อนสั่งอาหาร
                    </p>
                </div>
                <a href="{{ route('customer.seat-map') }}" class="salai-btn-primary text-xs py-1.5 px-3.5">
                    เลือกที่นั่ง &rarr;
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Products & Menu Section -->
            <div class="lg:col-span-2 space-y-5">
                <div class="salai-card p-6 space-y-4">
                    <div class="salai-step-header">
                        <span class="salai-step-bar"></span>
                        <h2 class="text-xl font-bold text-white tracking-wide font-sans">
                            สั่งอาหารและเครื่องดื่ม (PC Bang Kitchen)
                        </h2>
                    </div>
                    <p class="text-xs text-zinc-400 pl-4">เลือกเมนูอาหารสไตล์เกาหลีและเครื่องดื่มเย็นฉ่ำ ส่งตรงถึงโต๊ะคอมพิวเตอร์ของคุณ</p>

                    <!-- Category Filters -->
                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-[#1e2430]">
                        <a
                            href="{{ route('customer.food-order') }}"
                            class="salai-pill text-xs font-medium transition {{ is_null($selectedCategoryId) ? 'bg-red-600 text-white' : 'bg-[#141824] text-zinc-300 hover:text-white border border-[#232938]' }}"
                        >
                            🔥 เมนูทั้งหมด
                        </a>
                        @foreach ($categories as $cat)
                            <a
                                href="{{ route('customer.food-order', ['category_id' => $cat->id]) }}"
                                class="salai-pill text-xs font-medium transition {{ $selectedCategoryId == $cat->id ? 'bg-red-600 text-white' : 'bg-[#141824] text-zinc-300 hover:text-white border border-[#232938]' }}"
                            >
                                {{ $cat->name }} ({{ $cat->products_count }})
                            </a>
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
                                        type="button"
                                        onclick="addToCart({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->price }}, {{ $prod->stock_quantity }})"
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

            <!-- Cart & Checkout Sidebar -->
            <div class="space-y-6">
                <div class="salai-card-glow p-6 rounded-3xl sticky top-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-[#232938] pb-3">
                        <div class="salai-step-header">
                            <span class="salai-step-bar"></span>
                            <h3 class="text-lg font-black text-white font-sans">ตะกร้าของคุณ (Cart)</h3>
                        </div>
                        <span id="cartCountBadge" class="salai-badge-red text-xs font-bold">
                            0 รายการ
                        </span>
                    </div>

                    <!-- Seat Destination -->
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
                            <a href="{{ route('customer.seat-map') }}" class="salai-btn-primary text-xs py-2 px-4 w-full block text-center">
                                🖥️ ไปที่ผังที่นั่งเพื่อ Check-in
                            </a>
                        </div>
                    @endif

                    <!-- Cart Items List Container -->
                    <div id="cartItemsList" class="space-y-3 max-h-72 overflow-y-auto divide-y divide-[#1e2430]">
                        <div class="py-8 text-center text-zinc-500 text-xs" id="emptyCartNotice">
                            🛒 ยังไม่มีอาหารในตะกร้า
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <form method="POST" action="{{ route('customer.place-order') }}" id="checkoutForm" class="space-y-4">
                        @csrf
                        <input type="hidden" name="seat_id" value="{{ $activeSeat ? $activeSeat->id : '' }}">
                        <div id="formHiddenInputs"></div>

                        <!-- Payment Method Selection -->
                        <div class="pt-3 border-t border-[#232938] space-y-2">
                            <label class="block text-xs font-bold text-zinc-300">ช่องทางการชำระเงิน:</label>

                            <label class="flex items-start gap-2.5 p-2.5 border rounded-xl cursor-pointer text-xs border-[#232938] bg-[#141824] hover:border-red-500/50">
                                <input type="radio" name="payment_method" value="wallet" checked class="mt-0.5 text-red-600 focus:ring-red-500">
                                <div class="flex flex-col">
                                    <span class="font-bold text-white">ตัดเงินใน Wallet</span>
                                    <span class="text-[11px] text-zinc-400">
                                        ยอดที่ใช้ได้: <span class="text-amber-400 font-bold">฿{{ number_format($availableBalance, 2) }}</span>
                                    </span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-2.5 border rounded-xl cursor-pointer text-xs border-[#232938] bg-[#141824] hover:border-red-500/50">
                                <input type="radio" name="payment_method" value="promptpay" class="text-red-600 focus:ring-red-500">
                                <span class="font-bold text-white">สแกน QR PromptPay (จำลอง)</span>
                            </label>

                            <label class="flex items-center gap-2.5 p-2.5 border rounded-xl cursor-pointer text-xs border-[#232938] bg-[#141824] hover:border-red-500/50">
                                <input type="radio" name="payment_method" value="cash" class="text-red-600 focus:ring-red-500">
                                <span class="font-bold text-white">เงินสดเก็บปลายทางที่โต๊ะ (Cash)</span>
                            </label>
                        </div>

                        <!-- Total & Checkout Button -->
                        <div class="pt-4 border-t border-[#232938] space-y-3">
                            <div class="flex justify-between items-center text-sm font-bold">
                                <span class="text-zinc-300">ยอดรวมทั้งสิ้น:</span>
                                <span class="text-2xl font-bold text-white font-mono" id="cartTotalText">฿0.00</span>
                            </div>

                            <button
                                type="submit"
                                id="btnSubmitOrder"
                                @disabled(! $activeSeat)
                                class="salai-btn-primary w-full py-2.5 text-xs disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                @if (! $activeSeat)
                                    กรุณาเปิดเครื่องก่อนสั่งอาหาร
                                @else
                                    🚀 ยืนยันการสั่งอาหาร (ส่งไปที่เครื่อง {{ $activeSeat->seat_number }})
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = {};

        function addToCart(productId, name, price, maxStock) {
            if (!cart[productId]) {
                cart[productId] = { id: productId, name: name, price: price, quantity: 1, maxStock: maxStock };
            } else {
                if (cart[productId].quantity < maxStock) {
                    cart[productId].quantity += 1;
                } else {
                    alert('สินค้ามีในสต็อกเพียง ' + maxStock + ' ชิ้น');
                }
            }
            renderCart();
        }

        function changeQty(productId, delta) {
            if (cart[productId]) {
                const newQty = cart[productId].quantity + delta;
                if (newQty <= 0) {
                    delete cart[productId];
                } else if (newQty > cart[productId].maxStock) {
                    alert('สินค้ามีในสต็อกเพียง ' + cart[productId].maxStock + ' ชิ้น');
                } else {
                    cart[productId].quantity = newQty;
                }
                renderCart();
            }
        }

        function removeCartItem(productId) {
            delete cart[productId];
            renderCart();
        }

        function renderCart() {
            const listEl = document.getElementById('cartItemsList');
            const hiddenInputsEl = document.getElementById('formHiddenInputs');
            const countBadge = document.getElementById('cartCountBadge');
            const totalText = document.getElementById('cartTotalText');
            const submitBtn = document.getElementById('btnSubmitOrder');

            listEl.innerHTML = '';
            hiddenInputsEl.innerHTML = '';

            const keys = Object.keys(cart);
            let total = 0;
            let totalCount = 0;

            if (keys.length === 0) {
                listEl.innerHTML = '<div class="py-8 text-center text-zinc-500 text-xs">🛒 ยังไม่มีอาหารในตะกร้า</div>';
                countBadge.innerText = '0 รายการ';
                totalText.innerText = '฿0.00';
                return;
            }

            keys.forEach((pId, idx) => {
                const item = cart[pId];
                const subtotal = item.price * item.quantity;
                total += subtotal;
                totalCount += item.quantity;

                // Hidden input for standard Form POST
                hiddenInputsEl.innerHTML += `<input type="hidden" name="items[${idx}][product_id]" value="${item.id}">`;
                hiddenInputsEl.innerHTML += `<input type="hidden" name="items[${idx}][quantity]" value="${item.quantity}">`;

                // Cart item row
                listEl.innerHTML += `
                    <div class="pt-3 first:pt-0 flex items-center justify-between gap-2 text-xs">
                        <div class="flex-1">
                            <p class="font-bold text-white">${item.name}</p>
                            <p class="text-[11px] text-zinc-400 font-mono">฿${item.price.toFixed(2)} x ${item.quantity}</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="changeQty(${item.id}, -1)" class="w-6 h-6 rounded-lg bg-[#141824] border border-[#232938] text-zinc-200 flex items-center justify-center font-bold text-xs hover:border-red-500">&minus;</button>
                            <span class="w-6 text-center font-bold text-xs text-white">${item.quantity}</span>
                            <button type="button" onclick="changeQty(${item.id}, 1)" class="w-6 h-6 rounded-lg bg-[#141824] border border-[#232938] text-zinc-200 flex items-center justify-center font-bold text-xs hover:border-red-500">+</button>
                            <button type="button" onclick="removeCartItem(${item.id})" class="text-xs text-red-400 hover:text-red-300 ml-1">&times;</button>
                        </div>
                        <span class="font-bold text-xs text-white min-w-12 text-right font-mono">
                            ฿${subtotal.toFixed(2)}
                        </span>
                    </div>
                `;
            });

            countBadge.innerText = totalCount + ' ชิ้น (' + keys.length + ' รายการ)';
            totalText.innerText = '฿' + total.toFixed(2);
        }

        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            if (Object.keys(cart).length === 0) {
                e.preventDefault();
                alert('กรุณาเลือกอาหารใส่ตะกร้าก่อนสั่งซื้อ');
            }
        });
    </script>
</x-layouts::app>
