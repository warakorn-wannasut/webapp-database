<x-layouts::app :title="__('Dashboard')">
    <div class="d-flex flex-column gap-4">
        <!-- 1. Marquee Bar: ข่าวสารและประกาศของทางร้าน -->
        <div class="card bg-dark border-danger-subtle rounded-3 py-2 px-3 shadow-sm">
            <div class="d-flex align-items-center gap-3 overflow-hidden">
                <div class="badge bg-danger-subtle text-danger border border-danger-subtle py-1.5 px-3 d-flex align-items-center gap-1.5 flex-shrink-0">
                    <span class="spinner-grow spinner-grow-sm text-danger" style="width: 8px; height: 8px;"></span>
                    <i class="bi bi-megaphone"></i>
                    <span class="fw-bold">ประกาศร้าน</span>
                </div>
                <div class="overflow-hidden position-relative flex-grow-1">
                    <div class="animate-marquee d-flex align-items-center small text-light gap-4">
                        <span><strong class="text-warning"><i class="bi bi-moon-stars me-1"></i> โปรเหมาดึก:</strong> 23:00 - 06:00 น. ซื้อแพ็กเกจกลางคืนได้ที่หน้าเติมเงิน</span>
                        <span class="text-secondary">•</span>
                        <span><strong class="text-danger"><i class="bi bi-trophy me-1"></i> กิจกรรมประจำสัปดาห์:</strong> สอบถามตารางแข่งขันเกมได้ที่เคาน์เตอร์บริการ</span>
                        <span class="text-secondary">•</span>
                        <span><strong class="text-success"><i class="bi bi-cup-hot me-1"></i> ครัวเปิดบริการ:</strong> เมนูอาหารจานเดียวและเครื่องดื่มพร้อมส่งถึงโต๊ะตลอดช่วงเวลาทำการ</span>
                        <span class="text-secondary">•</span>
                        <span><strong class="text-info"><i class="bi bi-arrow-repeat me-1"></i> อัปเดตแพตช์เกม:</strong> เครื่องทุกโซนอัปเดตเกมเวอร์ชันล่าสุดพร้อมเข้าเล่นทันที</span>

                        <!-- Loop duplication -->
                        <span class="text-secondary ms-4">•</span>
                        <span><strong class="text-warning"><i class="bi bi-moon-stars me-1"></i> ช่วงเวลาเหมาดึก:</strong> 23:00 - 06:00 น. ซื้อแพ็กเกจกลางคืนได้ที่หน้าเติมเงิน</span>
                        <span class="text-secondary">•</span>
                        <span><strong class="text-success"><i class="bi bi-cup-hot me-1"></i> สั่งอาหารและเครื่องดื่ม:</strong> สั่งผ่านระบบออนไลน์ส่งตรงถึงเครื่องคอมพิวเตอร์ของคุณ</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        @if (session()->has('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 border-success-subtle bg-success-subtle text-success py-3 px-4 rounded-3 m-0 shadow-sm">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 border-danger-subtle bg-danger-subtle text-danger py-3 px-4 rounded-3 m-0 shadow-sm">
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
                            เวลาใช้งานของคุณใกล้จะหมดแล้ว ระบบจะตัดเวลาและปิดเครื่องเมื่อครบกำหนด กรุณาเติมเงินเข้าบัญชีหรือซื้อแพ็กเกจชั่วโมงเพื่อเล่นต่อ
                        </small>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('customer.topup') }}" class="btn btn-danger btn-sm px-3 fw-bold rounded-pill">
                        <i class="bi bi-wallet2 me-1"></i> เติมเงินต่อเวลาทันที
                    </a>
                </div>
            </div>

            <!-- Pop-up Modal แจ้งเตือน 15 นาทีสุดท้าย -->
            <div class="modal fade show d-block" id="timeExpiringModal" tabindex="-1" style="background: rgba(0,0,0,0.8); backdrop-filter: blur(4px);" x-data="{ showWarningModal: true }" x-show="showWarningModal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-danger shadow-lg p-3 text-center">
                        <div class="modal-body">
                            <div class="mx-auto rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-size: 28px;">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <span class="badge bg-danger-subtle text-danger mb-2">TIME EXPIRING SOON</span>
                            <h3 class="h5 fw-bold text-white mb-2">
                                เวลาใช้งานของคุณใกล้จะหมดแล้ว!
                            </h3>
                            <p class="small text-secondary mb-3">
                                เหลือเวลาใช้งานอีกประมาณ <span class="text-danger fw-bold fs-6">{{ \App\Models\UserPackage::formatMinutes($sessionRemainingMinutes) }}</span>
                            </p>

                            <div class="p-3 rounded-3 bg-dark-subtle border border-secondary-subtle text-start small mb-3">
                                @if ($activeSession)
                                    <div class="d-flex justify-content-between mb-1.5">
                                        <span class="text-secondary">เครื่องที่กำลังใช้งาน:</span>
                                        <span class="fw-bold text-white">เครื่อง {{ $activeSession->seat->seat_number }} ({{ $activeSession->seat->zone->name }})</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between">
                                    <span class="text-secondary">ยอดเงินในกระเป๋าคงเหลือ:</span>
                                    <span class="fw-bold font-monospace text-warning">฿{{ number_format($user->balance, 2) }}</span>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('customer.topup') }}" class="btn btn-danger btn-sm fw-bold py-2">
                                    <i class="bi bi-wallet2 me-1"></i> เติมเงินหรือซื้อแพ็กเกจชั่วโมง
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
                        <span class="badge bg-danger-subtle text-danger mb-2">
                            <i class="bi bi-person-circle me-1"></i> ข้อมูลสมาชิก
                        </span>
                        <small class="text-secondary d-block">ยินดีต้อนรับเข้าสู่ระบบ</small>
                        <h2 class="h4 fw-bold text-white mt-1 mb-0">{{ $user->name }}</h2>
                    </div>
                    <div class="pt-3 border-top border-secondary-subtle mt-3 d-flex justify-content-between align-items-center small">
                        <span class="text-secondary font-monospace">@ {{ $user->username }}</span>
                        <span class="badge bg-secondary-subtle text-light border border-secondary">
                            {{ $user->role === 'customer' ? 'สมาชิกทั่วไป' : ($user->role === 'staff' ? 'พนักงาน' : 'ผู้ดูแลระบบ') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Wallet Balance Card -->
            <div class="col-12 col-md-4">
                <div class="card h-100 bg-dark border-secondary-subtle rounded-4 p-4 d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-secondary fw-semibold">
                                <i class="bi bi-wallet2 me-1 text-warning"></i> ยอดเงินในกระเป๋า
                            </small>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle font-monospace">THB</span>
                        </div>
                        <div class="fs-2 fw-black text-warning font-monospace">
                            ฿{{ number_format($user->balance, 2) }}
                        </div>
                        @if(isset($availableBalance) && $availableBalance < (float)$user->balance)
                            <div class="pt-2">
                                <div class="d-flex justify-content-between align-items-center small text-success fw-semibold">
                                    <span>ยอดที่สามารถใช้สั่งอาหาร:</span>
                                    <span class="font-monospace">฿{{ number_format($availableBalance, 2) }}</span>
                                </div>
                                <small class="text-secondary d-block">
                                    (กันเงินไว้สำหรับค่าเครื่อง: ฿{{ number_format((float)$user->balance - $availableBalance, 2) }})
                                </small>
                            </div>
                        @else
                            <small class="text-secondary d-block mt-1">ใช้สำหรับเปิดเครื่อง ซื้อแพ็กเกจ หรือสั่งอาหาร</small>
                        @endif
                    </div>
                    <div class="pt-3 border-top border-secondary-subtle mt-3">
                        <a href="{{ route('customer.topup') }}" class="btn btn-danger btn-sm w-100 fw-bold rounded-pill">
                            <i class="bi bi-plus-circle me-1"></i> เติมเงินเข้าบัญชี
                        </a>
                    </div>
                </div>
            </div>

            <!-- Packages Card -->
            <div class="col-12 col-md-4">
                <div class="card h-100 bg-dark border-secondary-subtle rounded-4 p-4 d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-secondary fw-semibold">
                                <i class="bi bi-clock-history me-1 text-danger"></i> เวลาในแพ็กเกจรวม
                            </small>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                {{ $userPackages->count() }} แพ็กเกจ
                            </span>
                        </div>
                        <div class="fs-4 fw-black text-white font-monospace">
                            {{ \App\Models\UserPackage::formatMinutes($userPackages->sum('remaining_minutes')) }}
                        </div>
                        <small class="text-secondary d-block mt-1">เวลาในแพ็กเกจจะถูกนำมาใช้ก่อนเมื่อเปิดเครื่องตรงโซน</small>
                    </div>
                    <div class="pt-3 border-top border-secondary-subtle mt-3">
                        <a href="{{ route('customer.seat-map') }}" class="btn btn-outline-secondary text-light btn-sm w-100 fw-semibold rounded-pill">
                            <i class="bi bi-display me-1"></i> ผังที่นั่งคอมพิวเตอร์
                        </a>
                    </div>
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
                    <span class="badge bg-success-subtle text-success border border-success-subtle d-flex align-items-center gap-2 py-1.5 px-3">
                        <span class="spinner-grow spinner-grow-sm text-success" style="width: 8px; height: 8px;"></span>
                        กำลังใช้งาน
                    </span>
                @endif
            </div>

            @if ($activeSession)
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3 h-100">
                            <small class="text-secondary d-block">เครื่องคอมพิวเตอร์</small>
                            <div class="fs-4 fw-black text-white font-monospace">{{ $activeSession->seat->seat_number }}</div>
                            <small class="text-danger fw-semibold">{{ $activeSession->seat->zone->name }}</small>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3 h-100">
                            <small class="text-secondary d-block">เวลาที่เริ่มใช้งาน</small>
                            <div class="fs-4 fw-black text-white font-monospace">
                                {{ \Illuminate\Support\Carbon::parse($activeSession->start_time)->format('H:i') }} น.
                            </div>
                            <small class="text-secondary">ใช้ไปแล้ว: <strong class="text-white">{{ \App\Models\UserPackage::formatMinutes($elapsedMinutes) }}</strong></small>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3 h-100">
                            <small class="text-secondary d-block">รูปแบบค่าบริการ</small>
                            @if ($activeSession->userPackage)
                                <div class="fs-6 fw-bold text-danger text-truncate">{{ $activeSession->userPackage->package->name }}</div>
                                <small class="text-light">
                                    คงเหลือ: <strong class="text-warning">{{ \App\Models\UserPackage::formatMinutes($sessionRemainingMinutes ?? 0) }}</strong>
                                </small>
                            @else
                                <div class="fs-6 fw-bold text-warning">คิดเงินตามจริง (Pay as you go)</div>
                                <small class="text-secondary">฿{{ number_format($activeSession->rate_snapshot, 2) }} / ชั่วโมง</small>
                            @endif
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3 h-100">
                            <small class="text-secondary d-block">ค่าบริการโดยประมาณ</small>
                            <div class="fs-4 fw-black text-success font-monospace">
                                ฿{{ number_format($estimatedCost, 2) }}
                            </div>
                            <small class="text-secondary">คำนวณและตัดยอดเมื่อปิดเครื่อง</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <form method="POST" action="{{ route('customer.check-out') }}" onsubmit="return confirm('ยืนยันการปิดเครื่องและสิ้นสุดการใช้งานหรือไม่?');" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold rounded-pill">
                            <i class="bi bi-power me-1"></i> ปิดเครื่อง / สิ้นสุดการใช้งาน
                        </button>
                    </form>

                    <a href="{{ route('customer.food-order') }}?seat_id={{ $activeSession->seat_id }}" class="btn btn-outline-secondary text-light btn-sm px-4 fw-semibold rounded-pill">
                        <i class="bi bi-cup-hot me-1 text-warning"></i> สั่งอาหารส่งที่เครื่อง {{ $activeSession->seat->seat_number }}
                    </a>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="display-6 mb-2 text-secondary">
                        <i class="bi bi-pc-display"></i>
                    </div>
                    <p class="text-secondary small mb-3">ยังไม่มีเครื่องคอมพิวเตอร์ที่เปิดใช้งานอยู่ในขณะนี้</p>
                    <a href="{{ route('customer.seat-map') }}" class="btn btn-danger btn-sm px-4 fw-bold rounded-pill">
                        <i class="bi bi-display me-1"></i> เลือกที่นั่งเพื่อเปิดใช้งานเครื่อง
                    </a>
                </div>
            @endif
        </div>

        <!-- Active Packages List by Zone -->
        @if ($userPackages->isNotEmpty())
            <div class="card bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="bg-danger rounded" style="width: 4px; height: 20px;"></span>
                        <h3 class="h6 fw-bold text-white m-0">แพ็กเกจชั่วโมงของคุณที่พร้อมใช้งาน</h3>
                    </div>
                    <a href="{{ route('customer.topup') }}" class="btn btn-outline-secondary btn-sm small py-1 px-3">
                        + ซื้อแพ็กเกจเพิ่ม
                    </a>
                </div>
                <div class="row g-3">
                    @foreach ($userPackages as $upkg)
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3 d-flex flex-column justify-content-between h-100">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold text-white">{{ $upkg->package->name }}</span>
                                        @if ($upkg->package->zone)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                {{ $upkg->package->zone->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                ทุกโซน
                                            </span>
                                        @endif
                                    </div>
                                    <small class="text-secondary d-block">
                                        ซื้อเมื่อ: {{ $upkg->purchased_at->format('d/m/Y H:i') }} น.
                                    </small>
                                </div>
                                <div class="pt-2 border-top border-secondary-subtle mt-2 d-flex justify-content-between align-items-center">
                                    <span class="small text-secondary">คงเหลือ:</span>
                                    <span class="badge bg-danger text-white font-monospace fs-7 py-1 px-2.5">
                                        <i class="bi bi-hourglass-split me-1"></i> {{ $upkg->formattedRemainingTime() }}
                                    </span>
                                </div>
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
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="bg-danger rounded" style="width: 4px; height: 20px;"></span>
                            <h3 class="h6 fw-bold text-white m-0">ประวัติการทำรายการกระเป๋าเงิน</h3>
                        </div>
                        <a href="{{ route('customer.topup') }}" class="small text-secondary text-decoration-none">
                            ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover table-sm align-middle m-0">
                            <thead class="table-active text-secondary text-uppercase small">
                                <tr>
                                    <th class="py-2.5 px-3">ประเภทรายการ</th>
                                    <th class="py-2.5 px-3">จำนวนเงิน</th>
                                    <th class="py-2.5 px-3">วันและเวลา</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse ($transactions as $tx)
                                    <tr>
                                        <td class="py-2.5 px-3">
                                            @if ($tx->type === 'topup')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    เติมเงิน ({{ $tx->ref_type === 'qr_topup' ? 'พร้อมเพย์' : ($tx->ref_type === 'cash_topup' ? 'เงินสด' : $tx->ref_type) }})
                                                </span>
                                            @elseif ($tx->type === 'deduct')
                                                <span class="badge bg-secondary-subtle text-light border border-secondary">
                                                    หักค่าบริการ ({{ $tx->ref_type === 'package_purchase' ? 'ซื้อแพ็กเกจ' : ($tx->ref_type === 'session' ? 'ค่าเครื่อง' : ($tx->ref_type === 'order' ? 'สั่งอาหาร' : $tx->ref_type)) }})
                                                </span>
                                            @else
                                                <span class="badge bg-info-subtle text-info border border-info-subtle">คืนเงิน</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 fw-bold font-monospace {{ $tx->type === 'topup' ? 'text-success' : 'text-light' }}">
                                            {{ $tx->type === 'topup' ? '+' : '-' }}฿{{ number_format($tx->amount, 2) }}
                                        </td>
                                        <td class="py-2.5 px-3 text-secondary font-monospace small">
                                            {{ $tx->created_at->format('d/m/Y H:i') }} น.
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-secondary small">ไม่มีประวัติการทำรายการ</td>
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
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="bg-danger rounded" style="width: 4px; height: 20px;"></span>
                            <h3 class="h6 fw-bold text-white m-0">รายการสั่งอาหารและเครื่องดื่ม</h3>
                        </div>
                        <a href="{{ route('customer.food-order') }}" class="small text-secondary text-decoration-none">
                            สั่งอาหารเพิ่ม <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover table-sm align-middle m-0">
                            <thead class="table-active text-secondary text-uppercase small">
                                <tr>
                                    <th class="py-2.5 px-3">เลขออเดอร์</th>
                                    <th class="py-2.5 px-3">ยอดรวม</th>
                                    <th class="py-2.5 px-3">สถานะอาหาร</th>
                                    <th class="py-2.5 px-3">การชำระเงิน</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse ($recentOrders as $ord)
                                    <tr>
                                        <td class="py-2.5 px-3">
                                            <span class="fw-bold text-white">#{{ $ord->id }}</span>
                                            <small class="text-danger d-block fw-semibold">เครื่อง {{ $ord->seat->seat_number }}</small>
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
                                            ">
                                                {{ $ord->order_status === 'served' ? 'เสิร์ฟแล้ว' : ($ord->order_status === 'preparing' ? 'กำลังทำ' : ($ord->order_status === 'cancelled' ? 'ยกเลิก' : 'รอดำเนินการ')) }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <span class="badge {{ $ord->payment_status === 'paid' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }}">
                                                {{ $ord->payment_status === 'paid' ? 'ชำระแล้ว' : 'รอชำระ' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-secondary small">ยังไม่มีรายการสั่งอาหาร</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Floating Status Bar (วิดเจ็ตแสดงสถานะเครื่องมุมจอ) -->
    @if ($activeSession)
        <div
            x-data="{
                minimized: false,
                elapsedSeconds: {{ $elapsedMinutes * 60 }},
                remainingSeconds: {{ $sessionRemainingMinutes !== null ? $sessionRemainingMinutes * 60 : 'null' }},
                formatDHMS(totalSecs) {
                    if (totalSecs === null || totalSecs < 0) return '0 นาที';
                    const totalMins = Math.floor(totalSecs / 60);
                    const days = Math.floor(totalMins / 1440);
                    const hours = Math.floor((totalMins % 1440) / 60);
                    const mins = totalMins % 60;

                    let parts = [];
                    if (days > 0) parts.push(days + ' วัน');
                    if (hours > 0) parts.push(hours + ' ชม.');
                    parts.push(mins + ' นาที');
                    return parts.join(' ');
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
                    <span class="text-warning font-monospace fw-bold" x-text="'เหลือ ' + formatDHMS(remainingSeconds)"></span>
                @else
                    <span class="text-light font-monospace fw-bold" x-text="'เล่นไป ' + formatDHMS(elapsedSeconds)"></span>
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
                style="width: 330px;"
            >
                <div class="d-flex justify-content-between align-items-center border-bottom border-secondary-subtle pb-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success rounded-circle p-1"></span>
                        <span class="text-danger fw-bold small">LETSPLAY CLIENT HUD</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger-subtle text-danger fs-7">
                            {{ $activeSession->seat->seat_number }} ({{ $activeSession->seat->zone->name }})
                        </span>
                        <button type="button" @click="minimized = true" class="btn btn-sm btn-link text-secondary p-0 text-decoration-none">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="row g-2 mb-2 text-center">
                    <div class="col-6">
                        <div class="p-2 bg-dark-subtle border border-secondary-subtle rounded-3">
                            <small class="text-secondary d-block">
                                {{ $sessionRemainingMinutes !== null ? 'เวลาคงเหลือ' : 'เวลาที่เล่นไปแล้ว' }}
                            </small>
                            <span class="fw-bold font-monospace fs-7 {{ $sessionRemainingMinutes !== null && $sessionRemainingMinutes <= 15 ? 'text-danger' : 'text-warning' }}">
                                @if ($sessionRemainingMinutes !== null)
                                    <span x-text="formatDHMS(remainingSeconds)"></span>
                                @else
                                    <span x-text="formatDHMS(elapsedSeconds)"></span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-dark-subtle border border-secondary-subtle rounded-3">
                            <small class="text-secondary d-block">ค่าบริการขณะนี้</small>
                            <span class="fw-bold font-monospace text-success fs-6">
                                ฿{{ number_format($estimatedCost, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row g-1 pt-1">
                    <div class="col-4">
                        <a href="{{ route('customer.food-order') }}?seat_id={{ $activeSession->seat_id }}" class="btn btn-outline-secondary text-light btn-sm w-100 py-1">
                            <i class="bi bi-cup-hot me-1"></i> สั่งอาหาร
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('customer.topup') }}" class="btn btn-outline-secondary text-light btn-sm w-100 py-1">
                            <i class="bi bi-wallet2 me-1"></i> เติมเงิน
                        </a>
                    </div>
                    <div class="col-4">
                        <form method="POST" action="{{ route('customer.check-out') }}" onsubmit="return confirm('ยืนยันการปิดเครื่องและสิ้นสุดการใช้งานหรือไม่?');" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm w-100 py-1 fw-bold">
                                <i class="bi bi-power me-1"></i> เช็คเอาท์
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-layouts::app>
