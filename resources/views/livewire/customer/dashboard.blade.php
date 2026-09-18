<div class="space-y-6">
    <!-- Notifications -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-zinc-800 dark:text-green-400 border border-green-200 dark:border-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-zinc-800 dark:text-red-400 border border-red-200 dark:border-red-800">
            {{ session('error') }}
        </div>
    @endif

    <!-- Profile & Wallet Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">ยินดีต้อนรับ</p>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $user->name }}</h2>
            <div class="mt-2 flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                    Role: {{ strtoupper($user->role) }}
                </span>
                <span class="text-xs text-zinc-400">@ {{ $user->username }}</span>
            </div>
        </div>

        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex flex-col justify-between">
            <div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">ยอดเงินคงเหลือในกระเป๋า (Wallet)</p>
                <div class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">
                    ฿{{ number_format($user->balance, 2) }}
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('customer.topup') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition" wire:navigate>
                    + เติมเงิน / ซื้อแพ็กเกจ
                </a>
            </div>
        </div>

        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex flex-col justify-between">
            <div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">แพ็กเกจชั่วโมงสะสม</p>
                <div class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">
                    {{ $userPackages->sum('remaining_minutes') }} <span class="text-base font-normal text-zinc-400">นาที</span>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('customer.seat-map') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition" wire:navigate>
                    เปิดดูผังที่นั่ง (Seat Map)
                </a>
            </div>
        </div>
    </div>

    <!-- Active Session Section -->
    <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
        <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-4">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                <span class="w-3 h-3 rounded-full {{ $activeSession ? 'bg-green-500 animate-pulse' : 'bg-zinc-400' }}"></span>
                สถานะการใช้งานเครื่องปัจจุบัน
            </h3>
            @if ($activeSession)
                <span class="text-xs px-2.5 py-1 rounded-md bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 font-semibold">
                    กำลังใช้งาน (ACTIVE)
                </span>
            @endif
        </div>

        @if ($activeSession)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">ที่นั่งคอมพิวเตอร์</p>
                    <p class="text-xl font-bold text-zinc-900 dark:text-white mt-1">{{ $activeSession->seat->seat_number }}</p>
                    <p class="text-xs text-zinc-400 mt-0.5">{{ $activeSession->seat->zone->name }}</p>
                </div>
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">เวลาที่เริ่มเล่น</p>
                    <p class="text-xl font-bold text-zinc-900 dark:text-white mt-1">
                        {{ \Illuminate\Support\Carbon::parse($activeSession->start_time)->format('H:i:s') }}
                    </p>
                    <p class="text-xs text-zinc-400 mt-0.5">เล่นไปแล้ว: {{ $elapsedMinutes }} นาที</p>
                </div>
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">โหมดการคิดเวลา</p>
                    @if ($activeSession->userPackage)
                        <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                            {{ $activeSession->userPackage->package->name }}
                        </p>
                        <p class="text-xs text-zinc-400 mt-0.5">เหลือในแพ็กเกจ: {{ $activeSession->userPackage->remaining_minutes }} นาที</p>
                    @else
                        <p class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-1">Pay-as-you-go</p>
                        <p class="text-xs text-zinc-400 mt-0.5">฿{{ number_format($activeSession->rate_snapshot, 2) }} / ชม.</p>
                    @endif
                </div>
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">ค่าบริการโดยประมาณขณะนี้</p>
                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                        ฿{{ number_format($estimatedCost, 2) }}
                    </p>
                    <p class="text-xs text-zinc-400 mt-0.5">ตัดเงินเมื่อเช็คเอาท์</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button
                    wire:click="checkOut"
                    wire:confirm="คุณต้องการเช็คเอาท์และปิดเซสชันการเล่นหรือไม่?"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow transition"
                >
                    ออกจากเครื่อง (Check-out)
                </button>

                <a
                    href="{{ route('customer.food-order') }}?seat_id={{ $activeSession->seat_id }}"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow transition"
                    wire:navigate
                >
                    สั่งอาหารส่งมาที่เครื่อง {{ $activeSession->seat->seat_number }}
                </a>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-zinc-500 dark:text-zinc-400 mb-4">คุณยังไม่ได้เปิดใช้งานเครื่องคอมพิวเตอร์ในขณะนี้</p>
                <a href="{{ route('customer.seat-map') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition" wire:navigate>
                    เลือกที่นั่งในร้านเพื่อ Check-in
                </a>
            </div>
        @endif
    </div>

    <!-- Active Packages List -->
    @if ($userPackages->isNotEmpty())
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">แพ็กเกจชั่วโมงที่คุณถืออยู่</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($userPackages as $upkg)
                    <div class="p-4 border border-indigo-100 dark:border-indigo-900/50 bg-indigo-50/50 dark:bg-zinc-800/60 rounded-xl">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-bold text-zinc-900 dark:text-white">{{ $upkg->package->name }}</p>
                                <p class="text-xs text-zinc-500 mt-1">ซื้อเมื่อ: {{ $upkg->purchased_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400">
                                {{ $upkg->remaining_minutes }} นาที
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Tables: Recent Transactions and Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Wallet History -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">ประวัติเงินในกระเป๋าล่าสุด</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs uppercase text-zinc-500">
                        <tr>
                            <th class="py-3 px-3">ประเภท</th>
                            <th class="py-3 px-3">จำนวน</th>
                            <th class="py-3 px-3">เวลา</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($transactions as $tx)
                            <tr>
                                <td class="py-2.5 px-3">
                                    @if ($tx->type === 'topup')
                                        <span class="text-xs font-semibold text-green-600 dark:text-green-400">เติมเงิน ({{ $tx->ref_type }})</span>
                                    @elseif ($tx->type === 'deduct')
                                        <span class="text-xs font-semibold text-red-600 dark:text-red-400">หักเงิน ({{ $tx->ref_type }})</span>
                                    @else
                                        <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">คืนเงิน</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 font-semibold {{ $tx->type === 'topup' ? 'text-green-600' : 'text-zinc-900 dark:text-white' }}">
                                    {{ $tx->type === 'topup' ? '+' : '-' }}฿{{ number_format($tx->amount, 2) }}
                                </td>
                                <td class="py-2.5 px-3 text-xs text-zinc-400">
                                    {{ $tx->created_at->format('d/m H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-zinc-400 text-xs">ไม่มีรายการธุรกรรม</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Food Orders -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">คำสั่งซื้ออาหารล่าสุด</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs uppercase text-zinc-500">
                        <tr>
                            <th class="py-3 px-3">เลขบิล / โต๊ะ</th>
                            <th class="py-3 px-3">ยอดรวม</th>
                            <th class="py-3 px-3">สถานะอาหาร</th>
                            <th class="py-3 px-3">สถานะเงิน</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($recentOrders as $ord)
                            <tr>
                                <td class="py-2.5 px-3">
                                    <span class="font-medium text-zinc-900 dark:text-white">#{{ $ord->id }}</span>
                                    <span class="text-xs text-zinc-400 block">{{ $ord->seat->seat_number }}</span>
                                </td>
                                <td class="py-2.5 px-3 font-semibold text-zinc-900 dark:text-white">
                                    ฿{{ number_format($ord->total_amount, 2) }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="text-xs px-2 py-0.5 rounded font-medium
                                        @if($ord->order_status === 'served') bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                                        @elseif($ord->order_status === 'preparing') bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300
                                        @elseif($ord->order_status === 'cancelled') bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                                        @else bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300
                                        @endif
                                    ">
                                        {{ $ord->order_status }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="text-xs px-2 py-0.5 rounded font-medium {{ $ord->payment_status === 'paid' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300' }}">
                                        {{ $ord->payment_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-zinc-400 text-xs">ยังไม่มีคำสั่งซื้ออาหาร</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
