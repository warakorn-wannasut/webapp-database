<div wire:poll.5s class="space-y-6">
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

    <!-- Header & Live Status Stats -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">แดชบอร์ดมอนิเตอร์ที่นั่ง (Live Seat Monitor)</h2>
            </div>
            <p class="text-xs text-zinc-500 mt-1">อัปเดตข้อมูลอัตโนมัติทุก 5 วินาที (Polling)</p>
        </div>

        <!-- Quick Counters -->
        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <span class="px-3 py-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                ทั้งหมด: {{ $totalSeats }} เครื่อง
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-300">
                เล่นอยู่: {{ $occupiedSeats }} เครื่อง
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">
                ว่าง: {{ $availableSeats }} เครื่อง
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-400">
                ซ่อมบำรุง: {{ $maintenanceSeats }} เครื่อง
            </span>
        </div>
    </div>

    <!-- Zones & Seats -->
    @foreach ($zones as $zone)
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-4">
            <div class="flex justify-between items-center border-b border-zinc-100 dark:border-zinc-800 pb-2">
                <h3 class="font-bold text-base text-zinc-900 dark:text-white">{{ $zone->name }}</h3>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                    ฿{{ number_format($zone->hourly_rate, 2) }} / ชม.
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($zone->seats as $seat)
                    @php
                        $session = $seat->activeSession;
                        $elapsedMins = 0;
                        if ($session) {
                            $startTime = \Illuminate\Support\Carbon::parse($session->start_time);
                            $elapsedSeconds = max(1, (int) $startTime->diffInSeconds(\Illuminate\Support\Carbon::now()));
                            $elapsedMins = max(1, (int) ceil($elapsedSeconds / 60));
                        }
                    @endphp

                    <div class="p-4 rounded-xl border flex flex-col justify-between transition
                        @if($seat->status === 'occupied') bg-red-50/30 border-red-200 dark:border-red-900/60 dark:bg-zinc-800/60
                        @elseif($seat->status === 'available') bg-emerald-50/20 border-emerald-200 dark:border-emerald-900/40 dark:bg-zinc-800/40
                        @else bg-zinc-100/50 border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800/20
                        @endif
                    ">
                        <div>
                            <div class="flex justify-between items-start">
                                <span class="text-lg font-black text-zinc-900 dark:text-white">
                                    {{ $seat->seat_number }}
                                </span>
                                <span class="text-[11px] px-2 py-0.5 rounded font-bold
                                    @if($seat->status === 'occupied') bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                                    @elseif($seat->status === 'available') bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300
                                    @else bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300
                                    @endif
                                ">
                                    {{ strtoupper($seat->status) }}
                                </span>
                            </div>

                            @if ($session)
                                <div class="mt-3 pt-2 border-t border-zinc-200/60 dark:border-zinc-700/60 space-y-1 text-xs">
                                    <p class="text-zinc-900 dark:text-white font-bold flex justify-between">
                                        <span>ผู้เล่น:</span>
                                        <span class="text-indigo-600 dark:text-indigo-400">{{ $session->user->name }}</span>
                                    </p>
                                    <p class="text-zinc-500 flex justify-between">
                                        <span>เวลาเล่น:</span>
                                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $elapsedMins }} นาที</span>
                                    </p>
                                    <p class="text-zinc-500 flex justify-between">
                                        <span>โหมด:</span>
                                        @if($session->userPackage)
                                            <span class="text-indigo-600 font-semibold truncate max-w-[120px]" title="{{ $session->userPackage->package->name }}">
                                                {{ $session->userPackage->package->name }}
                                            </span>
                                        @else
                                            <span class="text-amber-600 font-semibold">Pay-as-you-go</span>
                                        @endif
                                    </p>
                                    <p class="text-zinc-500 flex justify-between">
                                        <span>Wallet คงเหลือ:</span>
                                        <span class="font-bold text-emerald-600">฿{{ number_format($session->user->balance, 2) }}</span>
                                    </p>
                                </div>
                            @else
                                <div class="mt-4 py-2 text-center text-xs text-zinc-400">
                                    เครื่องว่าง พร้อมใช้งาน
                                </div>
                            @endif
                        </div>

                        <!-- Staff Action Buttons -->
                        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-700/50 flex gap-2">
                            @if ($session)
                                <button
                                    wire:click="forceEnd({{ $session->id }})"
                                    wire:confirm="คุณต้องการสั่งปิดเครื่อง {{ $seat->seat_number }} และคิดเงินทันทีหรือไม่?"
                                    class="w-full py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition"
                                >
                                    Force End (สั่งปิดเครื่อง)
                                </button>
                            @else
                                <button
                                    wire:click="toggleMaintenance({{ $seat->id }})"
                                    class="w-full py-1.5 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 rounded-lg transition"
                                >
                                    {{ $seat->status === 'maintenance' ? 'เปิดใช้งานเครื่อง' : 'แจ้งซ่อมบำรุง' }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
