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

    <!-- Header & Status Legend -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
        <div>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">ผังที่นั่งคอมพิวเตอร์ (Seat Map)</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">เลือกเครื่องที่ว่างเพื่อเริ่มต้นใช้งาน (Check-in)</p>
        </div>

        <div class="flex items-center gap-4 text-xs font-semibold">
            <div class="flex items-center gap-1.5">
                <span class="w-3.5 h-3.5 rounded-md bg-emerald-500 border border-emerald-600"></span>
                <span class="text-zinc-700 dark:text-zinc-300">ว่าง (Available)</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3.5 h-3.5 rounded-md bg-red-500 border border-red-600"></span>
                <span class="text-zinc-700 dark:text-zinc-300">มีผู้ใช้งาน (Occupied)</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3.5 h-3.5 rounded-md bg-zinc-400 border border-zinc-500"></span>
                <span class="text-zinc-700 dark:text-zinc-300">ปรับปรุง (Maintenance)</span>
            </div>
        </div>
    </div>

    @if ($activeSession)
        <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></span>
                <p class="text-sm font-semibold text-amber-900 dark:text-amber-200">
                    ขณะนี้คุณกำลังเปิดใช้งานเครื่อง <span class="underline font-bold">{{ $activeSession->seat->seat_number }}</span> อยู่
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-xs font-bold text-amber-800 dark:text-amber-300 hover:underline" wire:navigate>
                กลับไปที่แดชบอร์ด &rarr;
            </a>
        </div>
    @endif

    <!-- Zone Map Sections -->
    @foreach ($zones as $zone)
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4 gap-2">
                <div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ $zone->name }}</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ $zone->description }}</p>
                </div>
                <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-zinc-800 px-3 py-1 rounded-lg self-start">
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
                        class="p-4 rounded-xl border flex flex-col items-center justify-between text-center transition
                            {{ $isCurrent ? 'bg-amber-100 border-amber-500 ring-2 ring-amber-500 dark:bg-amber-950/60' : '' }}
                            {{ $isSelected ? 'bg-indigo-50 border-indigo-600 ring-2 ring-indigo-600 dark:bg-indigo-950/60' : '' }}
                            @if (! $isCurrent && ! $isSelected)
                                @if($seat->status === 'available')
                                    bg-emerald-50/40 dark:bg-zinc-800/80 border-emerald-300 dark:border-emerald-800 hover:border-emerald-500 cursor-pointer hover:shadow-md
                                @elseif($seat->status === 'occupied')
                                    bg-red-50/40 dark:bg-zinc-800/40 border-red-200 dark:border-red-900 cursor-not-allowed opacity-75
                                @else
                                    bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 cursor-not-allowed opacity-50
                                @endif
                            @endif
                        "
                    >
                        <div class="w-8 h-8 rounded-lg mb-2 flex items-center justify-center font-bold text-sm
                            @if($seat->status === 'available') bg-emerald-500 text-white
                            @elseif($seat->status === 'occupied') bg-red-500 text-white
                            @else bg-zinc-400 text-white
                            @endif
                        ">
                            PC
                        </div>

                        <span class="font-extrabold text-base text-zinc-900 dark:text-white">
                            {{ $seat->seat_number }}
                        </span>

                        <span class="mt-1 text-[11px] font-semibold
                            @if($isCurrent) text-amber-600 dark:text-amber-400
                            @elseif($seat->status === 'available') text-emerald-600 dark:text-emerald-400
                            @elseif($seat->status === 'occupied') text-red-600 dark:text-red-400
                            @else text-zinc-400
                            @endif
                        ">
                            @if($isCurrent) เครื่องของคุณ
                            @elseif($seat->status === 'available') ว่าง
                            @elseif($seat->status === 'occupied') กำลังใช้งาน
                            @else ซ่อมบำรุง
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Check-in Confirmation Modal / Drawer -->
    @if ($selectedSeat)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-lg w-full p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white">ยืนยันการ Check-in</h3>
                    <button wire:click="cancelSelection" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">&times;</button>
                </div>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">หมายเลขเครื่อง:</span>
                        <span class="font-bold text-zinc-900 dark:text-white">{{ $selectedSeat->seat_number }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">โซนที่นั่ง:</span>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $selectedSeat->zone->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">อัตราค่าบริการปกติ:</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">฿{{ number_format($selectedSeat->zone->hourly_rate, 2) }} / ชม.</span>
                    </div>
                    <div class="flex justify-between text-sm pt-2 border-t border-zinc-200 dark:border-zinc-700">
                        <span class="text-zinc-500">ยอดเงินคงเหลือของคุณ:</span>
                        <span class="font-bold text-zinc-900 dark:text-white">฿{{ number_format($user->balance, 2) }}</span>
                    </div>
                </div>

                <!-- Choose Billing Mode -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">เลือกรูปแบบการคิดเวลา:</label>

                    <label class="flex items-start gap-3 p-3 border rounded-xl cursor-pointer transition {{ $billingMode === 'pay_as_you_go' ? 'border-indigo-600 bg-indigo-50/40 dark:bg-indigo-950/40' : 'border-zinc-200 dark:border-zinc-800' }}">
                        <input type="radio" wire:model.live="billingMode" value="pay_as_you_go" class="mt-1 text-indigo-600">
                        <div>
                            <span class="font-bold text-sm text-zinc-900 dark:text-white">Pay-as-you-go (คิดตามจริงจาก Wallet)</span>
                            <p class="text-xs text-zinc-500 mt-0.5">หักเงินจากยอดคงเหลือในกระเป๋าตามนาทีที่เล่นจริงเมื่อเช็คเอาท์</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 border rounded-xl cursor-pointer transition {{ $billingMode === 'package' ? 'border-indigo-600 bg-indigo-50/40 dark:bg-indigo-950/40' : 'border-zinc-200 dark:border-zinc-800' }}">
                        <input type="radio" wire:model.live="billingMode" value="package" class="mt-1 text-indigo-600">
                        <div class="flex-1">
                            <span class="font-bold text-sm text-zinc-900 dark:text-white">ใช้แพ็กเกจชั่วโมงสะสม</span>
                            <p class="text-xs text-zinc-500 mt-0.5">หักเวลาจากแพ็กเกจชั่วโมงที่คุณซื้อไว้</p>

                            @if ($billingMode === 'package')
                                <div class="mt-3">
                                    @if ($availablePackages->isEmpty())
                                        <p class="text-xs text-red-500">คุณยังไม่มีแพ็กเกจชั่วโมงสะสม กรุณาเลือก Pay-as-you-go หรือไปซื้อแพ็กเกจก่อน</p>
                                    @else
                                        <select wire:model="selectedUserPackageId" class="w-full text-sm rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 p-2">
                                            <option value="">-- กรุณาเลือกแพ็กเกจ --</option>
                                            @foreach ($availablePackages as $upkg)
                                                <option value="{{ $upkg->id }}">
                                                    {{ $upkg->package->name }} (เหลือ {{ $upkg->remaining_minutes }} นาที)
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selectedUserPackageId')
                                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>
                            @endif
                        </div>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <button wire:click="cancelSelection" class="px-4 py-2 text-sm text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition">
                        ยกเลิก
                    </button>
                    <button wire:click="checkIn" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow transition">
                        ยืนยันเปิดเครื่อง
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
