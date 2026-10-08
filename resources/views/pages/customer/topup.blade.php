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
            <div class="card-glow p-6 rounded-3xl space-y-5">
                <div class="border-b border-[#232938] pb-3 flex items-center justify-between">
                    <div class="step-header">
                        <span class="step-bar"></span>
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

                <form id="topupForm" method="POST" action="{{ route('customer.do-topup') }}" class="space-y-4">
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
                            <label id="methodLabelQr" class="flex items-center gap-2.5 p-3 border rounded-xl cursor-pointer text-xs border-red-500 bg-red-950/30 transition">
                                <input type="radio" name="topup_method" value="qr" checked onchange="handleMethodChange(this.value)" class="text-red-600 focus:ring-red-500">
                                <div>
                                    <span class="font-bold text-white block">สแกน QR PromptPay</span>
                                    <span class="text-[10px] text-zinc-400">เข้าทันที (จำลอง)</span>
                                </div>
                            </label>

                            <label id="methodLabelCash" class="flex items-center gap-2.5 p-3 border rounded-xl cursor-pointer text-xs border-[#232938] bg-[#141824] transition">
                                <input type="radio" name="topup_method" value="cash" onchange="handleMethodChange(this.value)" class="text-red-600 focus:ring-red-500">
                                <div>
                                    <span class="font-bold text-white block">เงินสดที่เคาน์เตอร์</span>
                                    <span class="text-[10px] text-zinc-400">ชำระกับพนักงานร้าน</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button
                        type="button"
                        id="submitTopupBtn"
                        onclick="handleTopupSubmit()"
                        class="btn-primary w-full py-3 text-xs font-black cursor-pointer"
                    >
                        ⚡ ยืนยันการเติมเงิน
                    </button>
                </form>
            </div>

            <!-- Section 2: Buy Time Packages -->
            <div class="card p-6 space-y-5">
                <div class="border-b border-[#1e2430] pb-3">
                    <div class="step-header">
                        <span class="step-bar"></span>
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
                                    <span class="badge-red text-[10px]">
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
                                        class="btn-primary text-xs py-1.5 px-3.5 disabled:opacity-40 disabled:cursor-not-allowed"
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
            <div class="card p-6 space-y-4">
                <div class="step-header">
                    <span class="step-bar"></span>
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
                                            <span class="badge-red text-[10px]">เติมเงิน ({{ $tx->ref_type }})</span>
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

        <!-- Modal จำลองการสแกน QR Code PromptPay -->
        <div id="qrModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="p-6 max-w-sm w-full border border-red-500/40 bg-[#101420] rounded-2xl shadow-2xl space-y-4 text-center">
                <div class="flex items-center justify-between pb-3 border-b border-[#232938]">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                        <span class="text-sm font-bold text-white">PromptPay QR Code</span>
                    </div>
                    <button type="button" onclick="closeQrModal()" class="text-zinc-400 hover:text-white text-lg font-bold leading-none cursor-pointer">&times;</button>
                </div>

                <div class="space-y-1">
                    <p class="text-xs text-zinc-400">สแกนเพื่อชำระเงิน</p>
                    <div class="text-2xl font-black text-amber-400 font-mono" id="modalQrAmount">฿100.00</div>
                </div>

                <!-- จำลอง QR Code Card PromptPay (สไตล์ทางการ) -->
                <div style="background-color: #ffffff; border-radius: 1rem; width: 15rem; margin: 0 auto; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);">
                    <!-- PromptPay Header Bar -->
                    <div style="background-color: #003d6b; padding: 0.625rem 0.5rem; text-align: center;">
                        <div style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; background-color: #ffffff; border-radius: 0.375rem; padding: 0.25rem 0.75rem;">
                            <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #dc2626; display: inline-block;"></span>
                            <span style="color: #003d6b; font-size: 11px; font-weight: 900; letter-spacing: 0.05em; font-family: sans-serif;">THAI QR PAYMENT</span>
                        </div>
                    </div>

                    <!-- QR Body -->
                    <div style="padding: 1rem; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #ffffff;">
                        <svg class="w-44 h-44 text-black" viewBox="0 0 100 100" fill="currentColor">
                        <rect x="0" y="0" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6"/>
                        <rect x="8" y="8" width="14" height="14"/>
                        <rect x="70" y="0" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6"/>
                        <rect x="78" y="8" width="14" height="14"/>
                        <rect x="0" y="70" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6"/>
                        <rect x="8" y="78" width="14" height="14"/>
                        <rect x="36" y="8" width="8" height="8"/>
                        <rect x="48" y="16" width="12" height="6"/>
                        <rect x="36" y="26" width="6" height="10"/>
                        <rect x="46" y="38" width="8" height="8"/>
                        <rect x="10" y="44" width="10" height="6"/>
                        <rect x="24" y="40" width="8" height="16"/>
                        <rect x="38" y="60" width="12" height="12"/>
                        <rect x="60" y="40" width="10" height="8"/>
                        <rect x="74" y="36" width="16" height="6"/>
                        <rect x="84" y="48" width="10" height="14"/>
                        <rect x="60" y="60" width="16" height="8"/>
                        <rect x="80" y="74" width="14" height="14"/>
                        <rect x="64" y="84" width="10" height="10"/>
                        <rect x="40" y="82" width="14" height="6"/>
                    </svg>
                    </div>
                </div>
                <br>
                <div class="text-[11px] text-zinc-400 bg-[#141824] p-2.5 rounded-lg border border-[#232938]">
                    ชื่อบัญชี: <span class="text-zinc-200 font-bold">Letsplay cafe</span><br>
                    เลขอ้างอิง: <span class="text-zinc-400 font-mono">089-XXX-XXXX</span>
                </div>

                <div class="flex gap-2.5 pt-2">
                    <button
                        type="button"
                        onclick="closeQrModal()"
                        class="w-1/3 py-2.5 px-3 rounded-xl border border-[#232938] bg-[#141824] text-xs font-bold text-zinc-300 hover:bg-[#1e2430] hover:text-white transition cursor-pointer text-center"
                    >
                        ยกเลิก
                    </button>
                    <button
                        type="button"
                        id="confirmPaymentBtn"
                        onclick="submitTopupForm()"
                        class="w-2/3 py-2.5 px-3 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition shadow-lg shadow-red-950/40 cursor-pointer text-center"
                    >
                        ✓ สแกนจ่ายเสร็จแล้ว
                    </button>
                </div>
            </div>
        </div>
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

        function handleMethodChange(method) {
            const qrLabel = document.getElementById('methodLabelQr');
            const cashLabel = document.getElementById('methodLabelCash');

            if (method === 'qr') {
                qrLabel.classList.add('border-red-500', 'bg-red-950/30');
                qrLabel.classList.remove('border-[#232938]', 'bg-[#141824]');
                cashLabel.classList.remove('border-red-500', 'bg-red-950/30');
                cashLabel.classList.add('border-[#232938]', 'bg-[#141824]');
            } else {
                cashLabel.classList.add('border-red-500', 'bg-red-950/30');
                cashLabel.classList.remove('border-[#232938]', 'bg-[#141824]');
                qrLabel.classList.remove('border-red-500', 'bg-red-950/30');
                qrLabel.classList.add('border-[#232938]', 'bg-[#141824]');
            }
        }

        function handleTopupSubmit() {
            const amountInput = document.getElementById('inputAmount');
            const amount = parseFloat(amountInput.value);

            if (!amount || amount <= 0) {
                alert('กรุณาระบุจำนวนเงินที่ถูกต้อง');
                amountInput.focus();
                return;
            }

            const selectedMethod = document.querySelector('input[name="topup_method"]:checked')?.value || 'qr';

            if (selectedMethod === 'qr') {
                document.getElementById('modalQrAmount').textContent = '฿' + amount.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const modal = document.getElementById('qrModal');
                modal.classList.remove('hidden');
            } else {
                submitTopupForm();
            }
        }

        function closeQrModal() {
            const modal = document.getElementById('qrModal');
            modal.classList.add('hidden');
        }

        function submitTopupForm() {
            const confirmBtn = document.getElementById('confirmPaymentBtn');
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.innerText = 'กำลังดำเนินการ...';
            }
            document.getElementById('topupForm').submit();
        }
    </script>
</x-layouts::app>
