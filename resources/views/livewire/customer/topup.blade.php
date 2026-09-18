<div class="space-y-6">
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Section 1: Wallet Top-up -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-5">
            <div class="border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">เติมเงินเข้า Wallet (Top-up)</h2>
                <p class="text-xs text-zinc-500 mt-0.5">ยอดเงินคงเหลือปัจจุบัน: <span class="font-extrabold text-emerald-600 dark:text-emerald-400 text-sm">฿{{ number_format($user->balance, 2) }}</span></p>
            </div>

            <!-- Amount presets -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300 mb-2">เลือกจำนวนเงินที่ต้องการเติม:</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach ([50, 100, 200, 300, 500, 1000] as $preset)
                        <button
                            type="button"
                            wire:click="selectAmount({{ $preset }})"
                            class="py-2.5 rounded-xl border font-bold text-sm transition {{ $amount == $preset ? 'border-emerald-600 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200' }}"
                        >
                            ฿{{ $preset }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Custom amount input -->
            <div>
                <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300 mb-1">หรือระบุจำนวนเงินเอง:</label>
                <input
                    type="number"
                    wire:model="amount"
                    min="1"
                    step="1"
                    class="w-full text-sm rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 p-2.5 font-bold text-zinc-900 dark:text-white"
                />
            </div>

            <!-- Payment Simulation Method -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300">ช่องทางการชำระเงิน:</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer text-xs transition {{ $topupMethod === 'qr' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/40' : 'border-zinc-200 dark:border-zinc-800' }}">
                        <input type="radio" wire:model.live="topupMethod" value="qr" class="text-emerald-600">
                        <span class="font-bold text-zinc-800 dark:text-zinc-200">สแกน QR PromptPay</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer text-xs transition {{ $topupMethod === 'cash' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/40' : 'border-zinc-200 dark:border-zinc-800' }}">
                        <input type="radio" wire:model.live="topupMethod" value="cash" class="text-emerald-600">
                        <span class="font-bold text-zinc-800 dark:text-zinc-200">ชำระเงินสดที่เคาน์เตอร์</span>
                    </label>
                </div>
            </div>

            <button
                wire:click="doTopup"
                class="w-full py-3 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition shadow"
            >
                ยืนยันการเติมเงิน ฿{{ number_format($amount, 2) }}
            </button>
        </div>

        <!-- Section 2: Buy Time Packages -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-5">
            <div class="border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">ซื้อแพ็กเกจชั่วโมง (Time Packages)</h2>
                <p class="text-xs text-zinc-500 mt-0.5">ซื้อเวลาเหมาชั่วโมง คุ้มกว่าเล่นแบบคิดตามจริง (หักเงินจาก Wallet)</p>
            </div>

            <div class="space-y-3">
                @foreach ($packages as $pkg)
                    <div class="p-4 border border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center justify-between hover:border-indigo-400 transition bg-zinc-50/50 dark:bg-zinc-800/40">
                        <div>
                            <h4 class="font-bold text-sm text-zinc-900 dark:text-white">{{ $pkg->name }}</h4>
                            <p class="text-xs text-zinc-500 mt-0.5">
                                เล่นได้ {{ $pkg->duration_hours }} ชั่วโมง ({{ $pkg->duration_hours * 60 }} นาที)
                                @if ($pkg->zone)
                                    <span class="text-indigo-600 font-semibold">• เฉพาะโซน {{ $pkg->zone->name }}</span>
                                @else
                                    <span class="text-emerald-600 font-semibold">• ใช้ได้ทุกโซน</span>
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="text-base font-extrabold text-zinc-900 dark:text-white">
                                ฿{{ number_format($pkg->price, 2) }}
                            </span>

                            <button
                                wire:click="buyPackage({{ $pkg->id }})"
                                wire:confirm="คุณต้องการซื้อ '{{ $pkg->name }}' ในราคา ฿{{ number_format($pkg->price, 2) }} หรือไม่?"
                                @disabled($user->balance < $pkg->price)
                                class="px-3.5 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed rounded-xl transition shadow-sm"
                            >
                                ซื้อแพ็กเกจ
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- User Active Packages list -->
            @if ($myPackages->isNotEmpty())
                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <h4 class="text-xs font-bold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider mb-2">แพ็กเกจที่คุณมีอยู่ในขณะนี้:</h4>
                    <div class="space-y-2">
                        @foreach ($myPackages as $mp)
                            <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 rounded-lg flex justify-between items-center text-xs">
                                <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ $mp->package->name }}</span>
                                <span class="font-extrabold text-indigo-600 dark:text-indigo-400">{{ $mp->remaining_minutes }} นาทีคงเหลือ</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
