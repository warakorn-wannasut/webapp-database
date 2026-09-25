<x-layouts::app :title="__('Seat Map')">
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

        <!-- Header & Status Legend -->
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
                <div class="flex items-center gap-1.5 bg-[#141824] px-2.5 py-1 rounded-md border border-[#232938]">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-emerald-400">ว่าง (Available)</span>
                </div>
                <div class="flex items-center gap-1.5 bg-[#141824] px-2.5 py-1 rounded-md border border-[#232938]">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span class="text-red-400">มีผู้ใช้งาน (Occupied)</span>
                </div>
                <div class="flex items-center gap-1.5 bg-[#141824] px-2.5 py-1 rounded-md border border-[#232938]">
                    <span class="w-2 h-2 rounded-full bg-zinc-500"></span>
                    <span class="text-zinc-400">ปรับปรุง (Maintenance)</span>
                </div>
            </div>
        </div>

        @if ($activeSession)
            <div class="p-4 rounded-xl bg-amber-950/30 border border-amber-500/30 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                    <p class="text-sm font-medium text-amber-200">
                        ขณะนี้คุณกำลังเปิดใช้งานเครื่อง <span class="underline font-bold text-amber-300">{{ $activeSession->seat->seat_number }}</span> อยู่
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="salai-btn-primary text-xs py-1.5 px-3.5">
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
                            <h3 class="text-lg font-bold text-white font-sans">{{ $zone->name }}</h3>
                            <p class="text-xs text-zinc-400">{{ $zone->description }}</p>
                        </div>
                    </div>
                    <div class="salai-badge-red text-xs self-start sm:self-auto">
                        ฿{{ number_format($zone->hourly_rate, 2) }} / ชม.
                    </div>
                </div>

                <!-- Seat Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach ($zone->seats as $seat)
                        @php
                            $isCurrent = $activeSession && $activeSession->seat_id === $seat->id;
                        @endphp
                        <div
                            @if ($seat->isAvailable() && ! $activeSession)
                                onclick="openCheckInModal({{ $seat->id }}, '{{ $seat->seat_number }}', '{{ $zone->name }}', {{ $zone->hourly_rate }})"
                                role="button"
                            @endif
                            class="p-4 rounded-xl border flex flex-col items-center justify-between text-center transition-colors duration-150
                                {{ $isCurrent ? 'bg-amber-950/40 border-amber-500 ring-1 ring-amber-500' : '' }}
                                @if (! $isCurrent)
                                    @if($seat->status === 'available')
                                        bg-[#141824] border-[#232938] hover:border-red-500/60 cursor-pointer
                                    @elseif($seat->status === 'occupied')
                                        bg-[#0e1017] border-red-950/50 cursor-not-allowed opacity-60
                                    @else
                                        bg-[#0e1017] border-zinc-800 cursor-not-allowed opacity-40
                                    @endif
                                @endif
                            "
                        >
                            <div class="w-9 h-9 rounded-lg mb-2 flex items-center justify-center font-bold text-sm
                                @if($isCurrent) bg-amber-500 text-black
                                @elseif($seat->status === 'available') bg-emerald-600/20 text-emerald-400 border border-emerald-500/30
                                @elseif($seat->status === 'occupied') bg-red-600/20 text-red-400 border border-red-500/30
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
                                @elseif($seat->status === 'available') กดเพื่อเปิดเครื่อง
                                @elseif($seat->status === 'occupied') ไม่ว่าง
                                @else ปรับปรุง
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Check-in Confirmation Modal -->
        <div id="checkInModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
            <div class="salai-card-glow max-w-lg w-full p-6 space-y-5 shadow-2xl rounded-3xl">
                <div class="flex items-center justify-between border-b border-[#232938] pb-3">
                    <div class="salai-step-header">
                        <span class="salai-step-bar"></span>
                        <h3 class="text-xl font-black text-white font-sans">ยืนยันการเปิดเครื่อง (Check-in)</h3>
                    </div>
                    <button type="button" onclick="closeCheckInModal()" class="text-zinc-400 hover:text-white text-2xl font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('customer.check-in') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="seat_id" id="modalSeatId" value="">

                    <div class="p-4 bg-[#141824] border border-[#232938] rounded-2xl space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-zinc-400">หมายเลขเครื่อง:</span>
                            <span class="font-black text-white text-sm" id="modalSeatNumber">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-400">โซนที่นั่ง:</span>
                            <span class="font-bold text-red-400" id="modalZoneName">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-400">อัตราค่าบริการปกติ:</span>
                            <span class="font-bold text-emerald-400 font-mono" id="modalHourlyRate">-</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-[#1e2430]">
                            <span class="text-zinc-400">ยอดเงินคงเหลือของคุณ:</span>
                            <span class="font-black text-amber-400 text-sm font-mono">฿{{ number_format(auth()->user()->balance ?? 0, 2) }}</span>
                        </div>
                    </div>

                    <!-- Choose Billing Mode -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider">เลือกรูปแบบการคิดค่าบริการ:</label>

                        <label class="flex items-start gap-3 p-3.5 border border-[#232938] bg-[#141824] rounded-2xl cursor-pointer hover:border-red-500/50 transition">
                            <input type="radio" name="billing_mode" value="pay_as_you_go" checked onchange="togglePackageSelect(false)" class="mt-1 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="font-black text-sm text-white">Pay-as-you-go (ตัดเงินตามจริงจาก Wallet)</span>
                                <p class="text-xs text-zinc-400 mt-0.5">ระบบจะคิดเงินตามนาทีที่เล่นจริงและหักจากยอด Wallet เมื่อกดออกจากเครื่อง</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 border border-[#232938] bg-[#141824] rounded-2xl cursor-pointer hover:border-red-500/50 transition">
                            <input type="radio" name="billing_mode" value="package" onchange="togglePackageSelect(true)" class="mt-1 text-red-600 focus:ring-red-500">
                            <div class="flex-1">
                                <span class="font-black text-sm text-white">ใช้แพ็กเกจชั่วโมงสะสม</span>
                                <p class="text-xs text-zinc-400 mt-0.5">หักเวลาจากแพ็กเกจชั่วโมงที่คุณซื้อไว้ล่วงหน้า</p>

                                <div id="packageSelectContainer" class="mt-3 hidden">
                                    @if ($availablePackages->isEmpty())
                                        <p class="text-xs text-red-400 bg-red-950/60 p-2.5 rounded-xl border border-red-500/30">
                                            คุณยังไม่มีแพ็กเกจชั่วโมงสะสม กรุณาเลือก Pay-as-you-go หรือไปซื้อแพ็กเกจก่อน
                                        </p>
                                    @else
                                        <select name="user_package_id" class="w-full text-xs rounded-xl border-[#232938] bg-[#0e1017] p-2.5 text-zinc-200 focus:border-red-500 focus:ring-red-500">
                                            <option value="">-- กรุณาเลือกแพ็กเกจ --</option>
                                            @foreach ($availablePackages as $upkg)
                                                <option value="{{ $upkg->id }}">
                                                    {{ $upkg->package->name }} (เหลือ {{ $upkg->remaining_minutes }} นาที)
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" onclick="closeCheckInModal()" class="salai-card text-xs font-semibold px-4 py-2 text-zinc-400 hover:text-white border-[#262d3d]">
                            ยกเลิก
                        </button>
                        <button type="submit" class="salai-btn-primary text-xs py-2 px-6">
                            🚀 ยืนยันเปิดเครื่อง
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCheckInModal(seatId, seatNumber, zoneName, hourlyRate) {
            document.getElementById('modalSeatId').value = seatId;
            document.getElementById('modalSeatNumber').innerText = seatNumber;
            document.getElementById('modalZoneName').innerText = zoneName;
            document.getElementById('modalHourlyRate').innerText = '฿' + Number(hourlyRate).toFixed(2) + ' / ชม.';
            const modal = document.getElementById('checkInModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCheckInModal() {
            const modal = document.getElementById('checkInModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function togglePackageSelect(isPackage) {
            const container = document.getElementById('packageSelectContainer');
            if (isPackage) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
</x-layouts::app>
