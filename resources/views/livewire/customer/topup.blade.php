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
        <!-- Section 1: Wallet Top-up (Salai Termgames Style) -->
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
                    <span class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-yellow-500 font-mono">
                        ฿{{ number_format($user->balance, 2) }}
                    </span>
                </div>
            </div>

            <!-- Amount presets (Salai Grid) -->
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
                            wire:click="selectAmount({{ $p['amt'] }})"
                            class="relative p-3 rounded-2xl border text-center transition-all duration-200 {{ $amount == $p['amt'] ? 'border-red-500 bg-red-950/40 ring-2 ring-red-500 shadow-[0_0_15px_rgba(220,38,38,0.3)]' : 'border-[#232938] bg-[#141824] hover:border-red-500/50' }}"
                        >
                            <span class="absolute -top-2 right-2 text-[9px] font-black px-1.5 py-0.2 rounded-full {{ $amount == $p['amt'] ? 'bg-red-600 text-white' : 'bg-[#232938] text-amber-400' }}">
                                {{ $p['badge'] }}
                            </span>
                            <div class="text-lg font-black text-white font-mono mt-1">
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
                    wire:model="amount"
                    min="1"
                    step="1"
                    placeholder="ระบุจำนวนเงิน..."
                    class="w-full text-sm rounded-xl border-[#232938] bg-[#141824] p-2.5 font-black text-white font-mono focus:border-red-500 focus:ring-red-500"
                />
            </div>

            <!-- Payment Simulation Method -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-zinc-300">ช่องทางการชำระเงิน:</label>
                <div class="grid grid-cols-2 gap-2.5">
                    <label class="flex items-center gap-2.5 p-3 border rounded-xl cursor-pointer text-xs transition {{ $topupMethod === 'qr' ? 'border-red-500 bg-red-950/30' : 'border-[#232938] bg-[#141824]' }}">
                        <input type="radio" wire:model.live="topupMethod" value="qr" class="text-red-600 focus:ring-red-500">
                        <div>
                            <span class="font-bold text-white block">สแกน QR PromptPay</span>
                            <span class="text-[10px] text-zinc-400">เข้าทันที (จำลอง)</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 border rounded-xl cursor-pointer text-xs transition {{ $topupMethod === 'cash' ? 'border-red-500 bg-red-950/30' : 'border-[#232938] bg-[#141824]' }}">
                        <input type="radio" wire:model.live="topupMethod" value="cash" class="text-red-600 focus:ring-red-500">
                        <div>
                            <span class="font-bold text-white block">เงินสดที่เคาน์เตอร์</span>
                            <span class="text-[10px] text-zinc-400">ชำระกับพนักงานร้าน</span>
                        </div>
                    </label>
                </div>
            </div>

            <button
                wire:click="doTopup"
                class="salai-btn-primary w-full py-3 text-xs font-black"
            >
                ⚡ ยืนยันการเติมเงิน ฿{{ number_format($amount, 2) }}
            </button>
        </div>

        <!-- Section 2: Buy Time Packages (Salai Termgames Style) -->
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

                            <button
                                wire:click="buyPackage({{ $pkg->id }})"
                                wire:confirm="คุณต้องการซื้อ '{{ $pkg->name }}' ในราคา ฿{{ number_format($pkg->price, 2) }} หรือไม่?"
                                @disabled($user->balance < $pkg->price)
                                class="salai-btn-primary text-xs py-1.5 px-3.5 disabled:opacity-40 disabled:cursor-not-allowed"
                            >
                                ซื้อเลย
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- User Active Packages list -->
            @if ($myPackages->isNotEmpty())
                <div class="pt-4 border-t border-[#1e2430] space-y-2">
                    <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">
                        แพ็กเกจที่คุณมีอยู่ในขณะนี้:
                    </h4>
                    <div class="space-y-2">
                        @foreach ($myPackages as $mp)
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
</div>
