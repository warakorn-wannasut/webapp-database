<x-layouts::app :title="__('Customer Dashboard')">
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

        <!-- Profile & Wallet Summary Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- User Profile Card -->
            <div class="salai-card p-6 flex flex-col justify-between">
                <div class="space-y-2">
                    <span class="salai-badge-red text-[10px]">MEMBER PROFILE</span>
                    <p class="text-xs text-zinc-400">ยินดีต้อนรับเข้าสู่ระบบ</p>
                    <h2 class="text-2xl font-black text-white tracking-wide font-sans">{{ $user->name }}</h2>
                </div>
                <div class="mt-4 pt-3 border-t border-[#1e2430] flex items-center justify-between text-xs">
                    <span class="font-mono text-zinc-400">@ {{ $user->username }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-600/20 text-red-400 border border-red-500/30">
                        {{ strtoupper($user->role) }}
                    </span>
                </div>
            </div>

            <!-- Wallet Balance Card -->
            <div class="salai-card-glow p-6 rounded-2xl flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-zinc-400">ยอดเงินในกระเป๋า (Wallet)</span>
                        <span class="salai-badge-gold text-[10px]">฿ THB</span>
                    </div>
                    <div class="text-3xl font-bold text-amber-400 font-mono">
                        ฿{{ number_format($user->balance, 2) }}
                    </div>
                    @if(isset($availableBalance) && $availableBalance < (float)$user->balance)
                        <div class="space-y-1 pt-1">
                            <p class="text-xs text-emerald-400 font-semibold flex items-center justify-between">
                                <span>คงเหลือใช้สั่งอาหาร:</span>
                                <span class="font-mono font-bold">฿{{ number_format($availableBalance, 2) }}</span>
                            </p>
                            <p class="text-[10px] text-zinc-500">
                                (กันไว้สำหรับค่าบริการเครื่อง: ฿{{ number_format((float)$user->balance - $availableBalance, 2) }})
                            </p>
                        </div>
                    @else
                        <p class="text-[11px] text-zinc-400">ใช้จ่ายค่าชั่วโมงและสั่งอาหารได้ทันที</p>
                    @endif
                </div>
                <div class="mt-4 pt-3 border-t border-[#232938]">
                    <a href="{{ route('customer.topup') }}" class="salai-btn-primary text-xs w-full py-2 block text-center">
                        + เติมเงิน Wallet อัตโนมัติ
                    </a>
                </div>
            </div>

            <!-- Packages Card -->
            <div class="salai-card p-6 flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-zinc-400">แพ็กเกจชั่วโมงสะสม</span>
                        <span class="salai-badge-red text-[10px]">TIME PACK</span>
                    </div>
                    <div class="text-3xl font-bold text-white font-mono">
                        {{ $userPackages->sum('remaining_minutes') }} <span class="text-sm font-normal text-zinc-400 font-sans">นาที</span>
                    </div>
                    <p class="text-[11px] text-zinc-400">หักเวลาอัตโนมัติเมื่อเช็คอินในร้าน</p>
                </div>
                <div class="mt-4 pt-3 border-t border-[#1e2430]">
                    <a href="{{ route('customer.seat-map') }}" class="salai-card text-xs font-semibold py-2 w-full text-center block text-zinc-200 hover:text-white border-[#262d3d] hover:border-red-500/50">
                        🖥️ เปิดดูผังที่นั่ง (Seat Map)
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Session Section (PC Bang HUD) -->
        <div class="salai-card p-6">
            <div class="flex flex-wrap items-center justify-between border-b border-[#1e2430] pb-4 mb-5 gap-3">
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h3 class="text-lg font-bold text-white tracking-wide font-sans">
                        สถานะการใช้งานเครื่องคอมพิวเตอร์ปัจจุบัน
                    </h3>
                </div>
                @if ($activeSession)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        กำลังใช้งาน (ACTIVE)
                    </span>
                @endif
            </div>

            @if ($activeSession)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 bg-[#141824] border border-[#232938] rounded-xl space-y-1">
                        <p class="text-[11px] text-zinc-400">ที่นั่งคอมพิวเตอร์</p>
                        <p class="text-2xl font-black text-white">{{ $activeSession->seat->seat_number }}</p>
                        <p class="text-xs text-red-400 font-semibold">{{ $activeSession->seat->zone->name }}</p>
                    </div>

                    <div class="p-4 bg-[#141824] border border-[#232938] rounded-xl space-y-1">
                        <p class="text-[11px] text-zinc-400">เวลาที่เริ่มเล่น</p>
                        <p class="text-2xl font-black text-white font-mono">
                            {{ \Illuminate\Support\Carbon::parse($activeSession->start_time)->format('H:i:s') }}
                        </p>
                        <p class="text-xs text-zinc-400">เล่นไปแล้ว: <span class="font-bold text-white">{{ $elapsedMinutes }}</span> นาที</p>
                    </div>

                    <div class="p-4 bg-[#141824] border border-[#232938] rounded-xl space-y-1">
                        <p class="text-[11px] text-zinc-400">โหมดการคิดเงิน</p>
                        @if ($activeSession->userPackage)
                            <p class="text-base font-bold text-red-400">
                                {{ $activeSession->userPackage->package->name }}
                            </p>
                            <p class="text-xs text-zinc-400">เหลือ: {{ $activeSession->userPackage->remaining_minutes }} นาที</p>
                        @else
                            <p class="text-base font-bold text-amber-400">Pay-as-you-go</p>
                            <p class="text-xs text-zinc-400">฿{{ number_format($activeSession->rate_snapshot, 2) }} / ชม.</p>
                        @endif
                    </div>

                    <div class="p-4 bg-[#141824] border border-[#232938] rounded-xl space-y-1">
                        <p class="text-[11px] text-zinc-400">ค่าบริการขณะนี้ (โดยประมาณ)</p>
                        <p class="text-2xl font-black text-emerald-400 font-mono">
                            ฿{{ number_format($estimatedCost, 2) }}
                        </p>
                        <p class="text-xs text-zinc-400">ตัดยอดเมื่อกดยืนยัน Check-out</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <form method="POST" action="{{ route('customer.check-out') }}" onsubmit="return confirm('คุณต้องการเช็คเอาท์และปิดเซสชันการเล่นหรือไม่?');">
                        @csrf
                        <button type="submit" class="salai-btn-primary text-xs py-2.5 px-5">
                            🚪 ออกจากเครื่อง (Check-out)
                        </button>
                    </form>

                    <a
                        href="{{ route('customer.food-order') }}?seat_id={{ $activeSession->seat_id }}"
                        class="salai-card text-xs font-semibold px-5 py-2.5 text-zinc-200 hover:text-white border-[#262d3d] hover:border-red-500/50 inline-flex items-center gap-1.5"
                    >
                        🍜 สั่งอาหารส่งมาที่เครื่อง {{ $activeSession->seat->seat_number }}
                    </a>
                </div>
            @else
                <div class="text-center py-10 space-y-3">
                    <div class="text-4xl">🖥️</div>
                    <p class="text-sm text-zinc-400">คุณยังไม่ได้เปิดใช้งานเครื่องคอมพิวเตอร์ในขณะนี้</p>
                    <div class="pt-2">
                        <a href="{{ route('customer.seat-map') }}" class="salai-btn-primary text-xs py-2.5 px-6">
                            เลือกที่นั่งในร้านเพื่อเริ่มต้นเล่น (Check-in)
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Active Packages List -->
        @if ($userPackages->isNotEmpty())
            <div class="salai-card p-6 space-y-4">
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h3 class="text-lg font-black text-white tracking-wide font-sans">
                        แพ็กเกจชั่วโมงที่คุณถืออยู่
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($userPackages as $upkg)
                        <div class="p-4 bg-[#141824] border border-[#232938] rounded-xl flex items-center justify-between">
                            <div>
                                <p class="font-bold text-white text-sm">{{ $upkg->package->name }}</p>
                                <p class="text-[11px] text-zinc-400 mt-0.5">ซื้อเมื่อ: {{ $upkg->purchased_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="text-sm font-black text-red-400 bg-red-500/10 px-2.5 py-1 rounded-lg border border-red-500/20">
                                {{ $upkg->remaining_minutes }} นาที
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tables: Recent Transactions and Recent Orders -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Wallet History -->
            <div class="salai-card p-6 space-y-4">
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h3 class="text-base font-black text-white tracking-wide font-sans">
                        ประวัติเงินในกระเป๋าล่าสุด
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-300">
                        <thead class="bg-[#141824] text-[11px] uppercase text-zinc-400 border-b border-[#1e2430]">
                            <tr>
                                <th class="py-2.5 px-3">ประเภท</th>
                                <th class="py-2.5 px-3">จำนวน</th>
                                <th class="py-2.5 px-3">เวลา</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1e2430]">
                            @forelse ($transactions as $tx)
                                <tr class="hover:bg-[#141824]/60 transition">
                                    <td class="py-2.5 px-3">
                                        @if ($tx->type === 'topup')
                                            <span class="salai-badge-red text-[10px]">เติมเงิน ({{ $tx->ref_type }})</span>
                                        @elseif ($tx->type === 'deduct')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-800 text-zinc-300 border border-zinc-700">หักเงิน ({{ $tx->ref_type }})</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">คืนเงิน</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 font-bold font-mono {{ $tx->type === 'topup' ? 'text-emerald-400' : 'text-zinc-200' }}">
                                        {{ $tx->type === 'topup' ? '+' : '-' }}฿{{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-[11px] text-zinc-400 font-mono">
                                        {{ $tx->created_at->format('d/m H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-zinc-500 text-xs">ไม่มีรายการธุรกรรม</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Food Orders -->
            <div class="salai-card p-6 space-y-4">
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h3 class="text-base font-black text-white tracking-wide font-sans">
                        คำสั่งซื้ออาหารล่าสุด
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-300">
                        <thead class="bg-[#141824] text-[11px] uppercase text-zinc-400 border-b border-[#1e2430]">
                            <tr>
                                <th class="py-2.5 px-3">เลขบิล / โต๊ะ</th>
                                <th class="py-2.5 px-3">ยอดรวม</th>
                                <th class="py-2.5 px-3">สถานะอาหาร</th>
                                <th class="py-2.5 px-3">สถานะเงิน</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1e2430]">
                            @forelse ($recentOrders as $ord)
                                <tr class="hover:bg-[#141824]/60 transition">
                                    <td class="py-2.5 px-3">
                                        <span class="font-bold text-white">#{{ $ord->id }}</span>
                                        <span class="text-[10px] text-red-400 block font-semibold">{{ $ord->seat->seat_number }}</span>
                                    </td>
                                    <td class="py-2.5 px-3 font-bold text-white font-mono">
                                        ฿{{ number_format($ord->total_amount, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                            @if($ord->order_status === 'served') bg-emerald-500/20 text-emerald-400 border border-emerald-500/30
                                            @elseif($ord->order_status === 'preparing') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                            @elseif($ord->order_status === 'cancelled') bg-red-500/20 text-red-400 border border-red-500/30
                                            @else bg-amber-500/20 text-amber-400 border border-amber-500/30
                                            @endif
                                        ">
                                            {{ $ord->order_status }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $ord->payment_status === 'paid' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                                            {{ $ord->payment_status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-zinc-500 text-xs">ยังไม่มีคำสั่งซื้ออาหาร</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
