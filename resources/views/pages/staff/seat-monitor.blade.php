<x-layouts::app :title="__('Seat Monitor')">
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

        <!-- Header & Live Status Stats -->
        <div class="salai-card p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="text-2xl font-black text-white font-sans">แดชบอร์ดมอนิเตอร์ที่นั่ง (Live Seat Monitor)</h2>
                </div>
                <p class="text-xs text-zinc-400 mt-1">มอนิเตอร์สถานะเครื่องคอมพิวเตอร์และเซสชันลูกค้าแบบเรียลไทม์</p>
            </div>

            <!-- Quick Counters -->
            <div class="flex flex-wrap gap-2 text-xs font-semibold">
                <span class="px-3 py-1.5 rounded-xl bg-[#141824] border border-[#232938] text-zinc-300">
                    ทั้งหมด: {{ $totalSeats }} เครื่อง
                </span>
                <span class="px-3 py-1.5 rounded-xl bg-red-950/60 border border-red-500/40 text-red-300">
                    เล่นอยู่: {{ $occupiedSeats }} เครื่อง
                </span>
                <span class="px-3 py-1.5 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300">
                    ว่าง: {{ $availableSeats }} เครื่อง
                </span>
                <span class="px-3 py-1.5 rounded-xl bg-zinc-800 text-zinc-400 border border-zinc-700">
                    ซ่อมบำรุง: {{ $maintenanceSeats }} เครื่อง
                </span>
            </div>
        </div>

        <!-- Zones & Seats -->
        @foreach ($zones as $zone)
            <div class="salai-card p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-[#1e2430] pb-2">
                    <div class="flex items-center gap-2">
                        <span class="salai-step-bar"></span>
                        <h3 class="font-bold text-base text-white">{{ $zone->name }}</h3>
                    </div>
                    <span class="text-xs font-bold text-emerald-400 font-mono">
                        ฿{{ number_format($zone->hourly_rate, 2) }} / ชม.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($zone->seats as $seat)
                        @php
                            $session = $seat->sessions->first();
                            $elapsedMins = 0;
                            if ($session) {
                                $startTime = \Illuminate\Support\Carbon::parse($session->start_time);
                                $elapsedSeconds = max(1, (int) $startTime->diffInSeconds(\Illuminate\Support\Carbon::now()));
                                $elapsedMins = max(1, (int) ceil($elapsedSeconds / 60));
                            }
                        @endphp

                        <div class="p-4 rounded-xl border flex flex-col justify-between transition
                            @if($seat->status === 'occupied') bg-red-950/20 border-red-500/40
                            @elseif($seat->status === 'available') bg-[#141824] border-[#232938]
                            @else bg-[#0e1017] border-zinc-800 opacity-60
                            @endif
                        ">
                            <div>
                                <div class="flex justify-between items-start">
                                    <span class="text-lg font-black text-white font-sans">
                                        {{ $seat->seat_number }}
                                    </span>
                                    <span class="text-[11px] px-2 py-0.5 rounded font-bold
                                        @if($seat->status === 'occupied') bg-red-600/20 text-red-400 border border-red-500/30
                                        @elseif($seat->status === 'available') bg-emerald-600/20 text-emerald-400 border border-emerald-500/30
                                        @else bg-zinc-800 text-zinc-400
                                        @endif
                                    ">
                                        {{ strtoupper($seat->status) }}
                                    </span>
                                </div>

                                @if ($session)
                                    <div class="mt-3 pt-2 border-t border-[#1e2430] space-y-1 text-xs">
                                        <p class="text-zinc-200 font-bold flex justify-between">
                                            <span class="text-zinc-400">ผู้เล่น:</span>
                                            <span class="text-white">{{ $session->user->name }}</span>
                                        </p>
                                        <p class="text-zinc-400 flex justify-between">
                                            <span>เวลาเล่น:</span>
                                            <span class="font-semibold text-white font-mono">{{ $elapsedMins }} นาที</span>
                                        </p>
                                        <p class="text-zinc-400 flex justify-between">
                                            <span>โหมด:</span>
                                            @if($session->userPackage)
                                                <span class="text-red-400 font-semibold truncate max-w-[120px]" title="{{ $session->userPackage->package->name }}">
                                                    {{ $session->userPackage->package->name }}
                                                </span>
                                            @else
                                                <span class="text-amber-400 font-semibold">Pay-as-you-go</span>
                                            @endif
                                        </p>
                                        <p class="text-zinc-400 flex justify-between">
                                            <span>Wallet คงเหลือ:</span>
                                            <span class="font-bold text-amber-400 font-mono">฿{{ number_format($session->user->balance, 2) }}</span>
                                        </p>
                                    </div>
                                @else
                                    <div class="mt-4 py-2 text-center text-xs text-zinc-500">
                                        เครื่องว่าง พร้อมใช้งาน
                                    </div>
                                @endif
                            </div>

                            <!-- Staff Action Buttons -->
                            <div class="mt-4 pt-3 border-t border-[#1e2430]">
                                @if ($session)
                                    <form method="POST" action="{{ route('staff.force-end') }}" onsubmit="return confirm('คุณต้องการสั่งปิดเครื่อง {{ $seat->seat_number }} และคิดเงินทันทีหรือไม่?');">
                                        @csrf
                                        <input type="hidden" name="session_id" value="{{ $session->id }}">
                                        <button
                                            type="submit"
                                            class="w-full py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition"
                                        >
                                            Force End (สั่งปิดเครื่อง)
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('staff.toggle-seat') }}">
                                        @csrf
                                        <input type="hidden" name="seat_id" value="{{ $seat->id }}">
                                        <button
                                            type="submit"
                                            class="w-full py-1.5 text-xs font-semibold text-zinc-300 bg-[#141824] border border-[#232938] hover:border-zinc-500 rounded-lg transition"
                                        >
                                            {{ $seat->status === 'maintenance' ? 'เปิดใช้งานเครื่อง' : 'แจ้งซ่อมบำรุง' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-layouts::app>
