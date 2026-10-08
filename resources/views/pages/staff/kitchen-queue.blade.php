<x-layouts::app :title="__('Kitchen Queue')">
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

        <!-- Header & Filter Tabs -->
        <div class="card bg-dark border-secondary-subtle rounded-4 p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 shadow-sm">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="spinner-grow spinner-grow-sm text-warning" style="width: 10px; height: 10px;"></span>
                    <h2 class="h4 fw-bold text-white m-0">คิวออเดอร์ห้องครัว (Kitchen Queue)</h2>
                </div>
                <small class="text-secondary ps-3">มอนิเตอร์และจัดการสถานะอาหารที่ลูกค้าสั่งจากเครื่องคอมพิวเตอร์</small>
            </div>

            <!-- Filter Buttons -->
            <div class="d-flex flex-wrap gap-2">
                <a
                    href="{{ route('staff.kitchen-queue', ['status' => 'active']) }}"
                    class="btn btn-sm rounded-pill px-3 fw-semibold {{ $filterStatus === 'active' ? 'btn-warning text-dark' : 'btn-outline-secondary text-light' }}"
                >
                    กำลังรอทำ/กำลังทำ ({{ $pendingCount + $preparingCount }})
                </a>
                <a
                    href="{{ route('staff.kitchen-queue', ['status' => 'served']) }}"
                    class="btn btn-sm rounded-pill px-3 fw-semibold {{ $filterStatus === 'served' ? 'btn-success' : 'btn-outline-secondary text-light' }}"
                >
                    เสิร์ฟแล้ว
                </a>
                <a
                    href="{{ route('staff.kitchen-queue', ['status' => 'all']) }}"
                    class="btn btn-sm rounded-pill px-3 fw-semibold {{ $filterStatus === 'all' ? 'btn-danger' : 'btn-outline-secondary text-light' }}"
                >
                    ทั้งหมด
                </a>
            </div>
        </div>

        <!-- Orders Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @forelse ($orders as $order)
                <div class="col">
                    <div class="card h-100 bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm d-flex flex-column justify-content-between">
                        <div>
                            <!-- Order Top Banner -->
                            <div class="d-flex justify-content-between align-items-start border-bottom border-secondary-subtle pb-3 mb-3">
                                <div>
                                    <small class="text-secondary fw-bold" style="font-size: 11px;">ออเดอร์ #{{ $order->id }}</small>
                                    <h3 class="h4 fw-black text-white m-0">
                                        โต๊ะ {{ $order->seat ? $order->seat->seat_number : '-' }}
                                    </h3>
                                    <small class="text-secondary" style="font-size: 11px;">
                                        {{ $order->user ? $order->user->name : '-' }} ({{ $order->created_at->format('H:i:s') }})
                                    </small>
                                </div>

                                <div class="text-end">
                                    <span class="badge
                                        @if($order->order_status === 'pending') bg-warning-subtle text-warning border border-warning-subtle
                                        @elseif($order->order_status === 'preparing') bg-info-subtle text-info border border-info-subtle
                                        @elseif($order->order_status === 'served') bg-success-subtle text-success border border-success-subtle
                                        @else bg-danger-subtle text-danger border border-danger-subtle
                                        @endif
                                    " style="font-size: 10px;">
                                        {{ strtoupper($order->order_status) }}
                                    </span>

                                    <div class="mt-1">
                                        <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}" style="font-size: 9px;">
                                            {{ $order->payment_status === 'paid' ? 'จ่ายแล้ว (' . $order->payment_method . ')' : 'รอเก็บเงิน (' . $order->payment_method . ')' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Items List -->
                            <div class="d-flex flex-column gap-2 mb-3">
                                @foreach ($order->items as $item)
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span class="text-light">
                                            {{ $item->product ? $item->product->name : 'สินค้า' }} <strong class="text-danger">x{{ $item->quantity }}</strong>
                                        </span>
                                        <span class="text-secondary font-monospace" style="font-size: 11px;">
                                            ฿{{ number_format($item->subtotal, 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Footer & Action Buttons -->
                        <div class="pt-3 border-top border-secondary-subtle">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-secondary small">ยอดรวม:</span>
                                <span class="fs-5 fw-bold text-white font-monospace">฿{{ number_format($order->total_amount, 2) }}</span>
                            </div>

                            <!-- Cash Confirmation if pending -->
                            @if ($order->payment_status === 'pending_payment')
                                <form method="POST" action="{{ route('staff.confirm-cash') }}" class="mb-2">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold rounded-pill text-dark">
                                        ยืนยันรับเงินสดแล้ว (฿{{ number_format($order->total_amount, 2) }})
                                    </button>
                                </form>
                            @endif

                            <!-- Status Workflow Buttons -->
                            <div class="row g-2">
                                @if ($order->order_status === 'pending')
                                    <div class="col-7">
                                        <form method="POST" action="{{ route('staff.update-order-status') }}">
                                            @csrf
                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            <input type="hidden" name="status" value="preparing">
                                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold rounded-pill">
                                                กำลังทำอาหาร &rarr;
                                            </button>
                                        </form>
                                    </div>

                                    <div class="col-5">
                                        <form method="POST" action="{{ route('staff.update-order-status') }}" onsubmit="return confirm('คุณต้องการยกเลิกออเดอร์นี้หรือไม่?');">
                                            @csrf
                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill">
                                                ยกเลิก
                                            </button>
                                        </form>
                                    </div>
                                @elseif ($order->order_status === 'preparing')
                                    <div class="col-12">
                                        <form method="POST" action="{{ route('staff.update-order-status') }}">
                                            @csrf
                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            <input type="hidden" name="status" value="served">
                                            <button type="submit" class="btn btn-success btn-sm w-100 fw-bold rounded-pill">
                                                เสิร์ฟที่โต๊ะเรียบร้อยแล้ว &check;
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card bg-dark border-secondary-subtle rounded-4 p-5 text-center text-secondary small">
                        ไม่มีออเดอร์อาหารในสถานะนี้
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts::app>
