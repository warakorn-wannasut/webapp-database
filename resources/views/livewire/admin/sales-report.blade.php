<div class="space-y-6">
    <!-- Header & Period Filter -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
        <div>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">รายงานสรุปยอดขาย (Sales & Analytics)</h2>
            <p class="text-xs text-zinc-500 mt-1">สรุปรายได้แยกตามค่าชั่วโมงเล่นเกมและค่าอาหาร/เครื่องดื่ม (Aggregation)</p>
        </div>

        <div class="flex gap-2 text-xs font-semibold">
            <button
                wire:click="$set('period', 'today')"
                class="px-3.5 py-1.5 rounded-xl transition {{ $period === 'today' ? 'bg-indigo-600 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}"
            >
                วันนี้
            </button>
            <button
                wire:click="$set('period', '7days')"
                class="px-3.5 py-1.5 rounded-xl transition {{ $period === '7days' ? 'bg-indigo-600 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}"
            >
                7 วันล่าสุด
            </button>
            <button
                wire:click="$set('period', 'month')"
                class="px-3.5 py-1.5 rounded-xl transition {{ $period === 'month' ? 'bg-indigo-600 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}"
            >
                30 วันล่าสุด
            </button>
            <button
                wire:click="$set('period', 'all')"
                class="px-3.5 py-1.5 rounded-xl transition {{ $period === 'all' ? 'bg-indigo-600 text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}"
            >
                ทั้งหมด
            </button>
        </div>
    </div>

    <!-- Revenue Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Grand Total -->
        <div class="p-6 bg-gradient-to-br from-indigo-500 to-indigo-700 text-white rounded-2xl shadow-lg">
            <p class="text-xs font-semibold text-indigo-100 uppercase tracking-wider">รายได้รวมทั้งหมด (Total Revenue)</p>
            <h3 class="text-3xl font-extrabold mt-2">฿{{ number_format($grandTotalRevenue, 2) }}</h3>
            <p class="text-[11px] text-indigo-200 mt-2">ค่าชั่วโมง + แพ็กเกจ + อาหาร</p>
        </div>

        <!-- Gaming Revenue -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
            <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">รายได้ค่าชั่วโมงเล่นเกม</p>
            <h3 class="text-2xl font-black text-zinc-900 dark:text-white mt-2">฿{{ number_format($totalGamingRevenue, 2) }}</h3>
            <div class="mt-2 text-xs text-zinc-400 space-y-0.5">
                <p>• รายชั่วโมง: ฿{{ number_format($hourlyRevenue, 2) }} ({{ $completedSessionsCount }} เซสชัน)</p>
                <p>• ขายแพ็กเกจ: ฿{{ number_format($packageRevenue, 2) }} ({{ $packageSalesCount }} บิล)</p>
            </div>
        </div>

        <!-- Food Revenue -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
            <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">รายได้อาหารและเครื่องดื่ม</p>
            <h3 class="text-2xl font-black text-zinc-900 dark:text-white mt-2">฿{{ number_format($foodRevenue, 2) }}</h3>
            <p class="text-xs text-zinc-400 mt-2">จำนวนออเดอร์ที่ชำระแล้ว: {{ $paidOrdersCount }} บิล</p>
        </div>

        <!-- Topup Total -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
            <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">ยอดเงินที่เติมเข้า Wallet</p>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">฿{{ number_format($totalTopup, 2) }}</h3>
            <p class="text-xs text-zinc-400 mt-2">กระแสเงินสดรับเข้าระบบ</p>
        </div>
    </div>

    <!-- Data Tables: Top Products and Zones -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 Products -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-4">
            <h3 class="text-base font-bold text-zinc-900 dark:text-white">5 อันดับเมนูขายดี (Top 5 Best Selling)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs uppercase text-zinc-500">
                        <tr>
                            <th class="py-2.5 px-3">อันดับ / เมนู</th>
                            <th class="py-2.5 px-3 text-center">จำนวนที่ขายได้</th>
                            <th class="py-2.5 px-3 text-right">ยอดขายรวม</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($topProducts as $idx => $tp)
                            <tr>
                                <td class="py-3 px-3 font-semibold text-zinc-900 dark:text-white">
                                    <span class="inline-block w-5 text-indigo-600 font-extrabold">#{{ $idx + 1 }}</span>
                                    {{ $tp->name }}
                                </td>
                                <td class="py-3 px-3 text-center font-bold text-zinc-800 dark:text-zinc-200">
                                    {{ $tp->total_qty }} ชิ้น
                                </td>
                                <td class="py-3 px-3 text-right font-extrabold text-emerald-600 dark:text-emerald-400">
                                    ฿{{ number_format($tp->total_sales, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-zinc-400 text-xs">ยังไม่มีข้อมูลยอดขายอาหาร</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Revenue by Zone -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-4">
            <h3 class="text-base font-bold text-zinc-900 dark:text-white">สถิติการใช้งานแยกตามโซน (Usage by Zone)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                    <thead class="bg-zinc-50 dark:bg-zinc-800 text-xs uppercase text-zinc-500">
                        <tr>
                            <th class="py-2.5 px-3">ชื่อโซน</th>
                            <th class="py-2.5 px-3 text-center">จำนวนเครื่อง</th>
                            <th class="py-2.5 px-3 text-center">รอบที่เล่นจบ</th>
                            <th class="py-2.5 px-3 text-right">ยอดเงินค่าบริการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach ($zoneStats as $zs)
                            <tr>
                                <td class="py-3 px-3 font-semibold text-zinc-900 dark:text-white">
                                    {{ $zs['zone']->name }}
                                </td>
                                <td class="py-3 px-3 text-center font-medium">
                                    {{ $zs['zone']->seats_count }} เครื่อง
                                </td>
                                <td class="py-3 px-3 text-center font-medium">
                                    {{ $zs['sessions_count'] }} ครั้ง
                                </td>
                                <td class="py-3 px-3 text-right font-extrabold text-indigo-600 dark:text-indigo-400">
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
