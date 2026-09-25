<x-layouts::app :title="__('Wallet & Packages')">
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Section 1: Wallet Top-up -->
            <div class="salai-card-glow p-6 rounded-3xl space-y-5">
                <div class="border-b border-[#232938] pb-3 flex items-center justify-between">
                    <div class="salai-step-header">
                        <span class="salai-step-bar"></span>
                        <div>
                            <h2 class="text-xl font-black text-white font-sans">เติมเงินเข้า Wallet (Top-up)</h2>
                            <p class="text-xs text-zinc-400">ระบบเติมเงินอัตโนมัติ เครดิตเข้าทันที</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-zinc-400 block font-bold">คงเหลือในกระเป๋า</span>
                        <span class="text-xl font-bold text-amber-400 font-mono">
                            ฿{{ number_format($user->balance, 2) }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('customer.do-topup') }}" class="space-y-4">
                    @csrf

                    <!-- Amount presets -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider">
                            เลือกจำนวนเงินที่ต้องการเติม:
                        </label>
                        <div class="grid grid-cols-3 gap-2.5">
                            @php
                                $presets = [
                                    ['amt' => 50, 'badge' => 'เริ่มต้น'],
                                    ['amt' => 100, 'badge' => 'HOT'],
                                    ['amt' => 200, 'badge' => 'ยอดนิยม'],
                                    ['amt' => 300, 'badge' => 'สุดคุ้ม'],
                                    ['amt' => 500, 'badge' => '+โบนัส 5%'],
                                    ['amt' => 1000, 'badge' => '+โบนัส 10%'],
                                ];
                            @endphp
                            @foreach ($presets as $p)
                                <button
                                    type="button"
                                    onclick="setAmount({{ $p['amt'] }})"
                                    class="preset-btn relative p-3 rounded-xl border text-center transition-colors border-[#232938] bg-[#141824] hover:border-red-500/50"
                                    id="preset-{{ $p['amt'] }}"
                                >
                                    <span class="absolute -top-2 right-2 text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#232938] text-amber-400">
                                        {{ $p['badge'] }}
                                    </span>
                                    <div class="text-lg font-bold text-white font-mono mt-1">
                                        ฿{{ $p['amt'] }}
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Custom amount input -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-zinc-300">หรือระบุจำนวนเงินเอง (บาท):</label>
                        <input
                            type="number"
                            name="amount"
                            id="inputAmount"
                            min="1"
                            step="1"
                            value="100"
                            required
                            placeholder="ระบุจำนวนเงิน..."
                            class="w-full text-sm rounded-xl border-[#232938] bg-[#141824] p-2.5 font-black text-white font-mono focus:border-red-500 focus:ring-red-500"
                        />
                    </div>

                    <!-- Payment Method -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-zinc-300">ช่องทางการชำระเงิน:</label>
                        <div class="grid grid-cols-2 gap-2.5">
                            <label class="flex items-center gap-2.5 p-3 border rounded-xl cursor-pointer text-xs border-red-500 bg-red-950/30">
                                <input type="radio" name="topup_method" value="qr" checked class="text-red-600 focus:ring-red-500">
                                <div>
                                    <span class="font-bold text-white block">สแกน QR PromptPay</span>
                                    <span class="text-[10px] text-zinc-400">เข้าทันที (จำลอง)</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 border rounded-xl cursor-pointer text-xs border-[#232938] bg-[#141824]">
                                <input type="radio" name="topup_method" value="cash" class="text-red-600 focus:ring-red-500">
                                <div>
                                    <span class="font-bold text-white block">เงินสดที่เคาน์เตอร์</span>
                                    <span class="text-[10px] text-zinc-400">ชำระกับพนักงานร้าน</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="salai-btn-primary w-full py-3 text-xs font-black"
                    >
                        ⚡ ยืนยันการเติมเงิน
                    </button>
                </form>
            </div>

            <!-- Section 2: Buy Time Packages -->
            <div class="salai-card p-6 space-y-5">
                <div class="border-b border-[#1e2430] pb-3">
                    <div class="salai-step-header">
                        <span class="salai-step-bar"></span>
                        <div>
                            <h2 class="text-xl font-black text-white font-sans">ซื้อแพ็กเกจชั่วโมง (Time Packages)</h2>
                            <p class="text-xs text-zinc-400">ซื้อเวลาเหมาชั่วโมง คุ้มกว่าเล่นแบบคิดตามจริง (หักจาก Wallet)</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach ($packages as $pkg)
                        <div class="p-4 border border-[#1e2430] rounded-2xl flex items-center justify-between hover:border-red-500/50 transition bg-[#141824] group">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-black text-sm text-white group-hover:text-red-400 transition-colors">{{ $pkg->name }}</h4>
                                    <span class="salai-badge-red text-[10px]">
                                        {{ $pkg->duration_hours }} ชม.
                                    </span>
                                </div>
                                <p class="text-[11px] text-zinc-400">
                                    เล่นได้ {{ $pkg->duration_hours * 60 }} นาที
                                    @if ($pkg->zone)
                                        <span class="text-red-400 font-semibold">• โซน {{ $pkg->zone->name }}</span>
                                    @else
                                        <span class="text-emerald-400 font-semibold">• ใช้ได้ทุกโซน</span>
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-lg font-black text-white font-mono">
                                    ฿{{ number_format($pkg->price, 2) }}
                                </span>

                                <form method="POST" action="{{ route('customer.buy-package') }}" onsubmit="return confirm('คุณต้องการซื้อ \'{{ addslashes($pkg->name) }}\' ในราคา ฿{{ number_format($pkg->price, 2) }} หรือไม่?');">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                    <button
                                        type="submit"
                                        @disabled($user->balance < $pkg->price)
                                        class="salai-btn-primary text-xs py-1.5 px-3.5 disabled:opacity-40 disabled:cursor-not-allowed"
                                    >
                                        ซื้อเลย
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- User Active Packages list -->
                @if ($userPackages->isNotEmpty())
                    <div class="pt-4 border-t border-[#1e2430] space-y-2">
                        <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">
                            แพ็กเกจที่คุณมีอยู่ในขณะนี้:
                        </h4>
                        <div class="space-y-2">
                            @foreach ($userPackages as $mp)
                                <div class="p-3 bg-[#141824] border border-[#232938] rounded-xl flex justify-between items-center text-xs">
                                    <span class="font-bold text-white">{{ $mp->package->name }}</span>
                                    <span class="font-black text-red-400 bg-red-500/10 px-2 py-0.5 rounded-lg border border-red-500/20 font-mono">
                                        {{ $mp->remaining_minutes }} นาทีคงเหลือ
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Section 3: Wallet Transactions History -->
        @if ($transactions->isNotEmpty())
            <div class="salai-card p-6 space-y-4">
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h3 class="text-base font-black text-white tracking-wide font-sans">
                        ประวัติการทำรายการล่าสุด (10 รายการ)
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
                            @foreach ($transactions as $tx)
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
                                        {{ $tx->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script>
        function setAmount(val) {
            document.getElementById('inputAmount').value = val;
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.classList.remove('border-red-500', 'bg-red-950/30', 'ring-1', 'ring-red-500');
            });
            const selected = document.getElementById('preset-' + val);
            if (selected) {
                selected.classList.add('border-red-500', 'bg-red-950/30', 'ring-1', 'ring-red-500');
            }
        }
    </script>
</x-layouts::app>
