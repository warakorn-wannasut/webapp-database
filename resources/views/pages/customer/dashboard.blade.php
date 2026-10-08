<x-layouts::app :title="__('Dashboard')">
    <div class="d-flex flex-column gap-4">
        <!-- 1. Marquee Bar: แถบตัววิ่งข่าวสารและโปรโมชันร้าน -->
        <div class="card bg-dark border-danger-subtle rounded-3 py-2 px-3 shadow-sm">
            <div class="d-flex align-items-center gap-3 overflow-hidden">
                <div class="badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2.5 d-flex align-items-center gap-1 flex-shrink-0">
                    <span class="spinner-grow spinner-grow-sm text-danger" style="width: 8px; height: 8px;"></span>
                    <span class="fw-bold">ประกาศร้าน</span>
                </div>
                <div class="overflow-hidden position-relative flex-grow-1">
                    <div class="animate-marquee d-flex align-items-center small text-light gap-4">
                        <span><strong class="text-warning">🌙 โปรเหมาดึก (Night Owl):</strong> 23:00 - 06:00 น. เหมาเล่น 7 ชั่วโมง เพียง 120 บาท ที่โซน Standard & VIP</span>
                        <span class="text-secondary">•</span>
                        <span><strong class="text-danger">🏆 Tournament สัปดาห์นี้:</strong> VALORANT Community Cup ชิงรางวัลรวมกว่า 5,000 บาท สมัครได้ที่เคาน์เตอร์</span>
                        <span class="text-secondary">•</span>
                        <span><strong class="text-success">🍜 ครัวเปิดถึง 02:00 น.:</strong> รามยอนหม้อไฟเกาหลี ข้าวไข่ข้นกะเพราเนื้อโคขุน สั่งส่งตรงถึงโต๊ะได้ทันที</span>
                        <span class="text-secondary">•</span>
                        <span><strong class="text-info">⚡ อัปเดตเกมล่าสุด:</strong> CS2, Valorant v10.4, Genshin Impact ติดตั้งพร้อมเล่นทุกเครื่อง</span>
                        <span class="text-secondary">•</span>
                        <span><strong class="text-warning">🎁 โปรโมชันเติมเงิน:</strong> เติมผ่าน QR Code ครบ 300 บาท รับฟรีเวลาเล่นสะสม 30 นาที</span>

                        <!-- Loop duplication -->
                        <span class="text-secondary ms-4">•</span>
                        <span><strong class="text-warning">🌙 โปรเหมาดึก (Night Owl):</strong> 23:00 - 06:00 น. เหมาเล่น 7 ชั่วโมง เพียง 120 บาท</span>
                        <span class="text-secondary">•</span>
                        <span><strong class="text-danger">🏆 Tournament สัปดาห์นี้:</strong> VALORANT Community Cup ชิงรางวัลรวมกว่า 5,000 บาท</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        @if (session()->has('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 border-success-subtle bg-success-subtle text-success py-3 px-4 rounded-3 m-0">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 border-danger-subtle bg-danger-subtle text-danger py-3 px-4 rounded-3 m-0">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <!-- 2. Time Warning Alert: แจ้งเตือนก่อนเวลาหมด 15 นาที -->
        @if ($sessionRemainingMinutes !== null && $sessionRemainingMinutes <= 15)
            <div class="alert alert-danger border-danger-subtle bg-danger-subtle text-danger d-flex flex-column flex-sm-row justify-content-between align-items-sm-center p-3 rounded-4 shadow-sm m-0 gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-danger text-white d-flex align-items-center justify-content-center fs-4 flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-hourglass-bottom"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="h6 fw-bold text-white m-0">
                                แจ้งเตือนเวลาการใช้งาน: เหลือเวลาอีกประมาณ {{ $sessionRemainingMinutes }} นาที!
                            </h4>
                            <span class="badge bg-danger">TIME LOW</span>
                        </div>
                        <small class="text-secondary d-block mt-1">
                            ระบบจะตัดเวลาและปิดเครื่องอัตโนมัติเมื่อครบกำหนด กรุณาเติมเงินเข้า Wallet หรือซื้อแพ็กเกจเพิ่มเพื่อเล่นต่อ
                        </small>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('customer.topup') }}" class="btn btn-danger btn-sm px-3 fw-bold rounded-pill">
                        💳 เติมเงินต่อเวลาทันที
                    </a>
                </div>
            </div>

            <!-- Pop-up Modal แจ้งเตือน 15 นาทีสุดท้าย -->
            <div class="modal fade show d-block" id="timeExpiringModal" tabindex="-1" style="background: rgba(0,0,0,0.8); backdrop-filter: blur(4px);" x-data="{ showWarningModal: true }" x-show="showWarningModal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-danger shadow-lg p-3 text-center">
                        <div class="modal-body">
                            <div class="mx-auto rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-size: 28px;">
                                ⚠️
                            </div>
                            <span class="badge bg-danger-subtle text-danger mb-2">TIME EXPIRING SOON</span>
                            <h3 class="h5 fw-bold text-white mb-2">
                                เวลาใช้งานของคุณใกล้จะหมดแล้ว!
                            </h3>
                            <p class="small text-secondary mb-3">
                                เหลือเวลาใช้งานอีกประมาณ <span class="text-danger fw-bold">{{ $sessionRemainingMinutes }} นาที</span>
                            </p>

                            <div class="p-3 rounded-3 bg-dark-subtle border border-secondary-subtle text-start small mb-3">
                                @if ($activeSession)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-secondary">เครื่องที่เปิดใช้งาน:</span>
                                        <span class="fw-bold text-white">เครื่อง {{ $activeSession->seat->seat_number }} ({{ $activeSession->seat->zone->name }})</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between">
                                    <span class="text-secondary">เงินในกระเป๋าคงเหลือ:</span>
                                    <span class="fw-bold font-monospace text-warning">฿{{ number_format($user->balance, 2) }}</span>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('customer.topup') }}" class="btn btn-danger btn-sm fw-bold py-2">
                                    💳 เติมเงิน Wallet หรือซื้อแพ็กเกจต่อเวลา
                                </a>
                                <button type="button" @click="showWarningModal = false" class="btn btn-outline-secondary btn-sm py-2">
                                    รับทราบ (ปิดหน้าต่างนี้)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Profile & Wallet Summary Cards -->
        <div class="row g-4">
            <!-- User Profile Card -->
            <div class="col-12 col-md-4">
                <div class="card h-100 bg-dark border-secondary-subtle rounded-4 p-4 d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <span class="badge bg-danger-subtle text-danger mb-2">MEMBER PROFILE</span>
                        <small class="text-secondary d-block">ยินดีต้อนรับเข้าสู่ระบบ</small>
                        <h2 class="h4 fw-bold text-white mt-1 mb-0">{{ $user->name }}</h2>
                    </div>
                    <div class="pt-3 border-top border-secondary-subtle mt-3 d-flex justify-content-between align-items-center small">
                        <span class="text-secondary font-monospace">@ {{ $user->username }}</span>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                            {{ strtoupper($user->role) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Wallet Balance Card -->
            <div class="col-12 col-md-4">
                <div class="card h-100 bg-dark border-secondary-subtle rounded-4 p-4 d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-secondary fw-semibold">ยอดเงินในกระเป๋า (Wallet)</small>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">฿ THB</span>
                        </div>
                        <div class="fs-2 fw-black text-warning font-monospace">
                            ฿{{ number_format($user->balance, 2) }}
                        </div>
                        @if(isset($availableBalance) && $availableBalance < (float)$user->balance)
                            <div class="pt-2">
                                <div class="d-flex justify-content-between align-items-center small text-success fw-semibold">
                                    <span>คงเหลือใช้สั่งอาหาร:</span>
                                    <span class="font-monospace">฿{{ number_format($availableBalance, 2) }}</span>
                                </div>
                                <small class="text-secondary d-block" style="font-size: 10px;">
                                    (กันไว้ค่าเครื่อง: ฿{{ number_format((float)$user->balance - $availableBalance, 2) }})
                                </small>
                            </div>
                        @else
                            <small class="text-secondary d-block mt-1">ใช้จ่ายค่าชั่วโมงและสั่งอาหารได้ทันที</small>
                        @endif
                    </div>
                    <div class="pt-3 border-top border-secondary-subtle mt-3">
                        <a href="{{ route('customer.topup') }}" class="btn btn-danger btn-sm w-100 fw-bold rounded-pill">
                            + เติมเงิน Wallet อัตโนมัติ
                        </a>
                    </div>
                </div>
            </div>

            <!-- Packages Card -->
            <div class="col-12 col-md-4">
                <div class="card h-100 bg-dark border-secondary-subtle rounded-4 p-4 d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-secondary fw-semibold">แพ็กเกจชั่วโมงสะสม</small>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">TIME PACK</span>
                        </div>
                        <div class="fs-2 fw-black text-white font-monospace">
                            {{ $userPackages->sum('remaining_minutes') }} <small class="fs-6 text-secondary fw-normal">นาที</small>
                        </div>
                        <small class="text-secondary d-block mt-1">หักเวลาอัตโนมัติเมื่อเช็คอินในร้าน</small>
                    </div>
                    <div class="pt-3 border-top border-secondary-subtle mt-3">
                        <a href="{{ route('customer.seat-map') }}" class="btn btn-outline-secondary text-light btn-sm w-100 fw-semibold rounded-pill">
                            🖥️ เปิดดูผังที่นั่ง (Seat Map)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valorant Gaming Event & Tournament Banner -->
        <div class="card bg-dark border-danger-subtle rounded-4 p-4 overflow-hidden position-relative shadow-sm">
            <img
                src="https://cmsassets.rgpub.io/sanity/images/dsfx7636/news_live/4c679fc9b8f253d5915261338f81fe1043fded45-3440x1020.jpg?accountingTag=VAL&fit=fill&fm=jpg&q=80&h=1020"
                alt="Valorant Gaming Event"
                class="position-absolute top-0 end-0 bottom-0 start-0 w-100 h-100 object-fit-cover opacity-25"
                style="pointer-events: none;"
            >
            <div class="position-absolute top-0 end-0 bottom-0 start-0 bg-gradient" style="background: linear-gradient(90deg, #0c0f17 30%, rgba(12,15,23,0.85) 70%, transparent 100%); pointer-events: none;"></div>

            <div class="position-relative z-1 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle mb-1">SPECIAL EVENT</span>
                    <h3 class="h5 fw-bold text-white m-0">VALORANT Community Watch Party & Tournament @ Letsplay Gaming</h3>
                    <small class="text-secondary d-block mt-1">เปิดเครื่องโซน VIP วันนี้ รับสิทธิ์เข้าร่วมแข่งขันและรับชาร้อนเกาหลีฟรี 1 แก้ว</small>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('customer.seat-map') }}" class="btn btn-danger btn-sm px-4 fw-bold rounded-pill">
                        จองที่นั่งในร้าน <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Session Section -->
        <div class="card bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm">
            <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom border-secondary-subtle pb-3 mb-4 gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="bg-danger rounded" style="width: 4px; height: 20px;"></span>
                    <h3 class="h6 fw-bold text-white m-0">สถานะการใช้งานเครื่องคอมพิวเตอร์ปัจจุบัน</h3>
                </div>
                @if ($activeSession)
                    <span class="badge bg-success-subtle text-success border border-success-subtle d-flex align-items-center gap-1.5 py-1 px-2.5">
                        <span class="spinner-grow spinner-grow-sm text-success" style="width: 8px; height: 8px;"></span>
                        กำลังใช้งาน (ACTIVE)
                    </span>
                @endif
            </div>

            @if ($activeSession)
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3">
                            <small class="text-secondary d-block" style="font-size: 11px;">ที่นั่งคอมพิวเตอร์</small>
                            <div class="fs-4 fw-black text-white">{{ $activeSession->seat->seat_number }}</div>
                            <small class="text-danger fw-semibold">{{ $activeSession->seat->zone->name }}</small>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3">
                            <small class="text-secondary d-block" style="font-size: 11px;">เวลาที่เริ่มเล่น</small>
                            <div class="fs-4 fw-black text-white font-monospace">
                                {{ \Illuminate\Support\Carbon::parse($activeSession->start_time)->format('H:i:s') }}
                            </div>
                            <small class="text-secondary">เล่นไปแล้ว: <strong class="text-white">{{ $elapsedMinutes }}</strong> นาที</small>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3">
                            <small class="text-secondary d-block" style="font-size: 11px;">โหมดการคิดเงิน</small>
                            @if ($activeSession->userPackage)
                                <div class="fs-6 fw-bold text-danger text-truncate">{{ $activeSession->userPackage->package->name }}</div>
                                <small class="text-secondary">เหลือ: {{ $activeSession->userPackage->remaining_minutes }} นาที</small>
                            @else
                                <div class="fs-6 fw-bold text-warning">Pay-as-you-go</div>
                                <small class="text-secondary">฿{{ number_format($activeSession->rate_snapshot, 2) }} / ชม.</small>
                            @endif
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3">
                            <small class="text-secondary d-block" style="font-size: 11px;">ค่าบริการขณะนี้ (ประเมิน)</small>
                            <div class="fs-4 fw-black text-success font-monospace">
                                ฿{{ number_format($estimatedCost, 2) }}
                            </div>
                            <small class="text-secondary" style="font-size: 10px;">ตัดยอดเมื่อ Check-out</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <form method="POST" action="{{ route('customer.check-out') }}" onsubmit="return confirm('คุณต้องการเช็คเอาท์และปิดเซสชันการเล่นหรือไม่?');" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold rounded-pill">
                            🚪 ออกจากเครื่อง (Check-out)
                        </button>
                    </form>

                    <a href="{{ route('customer.food-order') }}?seat_id={{ $activeSession->seat_id }}" class="btn btn-outline-secondary text-light btn-sm px-4 fw-semibold rounded-pill">
                        🍜 สั่งอาหารส่งมาที่เครื่อง {{ $activeSession->seat->seat_number }}
                    </a>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="display-6 mb-2">🖥️</div>
                    <p class="text-secondary small mb-3">คุณยังไม่ได้เปิดใช้งานเครื่องคอมพิวเตอร์ในขณะนี้</p>
                    <a href="{{ route('customer.seat-map') }}" class="btn btn-danger btn-sm px-4 fw-bold rounded-pill">
                        เลือกที่นั่งในร้านเพื่อเริ่มต้นเล่น (Check-in)
                    </a>
                </div>
            @endif
        </div>

        <!-- Active Packages List -->
        @if ($userPackages->isNotEmpty())
            <div class="card bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="bg-danger rounded" style="width: 4px; height: 20px;"></span>
                    <h3 class="h6 fw-bold text-white m-0">แพ็กเกจชั่วโมงที่คุณถืออยู่</h3>
                </div>
                <div class="row g-3">
                    @foreach ($userPackages as $upkg)
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-white small">{{ $upkg->package->name }}</div>
                                    <small class="text-secondary" style="font-size: 11px;">ซื้อเมื่อ: {{ $upkg->purchased_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <span class="badge bg-danger-subtle text-danger font-monospace">
                                    {{ $upkg->remaining_minutes }} นาที
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tables: Recent Transactions and Recent Orders -->
        <div class="row g-4">
            <!-- Wallet History -->
            <div class="col-12 col-lg-6">
                <div class="card h-100 bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="bg-danger rounded" style="width: 4px; height: 20px;"></span>
                        <h3 class="h6 fw-bold text-white m-0">ประวัติเงินในกระเป๋าล่าสุด</h3>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover table-sm align-middle small m-0">
                            <thead class="table-active text-secondary text-uppercase" style="font-size: 11px;">
                                <tr>
                                    <th class="py-2.5 px-3">ประเภท</th>
                                    <th class="py-2.5 px-3">จำนวน</th>
                                    <th class="py-2.5 px-3">เวลา</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse ($transactions as $tx)
                                    <tr>
                                        <td class="py-2.5 px-3">
                                            @if ($tx->type === 'topup')
                                                <span class="badge bg-danger-subtle text-danger" style="font-size: 10px;">เติมเงิน ({{ $tx->ref_type }})</span>
                                            @elseif ($tx->type === 'deduct')
                                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">หักเงิน ({{ $tx->ref_type }})</span>
                                            @else
                                                <span class="badge bg-info-subtle text-info" style="font-size: 10px;">คืนเงิน</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 fw-bold font-monospace {{ $tx->type === 'topup' ? 'text-success' : 'text-light' }}">
                                            {{ $tx->type === 'topup' ? '+' : '-' }}฿{{ number_format($tx->amount, 2) }}
                                        </td>
                                        <td class="py-2.5 px-3 text-secondary font-monospace" style="font-size: 11px;">
                                            {{ $tx->created_at->format('d/m H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-secondary small">ไม่มีรายการธุรกรรม</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Food Orders -->
            <div class="col-12 col-lg-6">
                <div class="card h-100 bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="bg-danger rounded" style="width: 4px; height: 20px;"></span>
                        <h3 class="h6 fw-bold text-white m-0">คำสั่งซื้ออาหารล่าสุด</h3>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover table-sm align-middle small m-0">
                            <thead class="table-active text-secondary text-uppercase" style="font-size: 11px;">
                                <tr>
                                    <th class="py-2.5 px-3">บิล / โต๊ะ</th>
                                    <th class="py-2.5 px-3">ยอดรวม</th>
                                    <th class="py-2.5 px-3">สถานะอาหาร</th>
                                    <th class="py-2.5 px-3">การชำระ</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse ($recentOrders as $ord)
                                    <tr>
                                        <td class="py-2.5 px-3">
                                            <span class="fw-bold text-white">#{{ $ord->id }}</span>
                                            <small class="text-danger d-block fw-semibold">{{ $ord->seat->seat_number }}</small>
                                        </td>
                                        <td class="py-2.5 px-3 fw-bold text-white font-monospace">
                                            ฿{{ number_format($ord->total_amount, 2) }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <span class="badge
                                                @if($ord->order_status === 'served') bg-success-subtle text-success border border-success-subtle
                                                @elseif($ord->order_status === 'preparing') bg-info-subtle text-info border border-info-subtle
                                                @elseif($ord->order_status === 'cancelled') bg-danger-subtle text-danger border border-danger-subtle
                                                @else bg-warning-subtle text-warning border border-warning-subtle
                                                @endif
                                            " style="font-size: 10px;">
                                                {{ $ord->order_status }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <span class="badge {{ $ord->payment_status === 'paid' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }}" style="font-size: 10px;">
                                                {{ $ord->payment_status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-secondary small">ยังไม่มีคำสั่งซื้ออาหาร</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
            class="position-fixed bottom-0 end-0 m-4 z-3"
            style="user-select: none;"
        >
            <!-- Minimized Pill -->
            <div
                x-show="minimized"
                x-cloak
                @click="minimized = false"
                class="badge bg-dark border border-danger-subtle rounded-pill py-2.5 px-3.5 shadow-lg d-flex align-items-center gap-2 cursor-pointer hover-border-danger"
            >
                <span class="spinner-grow spinner-grow-sm text-success" style="width: 8px; height: 8px;"></span>
                <span class="font-monospace text-white fw-bold">เครื่อง {{ $activeSession->seat->seat_number }}</span>
                <span class="text-secondary">|</span>
                @if ($sessionRemainingMinutes !== null)
                    <span class="text-warning font-monospace fw-bold" x-text="'⏳ ' + formatTime(remainingSeconds)"></span>
                @else
                    <span class="text-light font-monospace fw-bold" x-text="'⏱️ ' + formatTime(elapsedSeconds)"></span>
                @endif
                <span class="text-secondary">|</span>
                <span class="text-success font-monospace fw-bold">฿{{ number_format($estimatedCost, 2) }}</span>
                <span class="badge bg-secondary-subtle text-secondary rounded-circle ms-1">+</span>
            </div>

            <!-- Expanded Card -->
            <div
                x-show="!minimized"
                x-cloak
                class="card bg-dark border-secondary-subtle rounded-4 p-3 shadow-lg"
                style="width: 320px;"
            >
                <div class="d-flex justify-content-between align-items-center border-bottom border-secondary-subtle pb-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success rounded-circle p-1"></span>
                        <span class="text-danger fw-bold small" style="font-size: 11px;">LETSPLAY CLIENT HUD</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger-subtle text-danger" style="font-size: 10px;">
                            {{ $activeSession->seat->seat_number }} ({{ $activeSession->seat->zone->name }})
                        </span>
                        <button type="button" @click="minimized = true" class="btn btn-sm btn-link text-secondary p-0 text-decoration-none">
                            _
                        </button>
                    </div>
                </div>

                <div class="row g-2 mb-2 text-center">
                    <div class="col-6">
                        <div class="p-2 bg-dark-subtle border border-secondary-subtle rounded-3">
                            <small class="text-secondary d-block" style="font-size: 10px;">
                                {{ $sessionRemainingMinutes !== null ? 'เวลาคงเหลือ' : 'เวลาเล่นไปแล้ว' }}
                            </small>
                            <span class="fw-bold font-monospace fs-6 {{ $sessionRemainingMinutes !== null && $sessionRemainingMinutes <= 15 ? 'text-danger' : 'text-warning' }}">
                                @if ($sessionRemainingMinutes !== null)
                                    <span x-text="formatTime(remainingSeconds)"></span>
                                @else
                                    <span x-text="formatTime(elapsedSeconds)"></span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-dark-subtle border border-secondary-subtle rounded-3">
                            <small class="text-secondary d-block" style="font-size: 10px;">ค่าบริการขณะนี้</small>
                            <span class="fw-bold font-monospace text-success fs-6">
                                ฿{{ number_format($estimatedCost, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row g-1 pt-1">
                    <div class="col-4">
                        <a href="{{ route('customer.food-order') }}?seat_id={{ $activeSession->seat_id }}" class="btn btn-outline-secondary text-light btn-sm w-100 py-1" style="font-size: 10px;">
                            🍜 อาหาร
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('customer.topup') }}" class="btn btn-outline-secondary text-light btn-sm w-100 py-1" style="font-size: 10px;">
                            💳 เติมเงิน
                        </a>
                    </div>
                    <div class="col-4">
                        <form method="POST" action="{{ route('customer.check-out') }}" onsubmit="return confirm('คุณต้องการเช็คเอาท์และปิดเครื่องหรือไม่?');" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm w-100 py-1 fw-bold" style="font-size: 10px;">
                                🚪 ออก
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-layouts::app>
