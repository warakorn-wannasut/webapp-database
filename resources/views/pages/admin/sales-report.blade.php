<x-layouts::app :title="__('Sales Report')">
    <div class="space-y-6">
        <!-- Notifications -->
        @if (session()->has('success'))
            <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-sm flex items-center gap-2">
                <span class="text-base">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Header & Period Filter -->
        <div class="salai-card p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h2 class="text-2xl font-black text-white font-sans">รายงานสรุปยอดขาย (Sales & Analytics)</h2>
                </div>
                <p class="text-xs text-zinc-400 pl-4 mt-1">สรุปรายได้แยกตามค่าชั่วโมงเล่นเกมและค่าอาหาร/เครื่องดื่ม (Aggregation)</p>
            </div>

            <div class="flex gap-2 text-xs font-semibold">
                <a
                    href="{{ route('admin.sales-report', ['period' => 'today']) }}"
                    class="px-3.5 py-1.5 rounded-xl transition {{ $period === 'today' ? 'bg-red-600 text-white' : 'bg-[#141824] border border-[#232938] text-zinc-300 hover:text-white' }}"
                >
                    วันนี้
                </a>
                <a
                    href="{{ route('admin.sales-report', ['period' => '7days']) }}"
                    class="px-3.5 py-1.5 rounded-xl transition {{ $period === '7days' ? 'bg-red-600 text-white' : 'bg-[#141824] border border-[#232938] text-zinc-300 hover:text-white' }}"
                >
                    7 วันล่าสุด
                </a>
                <a
                    href="{{ route('admin.sales-report', ['period' => 'month']) }}"
                    class="px-3.5 py-1.5 rounded-xl transition {{ $period === 'month' ? 'bg-red-600 text-white' : 'bg-[#141824] border border-[#232938] text-zinc-300 hover:text-white' }}"
                >
                    30 วันล่าสุด
                </a>
                <a
                    href="{{ route('admin.sales-report', ['period' => 'all']) }}"
                    class="px-3.5 py-1.5 rounded-xl transition {{ $period === 'all' ? 'bg-red-600 text-white' : 'bg-[#141824] border border-[#232938] text-zinc-300 hover:text-white' }}"
                >
                    ทั้งหมด
                </a>
            </div>
        </div>

        <!-- Revenue Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Grand Total -->
            <div class="p-6 bg-gradient-to-br from-red-600 to-red-900 text-white rounded-2xl shadow-lg border border-red-500/40">
                <p class="text-xs font-semibold text-red-200 uppercase tracking-wider">รายได้รวมทั้งหมด (Total Revenue)</p>
                <h3 class="text-3xl font-black mt-2 font-mono">฿{{ number_format($grandTotalRevenue, 2) }}</h3>
                <p class="text-[11px] text-red-200 mt-2">ค่าชั่วโมง + แพ็กเกจ + อาหาร</p>
            </div>

            <!-- Gaming Revenue -->
            <div class="salai-card p-6">
                <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">รายได้ค่าชั่วโมงเล่นเกม</p>
                <h3 class="text-2xl font-black text-white mt-2 font-mono">฿{{ number_format($totalGamingRevenue, 2) }}</h3>
                <div class="mt-2 text-xs text-zinc-400 space-y-0.5">
                    <p>• รายชั่วโมง: ฿{{ number_format($hourlyRevenue, 2) }} ({{ $completedSessionsCount }} เซสชัน)</p>
                    <p>• ขายแพ็กเกจ: ฿{{ number_format($packageRevenue, 2) }} ({{ $packageSalesCount }} บิล)</p>
                </div>
            </div>

            <!-- Food Revenue -->
            <div class="salai-card p-6">
                <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">รายได้อาหารและเครื่องดื่ม</p>
                <h3 class="text-2xl font-black text-white mt-2 font-mono">฿{{ number_format($foodRevenue, 2) }}</h3>
                <p class="text-xs text-zinc-400 mt-2">จำนวนออเดอร์ที่ชำระแล้ว: {{ $paidOrdersCount }} บิล</p>
            </div>

            <!-- Topup Total -->
            <div class="salai-card p-6">
                <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">ยอดเงินที่เติมเข้า Wallet</p>
                <h3 class="text-2xl font-black text-emerald-400 mt-2 font-mono">฿{{ number_format($totalTopup, 2) }}</h3>
                <p class="text-xs text-zinc-400 mt-2">กระแสเงินสดรับเข้าระบบ</p>
            </div>
        </div>

        <!-- Data Tables: Top Products and Zones -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top 5 Products -->
            <div class="salai-card p-6 space-y-4">
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h3 class="text-base font-bold text-white">5 อันดับเมนูขายดี (Top 5 Best Selling)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-300">
                        <thead class="bg-[#141824] text-xs uppercase text-zinc-400 border-b border-[#1e2430]">
                            <tr>
                                <th class="py-2.5 px-3">อันดับ / เมนู</th>
                                <th class="py-2.5 px-3 text-center">จำนวนที่ขายได้</th>
                                <th class="py-2.5 px-3 text-right">ยอดขายรวม</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1e2430]">
                            @forelse ($topProducts as $idx => $tp)
                                <tr>
                                    <td class="py-3 px-3 font-semibold text-white">
                                        <span class="inline-block w-5 text-red-500 font-extrabold">#{{ $idx + 1 }}</span>
                                        {{ $tp->name }}
                                    </td>
                                    <td class="py-3 px-3 text-center font-bold text-zinc-200 font-mono">
                                        {{ $tp->total_qty }} ชิ้น
                                    </td>
                                    <td class="py-3 px-3 text-right font-extrabold text-emerald-400 font-mono">
                                        ฿{{ number_format($tp->total_sales, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-zinc-500 text-xs">ยังไม่มีข้อมูลยอดขายอาหาร</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Revenue by Zone -->
            <div class="salai-card p-6 space-y-4">
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h3 class="text-base font-bold text-white">สถิติการใช้งานแยกตามโซน (Usage by Zone)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-300">
                        <thead class="bg-[#141824] text-xs uppercase text-zinc-400 border-b border-[#1e2430]">
                            <tr>
                                <th class="py-2.5 px-3">ชื่อโซน</th>
                                <th class="py-2.5 px-3 text-center">จำนวนเครื่อง</th>
                                <th class="py-2.5 px-3 text-center">รอบที่เล่นจบ</th>
                                <th class="py-2.5 px-3 text-right">ยอดเงินค่าบริการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1e2430]">
                            @foreach ($zoneStats as $zs)
                                <tr>
                                    <td class="py-3 px-3 font-semibold text-white">
                                        {{ $zs['zone']->name }}
                                    </td>
                                    <td class="py-3 px-3 text-center font-medium font-mono">
                                        {{ $zs['zone']->seats_count }} เครื่อง
                                    </td>
                                    <td class="py-3 px-3 text-center font-medium font-mono">
                                        {{ $zs['sessions_count'] }} ครั้ง
                                    </td>
                                    <td class="py-3 px-3 text-right font-extrabold text-red-400 font-mono">
                                        ฿{{ number_format($zs['revenue'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
