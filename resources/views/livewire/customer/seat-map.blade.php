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

    <!-- Header & Status Legend (Salai Style) -->
    <div class="salai-card p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="space-y-1">
            <div class="salai-step-header">
                <span class="salai-step-bar"></span>
                <h2 class="text-2xl font-black text-white tracking-wide font-sans">
                    ผังที่นั่งคอมพิวเตอร์ (Seat Map)
                </h2>
            </div>
            <p class="text-xs text-zinc-400 pl-4">เลือกเครื่องคอมพิวเตอร์ที่ว่างเพื่อเริ่มต้นใช้งาน (Check-in)</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
            <div class="flex items-center gap-1.5 bg-[#141824] px-3 py-1.5 rounded-full border border-emerald-500/30">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                <span class="text-emerald-400 font-bold">ว่าง (Available)</span>
            </div>
            <div class="flex items-center gap-1.5 bg-[#141824] px-3 py-1.5 rounded-full border border-red-500/30">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)]"></span>
                <span class="text-red-400 font-bold">มีผู้ใช้งาน (Occupied)</span>
            </div>
            <div class="flex items-center gap-1.5 bg-[#141824] px-3 py-1.5 rounded-full border border-zinc-700">
                <span class="w-2.5 h-2.5 rounded-full bg-zinc-500"></span>
                <span class="text-zinc-400 font-bold">ปรับปรุง (Maintenance)</span>
            </div>
        </div>
    </div>

    @if ($activeSession)
        <div class="p-4 rounded-2xl bg-amber-950/40 border border-amber-500/40 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-amber-400 animate-ping"></span>
                <p class="text-sm font-semibold text-amber-200">
                    ขณะนี้คุณกำลังเปิดใช้งานเครื่อง <span class="underline font-black text-amber-300 text-base">{{ $activeSession->seat->seat_number }}</span> อยู่
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="salai-btn-primary text-xs py-1.5 px-4" wire:navigate>
                ดูแดชบอร์ดเครื่อง &rarr;
            </a>
        </div>
    @endif

    <!-- Zone Map Sections -->
    @foreach ($zones as $zone)
        <div class="salai-card p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-[#1e2430] pb-3 gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="salai-step-bar"></span>
                    <div>
                        <h3 class="text-lg font-black text-white font-sans">{{ $zone->name }}</h3>
                        <p class="text-xs text-zinc-400">{{ $zone->description }}</p>
                    </div>
                </div>
                <div class="salai-badge-red text-xs self-start sm:self-auto">
                    ฿{{ number_format($zone->hourly_rate, 2) }} / ชม.
                </div>
            </div>

            <!-- Seat Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($zone->seats as $seat)
                    @php
                        $isCurrent = $activeSession && $activeSession->seat_id === $seat->id;
                        $isSelected = $selectedSeatId === $seat->id;
                    @endphp
                    <div
                        @if ($seat->isAvailable() && ! $activeSession)
                            wire:click="selectSeat({{ $seat->id }})"
                            role="button"
                        @endif
                        class="p-4 rounded-2xl border flex flex-col items-center justify-between text-center transition-all duration-200
                            {{ $isCurrent ? 'bg-amber-950/60 border-amber-500 ring-2 ring-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.3)]' : '' }}
                            {{ $isSelected ? 'bg-red-950/60 border-red-500 ring-2 ring-red-500 shadow-[0_0_15px_rgba(220,38,38,0.4)]' : '' }}
                            @if (! $isCurrent && ! $isSelected)
                                @if($seat->status === 'available')
                                    bg-[#141824] border-[#232938] hover:border-red-500/70 hover:shadow-[0_0_15px_rgba(220,38,38,0.2)] cursor-pointer hover:-translate-y-1
                                @elseif($seat->status === 'occupied')
                                    bg-[#0e1017] border-red-950/60 cursor-not-allowed opacity-60
                                @else
                                    bg-[#0e1017] border-zinc-800 cursor-not-allowed opacity-40
                                @endif
                            @endif
                        "
                    >
                        <div class="w-10 h-10 rounded-xl mb-2 flex items-center justify-center font-black text-sm
                            @if($isCurrent) bg-amber-500 text-black shadow-[0_0_10px_rgba(245,158,11,0.6)]
                            @elseif($seat->status === 'available') bg-emerald-600/30 text-emerald-400 border border-emerald-500/40
                            @elseif($seat->status === 'occupied') bg-red-600/30 text-red-400 border border-red-500/40
                            @else bg-zinc-800 text-zinc-500
                            @endif
                        ">
                            🖥️
                        </div>

                        <span class="font-black text-lg text-white font-sans">
                            {{ $seat->seat_number }}
                        </span>

                        <span class="mt-1 text-[11px] font-bold
                            @if($isCurrent) text-amber-400
                            @elseif($seat->status === 'available') text-emerald-400
                            @elseif($seat->status === 'occupied') text-red-400
                            @else text-zinc-500
                            @endif
                        ">
                            @if($isCurrent) เครื่องของคุณ
                            @elseif($seat->status === 'available') กดเพื่อจอง
                            @elseif($seat->status === 'occupied') ไม่ว่าง
                            @else ปรับปรุง
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Check-in Confirmation Modal / Drawer (Salai Glow) -->
    @if ($selectedSeat)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="salai-card-glow max-w-lg w-full p-6 space-y-5 shadow-2xl rounded-3xl">
                <div class="flex items-center justify-between border-b border-[#232938] pb-3">
                    <div class="salai-step-header">
                        <span class="salai-step-bar"></span>
                        <h3 class="text-xl font-black text-white font-sans">ยืนยันการเปิดเครื่อง (Check-in)</h3>
                    </div>
                    <button wire:click="cancelSelection" class="text-zinc-400 hover:text-white text-xl font-bold">&times;</button>
                </div>

                <div class="p-4 bg-[#141824] border border-[#232938] rounded-2xl space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-zinc-400">หมายเลขเครื่อง:</span>
                        <span class="font-black text-white text-sm">{{ $selectedSeat->seat_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">โซนที่นั่ง:</span>
                        <span class="font-bold text-red-400">{{ $selectedSeat->zone->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">อัตราค่าบริการปกติ:</span>
                        <span class="font-bold text-emerald-400 font-mono">฿{{ number_format($selectedSeat->zone->hourly_rate, 2) }} / ชม.</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-[#1e2430]">
                        <span class="text-zinc-400">ยอดเงินคงเหลือของคุณ:</span>
                        <span class="font-black text-amber-400 text-sm font-mono">฿{{ number_format($user->balance, 2) }}</span>
                    </div>
                </div>

                <!-- Choose Billing Mode -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider">เลือกรูปแบบการคิดค่าบริการ:</label>

                    <label class="flex items-start gap-3 p-3.5 border rounded-2xl cursor-pointer transition {{ $billingMode === 'pay_as_you_go' ? 'border-red-500 bg-red-950/30' : 'border-[#232938] bg-[#141824]' }}">
                        <input type="radio" wire:model.live="billingMode" value="pay_as_you_go" class="mt-1 text-red-600 focus:ring-red-500">
                        <div>
                            <span class="font-black text-sm text-white">Pay-as-you-go (ตัดเงินตามจริงจาก Wallet)</span>
                            <p class="text-xs text-zinc-400 mt-0.5">ระบบจะคำนวณตามนาทีที่เล่นจริงและหักจากยอด Wallet เมื่อกดออกจากเครื่อง</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 border rounded-2xl cursor-pointer transition {{ $billingMode === 'package' ? 'border-red-500 bg-red-950/30' : 'border-[#232938] bg-[#141824]' }}">
                        <input type="radio" wire:model.live="billingMode" value="package" class="mt-1 text-red-600 focus:ring-red-500">
                        <div class="flex-1">
                            <span class="font-black text-sm text-white">ใช้แพ็กเกจชั่วโมงสะสม</span>
                            <p class="text-xs text-zinc-400 mt-0.5">หักเวลาจากแพ็กเกจชั่วโมงที่คุณซื้อไว้ล่วงหน้า</p>

                            @if ($billingMode === 'package')
                                <div class="mt-3">
                                    @if ($availablePackages->isEmpty())
                                        <p class="text-xs text-red-400 bg-red-950/60 p-2.5 rounded-xl border border-red-500/30">
                                            คุณยังไม่มีแพ็กเกจชั่วโมงสะสม กรุณาเลือก Pay-as-you-go หรือไปซื้อแพ็กเกจก่อน
                                        </p>
                                    @else
                                        <select wire:model="selectedUserPackageId" class="w-full text-xs rounded-xl border-[#232938] bg-[#0e1017] p-2.5 text-zinc-200 focus:border-red-500 focus:ring-red-500">
                                            <option value="">-- กรุณาเลือกแพ็กเกจ --</option>
                                            @foreach ($availablePackages as $upkg)
                                                <option value="{{ $upkg->id }}">
                                                    {{ $upkg->package->name }} (เหลือ {{ $upkg->remaining_minutes }} นาที)
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selectedUserPackageId')
                                            <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>
                            @endif
                        </div>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <button wire:click="cancelSelection" class="salai-card text-xs font-semibold px-4 py-2 text-zinc-400 hover:text-white border-[#262d3d]">
                        ยกเลิก
                    </button>
                    <button wire:click="checkIn" class="salai-btn-primary text-xs py-2 px-6">
                        🚀 ยืนยันเปิดเครื่อง
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
