<x-layouts::app :title="__('Order Food & Beverages')">
    <div class="d-flex flex-column gap-4">
        <!-- Notifications -->
        @if (session()->has('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 border-success-subtle bg-success-subtle text-success py-3 px-4 rounded-3 m-0">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 border-danger-subtle bg-danger-subtle text-danger py-3 px-4 rounded-3 m-0">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <!-- Active Seat Banner -->
        @if ($activeSeat)
            <div class="alert alert-dark border-secondary-subtle bg-dark d-flex justify-content-between align-items-center p-3 rounded-4 shadow-sm m-0">
                <div class="d-flex align-items-center gap-2">
                    <span class="spinner-grow spinner-grow-sm text-success" style="width: 8px; height: 8px;"></span>
                    <small class="text-light">
                        กำลังเล่นอยู่ที่เครื่อง <strong class="text-white text-decoration-underline">{{ $activeSeat->seat_number }}</strong> ({{ $activeSeat->zone->name }}) &bull; อาหารจะถูกเสิร์ฟมาที่โต๊ะนี้โดยอัตโนมัติ
                    </small>
                </div>
                <span class="badge bg-danger-subtle text-danger d-none d-sm-inline-block">AUTO DELIVER</span>
            </div>
        @else
            <div class="alert alert-warning border-warning-subtle bg-warning-subtle text-warning d-flex justify-content-between align-items-center p-3 rounded-4 shadow-sm m-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <small class="fw-semibold">
                        คุณยังไม่ได้เปิดเครื่องคอมพิวเตอร์ในร้าน กรุณา Check-in เข้าเครื่องก่อนสั่งอาหาร
                    </small>
                </div>
                <a href="{{ route('customer.seat-map') }}" class="btn btn-warning btn-sm fw-bold px-3 rounded-pill">
                    เลือกที่นั่ง &rarr;
                </a>
            </div>
        @endif

        <div class="row g-4">
            <!-- Products & Menu Section -->
            <div class="col-12 col-lg-8 d-flex flex-column gap-4">
                <div class="card bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="bg-danger rounded" style="width: 4px; height: 22px;"></span>
                        <h2 class="h5 fw-bold text-white m-0">
                            สั่งอาหารและเครื่องดื่ม (PC Bang Kitchen)
                        </h2>
                    </div>
                    <small class="text-secondary ps-3 d-block mb-3">
                        เลือกเมนูอาหารตามสั่ง ของว่าง และเครื่องดื่มเย็นฉ่ำ ส่งตรงถึงโต๊ะคอมพิวเตอร์ของคุณ
                    </small>

                    <!-- Category Filters -->
                    <div class="d-flex flex-wrap gap-2 pt-3 border-top border-secondary-subtle">
                        <a
                            href="{{ route('customer.food-order') }}"
                            class="btn btn-sm rounded-pill px-3 fw-semibold {{ is_null($selectedCategoryId) ? 'btn-danger' : 'btn-outline-secondary text-light' }}"
                        >
                            🔥 เมนูทั้งหมด
                        </a>
                        @foreach ($categories as $cat)
                            <a
                                href="{{ route('customer.food-order', ['category_id' => $cat->id]) }}"
                                class="btn btn-sm rounded-pill px-3 fw-semibold {{ $selectedCategoryId == $cat->id ? 'btn-danger' : 'btn-outline-secondary text-light' }}"
                            >
                                {{ $cat->name }} ({{ $cat->products_count }})
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row row-cols-1 row-cols-sm-2 g-3">
                    @forelse ($products as $prod)
                        <div class="col">
                            <div class="card h-100 bg-dark border-secondary-subtle rounded-4 p-3 shadow-sm d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-danger-subtle text-danger" style="font-size: 10px;">{{ $prod->category->name }}</span>
                                        <small class="fw-bold font-monospace {{ $prod->stock_quantity > 0 ? 'text-success' : 'text-danger' }}" style="font-size: 11px;">
                                            {{ $prod->stock_quantity > 0 ? 'คงเหลือ ' . $prod->stock_quantity . ' ชิ้น' : 'สินค้าหมด' }}
                                        </small>
                                    </div>

                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 bg-dark-subtle border border-secondary-subtle d-flex align-items-center justify-content-center fs-3 flex-shrink-0" style="width: 52px; height: 52px;">
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
                                            <h4 class="h6 fw-bold text-white mb-1">{{ $prod->name }}</h4>
                                            @if ($prod->description)
                                                <small class="text-secondary d-block line-clamp-2" style="font-size: 11px;">{{ $prod->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-3 border-top border-secondary-subtle mt-3 d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold text-white font-monospace">
                                        ฿{{ number_format($prod->price, 2) }}
                                    </span>

                                    @if ($prod->stock_quantity > 0)
                                        <button
                                            type="button"
                                            onclick="addToCart({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->price }}, {{ $prod->stock_quantity }})"
                                            class="btn btn-danger btn-sm px-3 fw-bold rounded-pill"
                                        >
                                            + ใส่ตะกร้า
                                        </button>
                                    @else
                                        <button disabled class="btn btn-outline-secondary btn-sm px-3 disabled">
                                            หมด
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card bg-dark border-secondary-subtle rounded-4 p-5 text-center text-secondary small">
                                ไม่พบรายการอาหารในหมวดหมู่นี้
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Cart & Checkout Sidebar -->
            <div class="col-12 col-lg-4">
                <div class="card bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm sticky-top" style="top: 80px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary-subtle pb-3 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="bg-danger rounded" style="width: 4px; height: 20px;"></span>
                            <h3 class="h6 fw-bold text-white m-0">ตะกร้าของคุณ (Cart)</h3>
                        </div>
                        <span id="cartCountBadge" class="badge bg-danger-subtle text-danger font-monospace">
                            0 รายการ
                        </span>
                    </div>

                    <!-- Seat Destination -->
                    @if ($activeSeat)
                        <div class="p-3 bg-dark-subtle border border-danger-subtle rounded-3 d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-4">🖥️</span>
                                <div>
                                    <small class="text-secondary d-block" style="font-size: 10px;">จัดส่งตรงถึงโต๊ะคอม</small>
                                    <strong class="text-white small">เครื่อง {{ $activeSeat->seat_number }}</strong>
                                    <small class="text-danger fw-semibold d-block" style="font-size: 10px;">{{ $activeSeat->zone->name }}</small>
                                </div>
                            </div>
                            <span class="badge bg-danger-subtle text-danger" style="font-size: 9px;">ผูกเครื่องแล้ว</span>
                        </div>
                    @else
                        <div class="p-3 bg-warning-subtle border border-warning-subtle rounded-3 text-center mb-3 text-warning">
                            <small class="d-block fw-bold">คุณยังไม่ได้ Check-in เปิดเครื่อง</small>
                            <small class="text-secondary d-block mb-2" style="font-size: 11px;">ระบบจะจัดส่งอาหารไปยังเครื่องที่คุณนั่ง</small>
                            <a href="{{ route('customer.seat-map') }}" class="btn btn-warning btn-sm w-100 fw-bold">
                                🖥️ ไปเปิดเครื่องก่อน
                            </a>
                        </div>
                    @endif

                    <!-- Cart Items List Container -->
                    <div id="cartItemsList" class="overflow-y-auto mb-3" style="max-height: 240px;">
                        <div class="py-4 text-center text-secondary small" id="emptyCartNotice">
                            🛒 ยังไม่มีอาหารในตะกร้า
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <form method="POST" action="{{ route('customer.place-order') }}" id="checkoutForm" class="d-flex flex-column gap-3">
                        @csrf
                        <input type="hidden" name="seat_id" value="{{ $activeSeat ? $activeSeat->id : '' }}">
                        <div id="formHiddenInputs"></div>

                        <!-- Payment Method Selection -->
                        <div class="pt-3 border-top border-secondary-subtle">
                            <label class="form-label text-secondary small fw-bold text-uppercase" style="font-size: 11px;">ช่องทางการชำระเงิน:</label>

                            <div class="d-flex flex-column gap-2">
                                <label class="form-check p-2.5 rounded-3 border border-secondary-subtle bg-dark-subtle d-flex align-items-start gap-2 cursor-pointer transition">
                                    <input type="radio" name="payment_method" value="wallet" checked class="form-check-input mt-1">
                                    <div>
                                        <strong class="text-white small d-block">ตัดเงินใน Wallet</strong>
                                        <small class="text-secondary" style="font-size: 11px;">
                                            ใช้ได้: <span class="text-warning fw-bold">฿{{ number_format($availableBalance, 2) }}</span>
                                        </small>
                                    </div>
                                </label>

                                <label class="form-check p-2.5 rounded-3 border border-secondary-subtle bg-dark-subtle d-flex align-items-center gap-2 cursor-pointer transition">
                                    <input type="radio" name="payment_method" value="promptpay" class="form-check-input mt-0">
                                    <span class="text-white small fw-bold">สแกน QR PromptPay (จำลอง)</span>
                                </label>

                                <label class="form-check p-2.5 rounded-3 border border-secondary-subtle bg-dark-subtle d-flex align-items-center gap-2 cursor-pointer transition">
                                    <input type="radio" name="payment_method" value="cash" class="form-check-input mt-0">
                                    <span class="text-white small fw-bold">เงินสดเก็บปลายทางที่โต๊ะ (Cash)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Total & Checkout Button -->
                        <div class="pt-3 border-top border-secondary-subtle">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-secondary small">ยอดรวมทั้งสิ้น:</span>
                                <span class="fs-4 fw-bold text-white font-monospace" id="cartTotalText">฿0.00</span>
                            </div>

                            <button
                                type="submit"
                                id="btnSubmitOrder"
                                @disabled(! $activeSeat)
                                class="btn btn-danger w-100 py-2.5 fw-bold rounded-pill shadow"
                            >
                                @if (! $activeSeat)
                                    กรุณาเปิดเครื่องก่อนสั่งอาหาร
                                @else
                                    🚀 ยืนยันการสั่งอาหาร (ส่งโต๊ะ {{ $activeSeat->seat_number }})
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

            listEl.innerHTML = '';
            hiddenInputsEl.innerHTML = '';

            const keys = Object.keys(cart);
            let total = 0;
            let totalCount = 0;

            if (keys.length === 0) {
                listEl.innerHTML = '<div class="py-4 text-center text-secondary small">🛒 ยังไม่มีอาหารในตะกร้า</div>';
                countBadge.innerText = '0 รายการ';
                totalText.innerText = '฿0.00';
                return;
            }

            keys.forEach((pId, idx) => {
                const item = cart[pId];
                const subtotal = item.price * item.quantity;
                total += subtotal;
                totalCount += item.quantity;

                // Hidden input for form POST
                hiddenInputsEl.innerHTML += `<input type="hidden" name="items[${idx}][product_id]" value="${item.id}">`;
                hiddenInputsEl.innerHTML += `<input type="hidden" name="items[${idx}][quantity]" value="${item.quantity}">`;

                // Cart item row in Bootstrap
                listEl.innerHTML += `
                    <div class="py-2 border-bottom border-secondary-subtle d-flex justify-content-between align-items-center gap-2 small">
                        <div class="overflow-hidden flex-grow-1">
                            <strong class="text-white d-block text-truncate" style="font-size: 12px;">${item.name}</strong>
                            <small class="text-secondary font-monospace">฿${item.price.toFixed(2)} x ${item.quantity}</small>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" onclick="changeQty(${item.id}, -1)" class="btn btn-outline-secondary btn-sm py-0 px-2 fw-bold">&minus;</button>
                            <span class="text-white fw-bold px-1" style="font-size: 12px;">${item.quantity}</span>
                            <button type="button" onclick="changeQty(${item.id}, 1)" class="btn btn-outline-secondary btn-sm py-0 px-2 fw-bold">+</button>
                            <button type="button" onclick="removeCartItem(${item.id})" class="btn btn-link text-danger btn-sm p-0 ms-1 text-decoration-none">&times;</button>
                        </div>
                        <span class="fw-bold text-white font-monospace text-end" style="min-width: 55px; font-size: 12px;">
                            ฿${subtotal.toFixed(2)}
                        </span>
                    </div>
                `;
            });

            countBadge.innerText = totalCount + ' ชิ้น';
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
