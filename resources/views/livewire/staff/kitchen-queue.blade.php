<div wire:poll.5s class="space-y-6">
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

    <!-- Header & Filter Tabs -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500 animate-pulse"></span>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">คิวออเดอร์ห้องครัว (Kitchen Queue)</h2>
            </div>
            <p class="text-xs text-zinc-500 mt-1">มอนิเตอร์และจัดการสถานะอาหารที่ลูกค้าสั่งจากเครื่องคอมพิวเตอร์</p>
        </div>

        <!-- Filter Buttons -->
        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <button
                wire:click="$set('filterStatus', 'active')"
                class="px-3.5 py-1.5 rounded-xl transition {{ $filterStatus === 'active' ? 'bg-amber-600 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}"
            >
                กำลังรอทำ/กำลังทำ ({{ $pendingCount + $preparingCount }})
            </button>
            <button
                wire:click="$set('filterStatus', 'served')"
                class="px-3.5 py-1.5 rounded-xl transition {{ $filterStatus === 'served' ? 'bg-green-600 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}"
            >
                เสิร์ฟแล้ว
            </button>
            <button
                wire:click="$set('filterStatus', 'all')"
                class="px-3.5 py-1.5 rounded-xl transition {{ $filterStatus === 'all' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}"
            >
                ทั้งหมด
            </button>
        </div>
    </div>

    <!-- Orders Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($orders as $order)
            <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex flex-col justify-between shadow-sm space-y-4">
                <div>
                    <!-- Order Top Banner -->
                    <div class="flex justify-between items-start border-b border-zinc-100 dark:border-zinc-800 pb-3">
                        <div>
                            <span class="text-xs font-bold text-zinc-400">ออเดอร์ #{{ $order->id }}</span>
                            <h3 class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400">
                                โต๊ะ {{ $order->seat->seat_number }}
                            </h3>
                            <p class="text-xs text-zinc-500 mt-0.5">{{ $order->user->name }} ({{ $order->created_at->format('H:i:s') }})</p>
                        </div>

                        <div class="text-right space-y-1">
                            <span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded
                                @if($order->order_status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200
                                @elseif($order->order_status === 'preparing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($order->order_status === 'served') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @endif
                            ">
                                {{ strtoupper($order->order_status) }}
                            </span>

                            <div class="block">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                    {{ $order->payment_status === 'paid' ? 'จ่ายแล้ว (' . $order->payment_method . ')' : 'รอเก็บเงิน (' . $order->payment_method . ')' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="mt-3 space-y-2">
                        @foreach ($order->items as $item)
                            <div class="flex justify-between text-xs">
                                <span class="text-zinc-800 dark:text-zinc-200 font-medium">
                                    {{ $item->product->name }} <span class="font-bold text-indigo-600">x{{ $item->quantity }}</span>
                                </span>
                                <span class="text-zinc-500 font-semibold">
                                    ฿{{ number_format($item->subtotal, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer & Action Buttons -->
                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 space-y-2">
                    <div class="flex justify-between items-center text-sm font-bold">
                        <span class="text-zinc-500">ยอดรวม:</span>
                        <span class="text-base text-zinc-900 dark:text-white font-extrabold">฿{{ number_format($order->total_amount, 2) }}</span>
                    </div>

                    <!-- Cash Confirmation if pending -->
                    @if ($order->payment_status === 'pending_payment')
                        <button
                            wire:click="confirmCash({{ $order->id }})"
                            class="w-full py-1.5 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition shadow"
                        >
                            ยืนยันรับเงินสดแล้ว (฿{{ number_format($order->total_amount, 2) }})
                        </button>
                    @endif

                    <!-- Status Workflow Buttons -->
                    <div class="grid grid-cols-2 gap-2">
                        @if ($order->order_status === 'pending')
                            <button
                                wire:click="updateStatus({{ $order->id }}, 'preparing')"
                                class="py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition"
                            >
                                กำลังทำอาหาร &rarr;
                            </button>
                            <button
                                wire:click="updateStatus({{ $order->id }}, 'cancelled')"
                                wire:confirm="คุณต้องการยกเลิกออเดอร์นี้และคืนสต็อก/เงิน หรือไม่?"
                                class="py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 rounded-xl transition"
                            >
                                ยกเลิก
                            </button>
                        @elseif ($order->order_status === 'preparing')
                            <button
                                wire:click="updateStatus({{ $order->id }}, 'served')"
                                class="col-span-2 py-1.5 text-xs font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl transition"
                            >
                                เสิร์ฟที่โต๊ะเรียบร้อยแล้ว &check;
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-zinc-400">
                ไม่มีออเดอร์อาหารในสถานะนี้
            </div>
        @endforelse
    </div>

    <div>
        {{ $orders->links() }}
    </div>
</div>
