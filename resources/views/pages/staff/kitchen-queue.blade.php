<x-layouts::app :title="__('Kitchen Queue')">
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

        <!-- Header & Filter Tabs -->
        <div class="salai-card p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500 animate-pulse"></span>
                    <h2 class="text-2xl font-black text-white font-sans">คิวออเดอร์ห้องครัว (Kitchen Queue)</h2>
                </div>
                <p class="text-xs text-zinc-400 mt-1">มอนิเตอร์และจัดการสถานะอาหารที่ลูกค้าสั่งจากเครื่องคอมพิวเตอร์</p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-2 text-xs font-semibold">
                <a
                    href="{{ route('staff.kitchen-queue', ['status' => 'active']) }}"
                    class="px-3.5 py-1.5 rounded-xl transition {{ $filterStatus === 'active' ? 'bg-amber-600 text-white' : 'bg-[#141824] border border-[#232938] text-zinc-300 hover:text-white' }}"
                >
                    กำลังรอทำ/กำลังทำ ({{ $pendingCount + $preparingCount }})
                </a>
                <a
                    href="{{ route('staff.kitchen-queue', ['status' => 'served']) }}"
                    class="px-3.5 py-1.5 rounded-xl transition {{ $filterStatus === 'served' ? 'bg-emerald-600 text-white' : 'bg-[#141824] border border-[#232938] text-zinc-300 hover:text-white' }}"
                >
                    เสิร์ฟแล้ว
                </a>
                <a
                    href="{{ route('staff.kitchen-queue', ['status' => 'all']) }}"
                    class="px-3.5 py-1.5 rounded-xl transition {{ $filterStatus === 'all' ? 'bg-red-600 text-white' : 'bg-[#141824] border border-[#232938] text-zinc-300 hover:text-white' }}"
                >
                    ทั้งหมด
                </a>
            </div>
        </div>

        <!-- Orders Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($orders as $order)
                <div class="salai-card p-5 flex flex-col justify-between space-y-4">
                    <div>
                        <!-- Order Top Banner -->
                        <div class="flex justify-between items-start border-b border-[#1e2430] pb-3">
                            <div>
                                <span class="text-xs font-bold text-zinc-400">ออเดอร์ #{{ $order->id }}</span>
                                <h3 class="text-xl font-extrabold text-white">
                                    โต๊ะ {{ $order->seat ? $order->seat->seat_number : '-' }}
                                </h3>
                                <p class="text-xs text-zinc-400 mt-0.5">{{ $order->user ? $order->user->name : '-' }} ({{ $order->created_at->format('H:i:s') }})</p>
                            </div>

                            <div class="text-right space-y-1">
                                <span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded
                                    @if($order->order_status === 'pending') bg-amber-500/20 text-amber-400 border border-amber-500/30
                                    @elseif($order->order_status === 'preparing') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                    @elseif($order->order_status === 'served') bg-emerald-500/20 text-emerald-400 border border-emerald-500/30
                                    @else bg-red-500/20 text-red-400 border border-red-500/30
                                    @endif
                                ">
                                    {{ strtoupper($order->order_status) }}
                                </span>

                                <div class="block">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $order->payment_status === 'paid' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                                        {{ $order->payment_status === 'paid' ? 'จ่ายแล้ว (' . $order->payment_method . ')' : 'รอเก็บเงิน (' . $order->payment_method . ')' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Items List -->
                        <div class="mt-3 space-y-2">
                            @foreach ($order->items as $item)
                                <div class="flex justify-between text-xs">
                                    <span class="text-zinc-200 font-medium">
                                        {{ $item->product ? $item->product->name : 'สินค้า' }} <span class="font-bold text-red-400">x{{ $item->quantity }}</span>
                                    </span>
                                    <span class="text-zinc-400 font-semibold font-mono">
                                        ฿{{ number_format($item->subtotal, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Footer & Action Buttons -->
                    <div class="pt-3 border-t border-[#1e2430] space-y-2">
                        <div class="flex justify-between items-center text-sm font-bold">
                            <span class="text-zinc-400">ยอดรวม:</span>
                            <span class="text-base text-white font-extrabold font-mono">฿{{ number_format($order->total_amount, 2) }}</span>
                        </div>

                        <!-- Cash Confirmation if pending -->
                        @if ($order->payment_status === 'pending_payment')
                            <form method="POST" action="{{ route('staff.confirm-cash') }}">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <button
                                    type="submit"
                                    class="w-full py-1.5 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition shadow"
                                >
                                    ยืนยันรับเงินสดแล้ว (฿{{ number_format($order->total_amount, 2) }})
                                </button>
                            </form>
                        @endif

                        <!-- Status Workflow Buttons -->
                        <div class="grid grid-cols-2 gap-2">
                            @if ($order->order_status === 'pending')
                                <form method="POST" action="{{ route('staff.update-order-status') }}">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <input type="hidden" name="status" value="preparing">
                                    <button
                                        type="submit"
                                        class="w-full py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition"
                                    >
                                        กำลังทำอาหาร &rarr;
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('staff.update-order-status') }}" onsubmit="return confirm('คุณต้องการยกเลิกออเดอร์นี้หรือไม่?');">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <input type="hidden" name="status" value="cancelled">
                                    <button
                                        type="submit"
                                        class="w-full py-1.5 text-xs font-semibold text-red-400 bg-red-950/40 border border-red-500/30 hover:bg-red-900/60 rounded-xl transition"
                                    >
                                        ยกเลิก
                                    </button>
                                </form>
                            @elseif ($order->order_status === 'preparing')
                                <form method="POST" action="{{ route('staff.update-order-status') }}" class="col-span-2">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <input type="hidden" name="status" value="served">
                                    <button
                                        type="submit"
                                        class="w-full py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition"
                                    >
                                        เสิร์ฟที่โต๊ะเรียบร้อยแล้ว &check;
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-12 text-center salai-card text-zinc-500 text-xs">
                    ไม่มีออเดอร์อาหารในสถานะนี้
                </div>
            @endforelse
        </div>
    </div>
</x-layouts::app>
