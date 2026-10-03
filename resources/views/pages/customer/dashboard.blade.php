<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-6">
        <!-- 1. Marquee Bar: แถบตัววิ่งข่าวสารและโปรโมชันร้าน -->
        <div class="overflow-hidden rounded-xl border border-red-500/30 bg-[#0d1019] py-2 px-3 flex items-center gap-3 shadow-inner">
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-600/20 border border-red-500/40 text-red-400 text-xs font-bold shrink-0">
                <span class="animate-pulse">📢</span>
                <span>ประกาศร้าน</span>
            </div>
            <div class="overflow-hidden relative flex-1 cursor-default">
                <div class="animate-marquee items-center text-xs text-zinc-300 gap-8">
                    <span class="flex items-center gap-2">
                        <span class="text-amber-400 font-bold">🌙 โปรเหมาดึก (Night Owl):</span> 23:00 - 06:00 น. เหมาเล่น 7 ชั่วโมง เพียง 120 บาท ที่โซน Standard & VIP
                    </span>
                    <span class="text-zinc-600">•</span>
                    <span class="flex items-center gap-2">
                        <span class="text-red-400 font-bold">🏆 Tournament สัปดาห์นี้:</span> VALORANT Community Cup ชิงรางวัลรวมกว่า 5,000 บาท สมัครได้ที่หน้าเคาน์เตอร์
                    </span>
                    <span class="text-zinc-600">•</span>
                    <span class="flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">🍜 ครัวเปิดบริการถึง 02:00 น.:</span> รามยอนหม้อไฟเกาหลี และ ข้าวไข่ข้นกะเพราเนื้อโคขุน สั่งส่งตรงถึงเครื่องได้ทันที
                    </span>
                    <span class="text-zinc-600">•</span>
                    <span class="flex items-center gap-2">
                        <span class="text-cyan-400 font-bold">⚡ อัปเดตเกมล่าสุด:</span> CS2 Source 2, Valorant v10.4 และ Genshin Impact v5.2 ติดตั้งพร้อมเล่นทุกเครื่อง
                    </span>
                    <span class="text-zinc-600">•</span>
                    <span class="flex items-center gap-2">
                        <span class="text-purple-400 font-bold">🎁 โปรโมชันเติมเงิน:</span> เติมเงินผ่าน QR Code ครบ 300 บาท รับฟรีเวลาเล่นสะสม 30 นาที
                    </span>

                    <!-- Loop duplication for continuous marquee scroll -->
                    <span class="text-zinc-600 ml-8">•</span>
                    <span class="flex items-center gap-2">
                        <span class="text-amber-400 font-bold">🌙 โปรเหมาดึก (Night Owl):</span> 23:00 - 06:00 น. เหมาเล่น 7 ชั่วโมง เพียง 120 บาท ที่โซน Standard & VIP
                    </span>
                    <span class="text-zinc-600">•</span>
                    <span class="flex items-center gap-2">
                        <span class="text-red-400 font-bold">🏆 Tournament สัปดาห์นี้:</span> VALORANT Community Cup ชิงรางวัลรวมกว่า 5,000 บาท สมัครได้ที่หน้าเคาน์เตอร์
                    </span>
                    <span class="text-zinc-600">•</span>
                    <span class="flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">🍜 ครัวเปิดบริการถึง 02:00 น.:</span> รามยอนหม้อไฟเกาหลี และ ข้าวไข่ข้นกะเพราเนื้อโคขุน สั่งส่งตรงถึงเครื่องได้ทันที
                    </span>
                    <span class="text-zinc-600">•</span>
                    <span class="flex items-center gap-2">
                        <span class="text-cyan-400 font-bold">⚡ อัปเดตเกมล่าสุด:</span> CS2 Source 2, Valorant v10.4 และ Genshin Impact v5.2 ติดตั้งพร้อมเล่นทุกเครื่อง
                    </span>
                    <span class="text-zinc-600">•</span>
                    <span class="flex items-center gap-2">
                        <span class="text-purple-400 font-bold">🎁 โปรโมชันเติมเงิน:</span> เติมเงินผ่าน QR Code ครบ 300 บาท รับฟรีเวลาเล่นสะสม 30 นาที
                    </span>
                </div>
            </div>
        </div>

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

        <!-- 2. Time Warning Alert: แจ้งเตือนก่อนเวลาหมด 15 นาที -->
        @if ($sessionRemainingMinutes !== null && $sessionRemainingMinutes <= 15)
            <div class="p-4 rounded-xl bg-gradient-to-r from-red-950/90 via-[#200e14] to-amber-950/80 border border-red-500/60 shadow-xl pulse-glow-red flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-600/30 border border-red-500/50 flex items-center justify-center text-xl shrink-0 animate-bounce">
                        ⏳
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-red-300">
                                แจ้งเตือนเวลาการใช้งาน: เหลือเวลาอีกประมาณ {{ $sessionRemainingMinutes }} นาที!
                            </h4>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-600 text-white animate-pulse">
                                TIME LOW
                            </span>
                        </div>
                        <p class="text-xs text-zinc-300 mt-1">
                            ระบบจะตัดเวลาและปิดเครื่องอัตโนมัติเมื่อครบกำหนด กรุณาเติมเงินเข้า Wallet หรือซื้อแพ็กเกจเพิ่มเพื่อเล่นต่อได้อย่างต่อเนื่อง
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('customer.topup') }}" class="btn-primary text-xs py-2 px-4 whitespace-nowrap shadow-lg shadow-red-600/40">
                        💳 เติมเงินต่อเวลาทันที
                    </a>
                </div>
            </div>

            <!-- Pop-up Modal แจ้งเตือน 15 นาทีสุดท้าย -->
            <div
                x-data="{ showWarningModal: true }"
                x-show="showWarningModal"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            >
                <div
                    @click.away="showWarningModal = false"
                    class="relative w-full max-w-md rounded-2xl bg-[#111420] border border-red-500/50 p-6 shadow-2xl text-center space-y-4 pulse-glow-red"
                >
                    <div class="mx-auto w-14 h-14 rounded-2xl bg-red-500/20 border border-red-500/40 flex items-center justify-center text-3xl">
                        ⚠️
                    </div>

                    <div class="space-y-1">
                        <span class="badge-red text-[11px]">TIME EXPIRING SOON</span>
                        <h3 class="text-xl font-black text-white font-sans">
                            เวลาใช้งานของคุณใกล้จะหมดแล้ว!
                        </h3>
                        <p class="text-xs text-zinc-400">
                            เหลือเวลาใช้งานอีกประมาณ <span class="text-red-400 font-bold text-sm">{{ $sessionRemainingMinutes }} นาที</span>
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-[#161a28] border border-[#232938] text-xs text-left space-y-2">
                        @if ($activeSession)
                            <div class="flex justify-between text-zinc-300">
                                <span>เครื่องที่เปิดใช้งาน:</span>
                                <span class="font-bold text-white">เครื่อง {{ $activeSession->seat->seat_number }} ({{ $activeSession->seat->zone->name }})</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-zinc-300">
                            <span>เงินในกระเป๋าคงเหลือ:</span>
                            <span class="font-bold font-mono text-amber-400">฿{{ number_format($user->balance, 2) }}</span>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col gap-2">
                        <a href="{{ route('customer.topup') }}" class="btn-primary text-xs py-2.5 w-full text-center block">
                            💳 เติมเงิน Wallet หรือซื้อแพ็กเกจต่อเวลา
                        </a>
                        <button
                            type="button"
                            @click="showWarningModal = false"
                            class="card text-xs font-semibold py-2 w-full text-zinc-400 hover:text-white border-[#262d3d] hover:border-zinc-500"
                        >
                            รับทราบ (ปิดหน้าต่างนี้)
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Profile & Wallet Summary Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- User Profile Card -->
            <div class="card p-6 flex flex-col justify-between">
                <div class="space-y-2">
                    <span class="badge-red text-[10px]">MEMBER PROFILE</span>
                    <p class="text-xs text-zinc-400">ยินดีต้อนรับเข้าสู่ระบบ</p>
                    <h2 class="text-2xl font-black text-white tracking-wide font-sans">{{ $user->name }}</h2>
                </div>
                <div class="mt-4 pt-3 border-t border-[#1e2430] flex items-center justify-between text-xs">
                    <span class="font-mono text-zinc-400">@ {{ $user->username }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-600/20 text-red-400 border border-red-500/30">
                        {{ strtoupper($user->role) }}
                    </span>
                </div>
            </div>

            <!-- Wallet Balance Card -->
            <div class="card-glow p-6 rounded-2xl flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-zinc-400">ยอดเงินในกระเป๋า (Wallet)</span>
                        <span class="badge-gold text-[10px]">฿ THB</span>
                    </div>
                    <div class="text-3xl font-bold text-amber-400 font-mono">
                        ฿{{ number_format($user->balance, 2) }}
                    </div>
                    @if(isset($availableBalance) && $availableBalance < (float)$user->balance)
                        <div class="space-y-1 pt-1">
                            <p class="text-xs text-emerald-400 font-semibold flex items-center justify-between">
                                <span>คงเหลือใช้สั่งอาหาร:</span>
                                <span class="font-mono font-bold">฿{{ number_format($availableBalance, 2) }}</span>
                            </p>
                            <p class="text-[10px] text-zinc-500">
                                (กันไว้สำหรับค่าบริการเครื่อง: ฿{{ number_format((float)$user->balance - $availableBalance, 2) }})
                            </p>
                        </div>
                    @else
                        <p class="text-[11px] text-zinc-400">ใช้จ่ายค่าชั่วโมงและสั่งอาหารได้ทันที</p>
                    @endif
                </div>
                <div class="mt-4 pt-3 border-t border-[#232938]">
                    <a href="{{ route('customer.topup') }}" class="btn-primary text-xs w-full py-2 block text-center">
                        + เติมเงิน Wallet อัตโนมัติ
                    </a>
                </div>
            </div>

            <!-- Packages Card -->
            <div class="card p-6 flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-zinc-400">แพ็กเกจชั่วโมงสะสม</span>
                        <span class="badge-red text-[10px]">TIME PACK</span>
                    </div>
                    <div class="text-3xl font-bold text-white font-mono">
                        {{ $userPackages->sum('remaining_minutes') }} <span class="text-sm font-normal text-zinc-400 font-sans">นาที</span>
                    </div>
                    <p class="text-[11px] text-zinc-400">หักเวลาอัตโนมัติเมื่อเช็คอินในร้าน</p>
                </div>
                <div class="mt-4 pt-3 border-t border-[#1e2430]">
                    <a href="{{ route('customer.seat-map') }}" class="card text-xs font-semibold py-2 w-full text-center block text-zinc-200 hover:text-white border-[#262d3d] hover:border-red-500/50">
                        🖥️ เปิดดูผังที่นั่ง (Seat Map)
                    </a>
                </div>
            </div>
        </div>

        <!-- Valorant Gaming Event & Tournament Banner -->
        <div class="relative overflow-hidden rounded-2xl border border-red-500/30 p-5 bg-[#0e111a] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <img
                src="https://cmsassets.rgpub.io/sanity/images/dsfx7636/news_live/4c679fc9b8f253d5915261338f81fe1043fded45-3440x1020.jpg?accountingTag=VAL&fit=fill&fm=jpg&q=80&h=1020"
                alt="Valorant Gaming Event"
                class="absolute inset-0 w-full h-full object-cover object-right md:object-center opacity-35 select-none pointer-events-none"
            >
            <div class="absolute inset-0 bg-gradient-to-r from-[#0c0f17] via-[#0c0f17]/90 to-transparent pointer-events-none"></div>

            <div class="relative z-10 space-y-1">
                <span class="badge-red text-[10px]">SPECIAL EVENT</span>
                <h3 class="text-base font-black text-white font-sans">VALORANT Community Watch Party & Tournament @ Letsplay Gaming</h3>
                <p class="text-xs text-zinc-300">เปิดเครื่องโซน VIP วันนี้ รับสิทธิ์เข้าร่วมแข่งขันและรับชาร้อนเกาหลีฟรี 1 แก้ว</p>
            </div>
            <div class="relative z-10">
                <a href="{{ route('customer.seat-map') }}" class="btn-primary text-xs py-2 px-4 whitespace-nowrap block text-center">
                    จองที่นั่งในร้าน &rarr;
                </a>
            </div>
        </div>

        <!-- Active Session Section (PC Bang HUD In-Page) -->
        <div class="card p-6">
            <div class="flex flex-wrap items-center justify-between border-b border-[#1e2430] pb-4 mb-5 gap-3">
                <div class="step-header">
                    <span class="step-bar"></span>
                    <h3 class="text-lg font-bold text-white tracking-wide font-sans">
                        สถานะการใช้งานเครื่องคอมพิวเตอร์ปัจจุบัน
                    </h3>
                </div>
                @if ($activeSession)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        กำลังใช้งาน (ACTIVE)
                    </span>
                @endif
            </div>

            @if ($activeSession)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 bg-[#141824] border border-[#232938] rounded-xl space-y-1">
                        <p class="text-[11px] text-zinc-400">ที่นั่งคอมพิวเตอร์</p>
                        <p class="text-2xl font-black text-white">{{ $activeSession->seat->seat_number }}</p>
                        <p class="text-xs text-red-400 font-semibold">{{ $activeSession->seat->zone->name }}</p>
                    </div>

                    <div class="p-4 bg-[#141824] border border-[#232938] rounded-xl space-y-1">
                        <p class="text-[11px] text-zinc-400">เวลาที่เริ่มเล่น</p>
                        <p class="text-2xl font-black text-white font-mono">
                            {{ \Illuminate\Support\Carbon::parse($activeSession->start_time)->format('H:i:s') }}
                        </p>
                        <p class="text-xs text-zinc-400">เล่นไปแล้ว: <span class="font-bold text-white">{{ $elapsedMinutes }}</span> นาที</p>
                    </div>

                    <div class="p-4 bg-[#141824] border border-[#232938] rounded-xl space-y-1">
                        <p class="text-[11px] text-zinc-400">โหมดการคิดเงิน</p>
                        @if ($activeSession->userPackage)
                            <p class="text-base font-bold text-red-400">
                                {{ $activeSession->userPackage->package->name }}
                            </p>
                            <p class="text-xs text-zinc-400">เหลือ: {{ $activeSession->userPackage->remaining_minutes }} นาที</p>
                        @else
                            <p class="text-base font-bold text-amber-400">Pay-as-you-go</p>
                            <p class="text-xs text-zinc-400">฿{{ number_format($activeSession->rate_snapshot, 2) }} / ชม.</p>
                        @endif
                    </div>

                    <div class="p-4 bg-[#141824] border border-[#232938] rounded-xl space-y-1">
                        <p class="text-[11px] text-zinc-400">ค่าบริการขณะนี้ (โดยประมาณ)</p>
                        <p class="text-2xl font-black text-emerald-400 font-mono">
                            ฿{{ number_format($estimatedCost, 2) }}
                        </p>
                        <p class="text-xs text-zinc-400">ตัดยอดเมื่อกดยืนยัน Check-out</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <form method="POST" action="{{ route('customer.check-out') }}" onsubmit="return confirm('คุณต้องการเช็คเอาท์และปิดเซสชันการเล่นหรือไม่?');">
                        @csrf
                        <button type="submit" class="btn-primary text-xs py-2.5 px-5">
                            🚪 ออกจากเครื่อง (Check-out)
                        </button>
                    </form>

                    <a
                        href="{{ route('customer.food-order') }}?seat_id={{ $activeSession->seat_id }}"
                        class="card text-xs font-semibold px-5 py-2.5 text-zinc-200 hover:text-white border-[#262d3d] hover:border-red-500/50 inline-flex items-center gap-1.5"
                    >
                        🍜 สั่งอาหารส่งมาที่เครื่อง {{ $activeSession->seat->seat_number }}
                    </a>
                </div>
            @else
                <div class="text-center py-10 space-y-3">
                    <div class="text-4xl">🖥️</div>
                    <p class="text-sm text-zinc-400">คุณยังไม่ได้เปิดใช้งานเครื่องคอมพิวเตอร์ในขณะนี้</p>
                    <div class="pt-2">
                        <a href="{{ route('customer.seat-map') }}" class="btn-primary text-xs py-2.5 px-6">
                            เลือกที่นั่งในร้านเพื่อเริ่มต้นเล่น (Check-in)
                        </a>
                    </div>
                </div>
            @endif
        </div>


        <!-- Active Packages List -->
        @if ($userPackages->isNotEmpty())
            <div class="card p-6 space-y-4">
                <div class="step-header">
                    <span class="step-bar"></span>
                    <h3 class="text-lg font-black text-white tracking-wide font-sans">
                        แพ็กเกจชั่วโมงที่คุณถืออยู่
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($userPackages as $upkg)
                        <div class="p-4 bg-[#141824] border border-[#232938] rounded-xl flex items-center justify-between">
                            <div>
                                <p class="font-bold text-white text-sm">{{ $upkg->package->name }}</p>
                                <p class="text-[11px] text-zinc-400 mt-0.5">ซื้อเมื่อ: {{ $upkg->purchased_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="text-sm font-black text-red-400 bg-red-500/10 px-2.5 py-1 rounded-lg border border-red-500/20">
                                {{ $upkg->remaining_minutes }} นาที
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tables: Recent Transactions and Recent Orders -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Wallet History -->
            <div class="card p-6 space-y-4">
                <div class="step-header">
                    <span class="step-bar"></span>
                    <h3 class="text-base font-black text-white tracking-wide font-sans">
                        ประวัติเงินในกระเป๋าล่าสุด
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
                            @forelse ($transactions as $tx)
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
                                        {{ $tx->created_at->format('d/m H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-zinc-500 text-xs">ไม่มีรายการธุรกรรม</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Food Orders -->
            <div class="card p-6 space-y-4">
                <div class="step-header">
                    <span class="step-bar"></span>
                    <h3 class="text-base font-black text-white tracking-wide font-sans">
                        คำสั่งซื้ออาหารล่าสุด
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-300">
                        <thead class="bg-[#141824] text-[11px] uppercase text-zinc-400 border-b border-[#1e2430]">
                            <tr>
                                <th class="py-2.5 px-3">เลขบิล / โต๊ะ</th>
                                <th class="py-2.5 px-3">ยอดรวม</th>
                                <th class="py-2.5 px-3">สถานะอาหาร</th>
                                <th class="py-2.5 px-3">สถานะเงิน</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1e2430]">
                            @forelse ($recentOrders as $ord)
                                <tr class="hover:bg-[#141824]/60 transition">
                                    <td class="py-2.5 px-3">
                                        <span class="font-bold text-white">#{{ $ord->id }}</span>
                                        <span class="text-[10px] text-red-400 block font-semibold">{{ $ord->seat->seat_number }}</span>
                                    </td>
                                    <td class="py-2.5 px-3 font-bold text-white font-mono">
                                        ฿{{ number_format($ord->total_amount, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                            @if($ord->order_status === 'served') bg-emerald-500/20 text-emerald-400 border border-emerald-500/30
                                            @elseif($ord->order_status === 'preparing') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                            @elseif($ord->order_status === 'cancelled') bg-red-500/20 text-red-400 border border-red-500/30
                                            @else bg-amber-500/20 text-amber-400 border border-amber-500/30
                                            @endif
                                        ">
                                            {{ $ord->order_status }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $ord->payment_status === 'paid' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                                            {{ $ord->payment_status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-zinc-500 text-xs">ยังไม่มีคำสั่งซื้ออาหาร</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Floating Status Bar (PC Bang Client HUD วิดเจ็ตลอยมุมจอ) -->
    @if ($activeSession)
        <div
            x-data="{
                minimized: false,
                elapsedSeconds: {{ $elapsedMinutes * 60 }},
                remainingSeconds: {{ $sessionRemainingMinutes !== null ? $sessionRemainingMinutes * 60 : 'null' }},
                formatTime(totalSecs) {
                    if (totalSecs === null || totalSecs < 0) totalSecs = 0;
                    const h = String(Math.floor(totalSecs / 3600)).padStart(2, '0');
                    const m = String(Math.floor((totalSecs % 3600) / 60)).padStart(2, '0');
                    const s = String(totalSecs % 60).padStart(2, '0');
                    return `${h}:${m}:${s}`;
                },
                init() {
                    setInterval(() => {
                        this.elapsedSeconds++;
                        if (this.remainingSeconds !== null && this.remainingSeconds > 0) {
                            this.remainingSeconds--;
                        }
                    }, 1000);
                }
            }"
            class="fixed bottom-6 right-6 z-40 select-none"
        >
            <!-- Minimized Pill State -->
            <div
                x-show="minimized"
                x-cloak
                @click="minimized = false"
                class="flex items-center gap-3 px-4 py-2.5 rounded-full bg-[#0d1019]/95 border border-red-500/40 shadow-2xl backdrop-blur-md cursor-pointer hover:border-red-400 transition pulse-glow-red"
            >
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-xs font-bold text-white font-mono">เครื่อง {{ $activeSession->seat->seat_number }}</span>
                <span class="text-zinc-600">|</span>
                @if ($sessionRemainingMinutes !== null)
                    <span class="text-xs font-mono font-bold text-amber-400" x-text="'⏳ ' + formatTime(remainingSeconds)"></span>
                @else
                    <span class="text-xs font-mono font-bold text-zinc-300" x-text="'⏱️ ' + formatTime(elapsedSeconds)"></span>
                @endif
                <span class="text-zinc-600">|</span>
                <span class="text-xs font-mono font-bold text-emerald-400">฿{{ number_format($estimatedCost, 2) }}</span>
                <button
                    type="button"
                    class="ml-1 text-[10px] text-zinc-400 hover:text-white px-1.5 py-0.5 rounded bg-zinc-800 border border-zinc-700 font-bold"
                    title="ขยายหน้าต่าง"
                >
                    +
                </button>
            </div>

            <!-- Expanded Floating HUD Card -->
            <div
                x-show="!minimized"
                x-cloak
                class="w-80 rounded-2xl bg-[#0d1019]/95 border border-[#262e40] shadow-2xl backdrop-blur-md p-4 text-white space-y-3 pulse-glow-red"
            >
                <!-- HUD Header -->
                <div class="flex items-center justify-between border-b border-[#1e2430] pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-xs font-black tracking-wider text-red-400 font-sans">LETSPLAY CLIENT HUD</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-red-600/20 text-red-300 border border-red-500/30">
                            {{ $activeSession->seat->seat_number }} ({{ $activeSession->seat->zone->name }})
                        </span>
                        <button
                            type="button"
                            @click="minimized = true"
                            class="text-zinc-400 hover:text-white text-xs px-1.5 py-0.5 rounded hover:bg-zinc-800 transition"
                            title="ย่อขนาด"
                        >
                            _
                        </button>
                    </div>
                </div>

                <!-- Timer & Cost Metrics -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-[#141824] border border-[#232938]">
                        <p class="text-[10px] text-zinc-400">
                            @if ($sessionRemainingMinutes !== null)
                                เวลาคงเหลือ
                            @else
                                เวลาเล่นไปแล้ว
                            @endif
                        </p>
                        <p class="text-lg font-black font-mono mt-0.5 {{ $sessionRemainingMinutes !== null && $sessionRemainingMinutes <= 15 ? 'text-red-400 animate-pulse' : 'text-amber-400' }}">
                            @if ($sessionRemainingMinutes !== null)
                                <span x-text="formatTime(remainingSeconds)"></span>
                            @else
                                <span x-text="formatTime(elapsedSeconds)"></span>
                            @endif
                        </p>
                    </div>

                    <div class="p-2.5 rounded-xl bg-[#141824] border border-[#232938]">
                        <p class="text-[10px] text-zinc-400">ค่าบริการขณะนี้</p>
                        <p class="text-lg font-black font-mono text-emerald-400 mt-0.5">
                            ฿{{ number_format($estimatedCost, 2) }}
                        </p>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="grid grid-cols-3 gap-1.5 pt-1">
                    <a
                        href="{{ route('customer.food-order') }}?seat_id={{ $activeSession->seat_id }}"
                        class="p-2 rounded-lg bg-[#141824] hover:bg-[#1a2030] border border-[#232938] hover:border-red-500/40 text-center transition flex flex-col items-center gap-1 text-[10px] text-zinc-300 hover:text-white"
                    >
                        <span class="text-sm">🍜</span>
                        <span>สั่งอาหาร</span>
                    </a>
                    <a
                        href="{{ route('customer.topup') }}"
                        class="p-2 rounded-lg bg-[#141824] hover:bg-[#1a2030] border border-[#232938] hover:border-amber-500/40 text-center transition flex flex-col items-center gap-1 text-[10px] text-zinc-300 hover:text-white"
                    >
                        <span class="text-sm">💳</span>
                        <span>เติมเงิน</span>
                    </a>
                    <form
                        method="POST"
                        action="{{ route('customer.check-out') }}"
                        onsubmit="return confirm('คุณต้องการเช็คเอาท์และปิดเครื่องหรือไม่?');"
                        class="m-0"
                    >
                        @csrf
                        <button
                            type="submit"
                            class="w-full p-2 rounded-lg bg-red-600/20 hover:bg-red-600 border border-red-500/30 hover:border-red-600 text-center transition flex flex-col items-center gap-1 text-[10px] text-red-300 hover:text-white"
                        >
                            <span class="text-sm">🚪</span>
                            <span>เช็คเอาท์</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-layouts::app>
