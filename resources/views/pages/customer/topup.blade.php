<x-layouts::app :title="__('Wallet & Packages')">
    <div class="d-flex flex-column gap-4">
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

        <div class="row g-4">
            <!-- Section 1: Wallet Top-up -->
            <div class="col-12 col-xl-5">
                <div class="card bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm h-100">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary-subtle pb-3 mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="bg-danger rounded" style="width: 4px; height: 22px;"></span>
                            <div>
                                <h2 class="h5 fw-bold text-white m-0">เติมเงินเข้าบัญชี (Top-up)</h2>
                                <small class="text-secondary">เติมเงินเพื่อใช้เล่นเกม ซื้อแพ็กเกจ หรือสั่งอาหาร</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-secondary d-block fw-semibold">ยอดเงินปัจจุบัน</small>
                            <span class="fs-5 fw-bold text-warning font-monospace">฿{{ number_format($user->balance, 2) }}</span>
                        </div>
                    </div>

                    <form id="topupForm" method="POST" action="{{ route('customer.do-topup') }}" class="d-flex flex-column gap-3">
                        @csrf

                        <!-- Amount presets -->
                        <div>
                            <label class="form-label text-secondary text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                                เลือกจำนวนเงิน:
                            </label>
                            <div class="row g-2">
                                @php
                                    $presets = [50, 100, 200, 300, 500, 1000];
                                @endphp
                                @foreach ($presets as $amt)
                                    <div class="col-4">
                                        <button
                                            type="button"
                                            onclick="setAmount({{ $amt }})"
                                            class="btn preset-btn w-100 py-2.5 rounded-3 border border-secondary-subtle bg-dark-subtle text-white text-center transition {{ $amt === 100 ? 'border-danger bg-danger-subtle' : '' }}"
                                            id="preset-{{ $amt }}"
                                        >
                                            <div class="fs-5 fw-bold text-white font-monospace">
                                                ฿{{ $amt }}
                                            </div>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Custom amount input -->
                        <div>
                            <label for="inputAmount" class="form-label text-secondary small fw-semibold">หรือระบุจำนวนเงินเอง (บาท):</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark-subtle border-secondary-subtle text-secondary font-monospace">฿</span>
                                <input
                                    type="number"
                                    name="amount"
                                    id="inputAmount"
                                    min="1"
                                    step="1"
                                    value="100"
                                    required
                                    placeholder="ระบุจำนวนเงิน..."
                                    class="form-control bg-dark-subtle border-secondary-subtle text-white fw-bold font-monospace py-2"
                                />
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="form-label text-secondary small fw-semibold">ช่องทางการชำระเงิน:</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label id="methodLabelQr" class="form-check p-3 rounded-3 border border-danger bg-danger-subtle d-flex align-items-center gap-2 cursor-pointer transition">
                                        <input type="radio" name="topup_method" value="qr" checked onchange="handleMethodChange(this.value)" class="form-check-input mt-0">
                                        <div class="lh-sm">
                                            <span class="fw-bold text-white d-block small">
                                                <i class="bi bi-qr-code-scan me-1 text-danger"></i> พร้อมเพย์ QR
                                            </span>
                                            <small class="text-secondary">อัปเดตยอดทันที</small>
                                        </div>
                                    </label>
                                </div>

                                <div class="col-6">
                                    <label id="methodLabelCash" class="form-check p-3 rounded-3 border border-secondary-subtle bg-dark-subtle d-flex align-items-center gap-2 cursor-pointer transition">
                                        <input type="radio" name="topup_method" value="cash" onchange="handleMethodChange(this.value)" class="form-check-input mt-0">
                                        <div class="lh-sm">
                                            <span class="fw-bold text-white d-block small">
                                                <i class="bi bi-cash-stack me-1 text-secondary"></i> เงินสดเคาน์เตอร์
                                            </span>
                                            <small class="text-secondary">ติดต่อพนักงาน</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            id="submitTopupBtn"
                            onclick="handleTopupSubmit()"
                            class="btn btn-danger w-100 py-2.5 fw-bold mt-2 shadow-sm d-flex align-items-center justify-content-center gap-2"
                        >
                            <i class="bi bi-check-circle"></i> ยืนยันการทำรายการเติมเงิน
                        </button>
                    </form>
                </div>
            </div>

            <!-- Section 2: Buy Zone-Specific Time Packages -->
            <div class="col-12 col-xl-7">
                <div class="card bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="border-bottom border-secondary-subtle pb-3 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="bg-danger rounded" style="width: 4px; height: 22px;"></span>
                                <div>
                                    <h2 class="h5 fw-bold text-white m-0">ซื้อแพ็กเกจชั่วโมงตามโซน</h2>
                                    <small class="text-secondary">แต่ละโซนมีอัตราค่าบริการต่างกัน การซื้อแพ็กเกจจะประหยัดกว่าการเล่นแบบคิดตามจริง</small>
                                </div>
                            </div>
                        </div>

                        <!-- Zone Tabs for Packages -->
                        <ul class="nav nav-pills mb-3 gap-2" id="packageZoneTabs" role="tablist">
                            @foreach ($zones as $idx => $z)
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link py-1.5 px-3 rounded-3 small fw-semibold {{ $idx === 0 ? 'active btn-danger' : 'text-secondary border border-secondary-subtle bg-dark-subtle' }}"
                                        id="zone-tab-{{ $z->id }}"
                                        data-bs-toggle="pill"
                                        data-bs-target="#zone-pane-{{ $z->id }}"
                                        type="button"
                                        role="tab"
                                        aria-controls="zone-pane-{{ $z->id }}"
                                        aria-selected="{{ $idx === 0 ? 'true' : 'false' }}"
                                    >
                                        <i class="bi bi-display me-1"></i> {{ $z->name }}
                                        <span class="badge bg-dark border border-secondary text-light ms-1 font-monospace">฿{{ number_format($z->hourly_rate, 0) }}/ชม.</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Zone Tab Contents -->
                        <div class="tab-content mb-4" id="packageZoneTabContent">
                            @foreach ($zones as $idx => $z)
                                <div
                                    class="tab-pane fade {{ $idx === 0 ? 'show active' : '' }}"
                                    id="zone-pane-{{ $z->id }}"
                                    role="tabpanel"
                                    aria-labelledby="zone-tab-{{ $z->id }}"
                                    tabindex="0"
                                >
                                    <div class="p-2.5 mb-3 rounded-3 bg-dark-subtle border border-secondary-subtle d-flex justify-content-between align-items-center">
                                        <div class="small text-secondary">
                                            <span class="text-light fw-bold">{{ $z->name }}</span>
                                            <div>{{ $z->description ?? 'คอมพิวเตอร์พร้อมอุปกรณ์เกมมิ่งครบชุด' }}</div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-secondary-subtle text-light border border-secondary">
                                                ปกติ ฿{{ number_format($z->hourly_rate, 2) }}/ชั่วโมง
                                            </span>
                                        </div>
                                    </div>

                                    @php
                                        $zonePackages = $packages->where('zone_id', $z->id);
                                    @endphp

                                    @if ($zonePackages->isEmpty())
                                        <div class="p-4 text-center text-secondary rounded-3 border border-secondary-subtle">
                                            ยังไม่มีรายการแพ็กเกจสำหรับโซนนี้
                                        </div>
                                    @else
                                        <div class="d-flex flex-column gap-2.5">
                                            @foreach ($zonePackages as $pkg)
                                                @php
                                                    $normalCost = (float) $z->hourly_rate * $pkg->duration_hours;
                                                    $savings = max(0, $normalCost - (float) $pkg->price);
                                                @endphp
                                                <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 transition">
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="fw-bold text-white">{{ $pkg->name }}</span>
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                                <i class="bi bi-clock me-1"></i> {{ $pkg->duration_hours }} ชั่วโมง
                                                            </span>
                                                        </div>
                                                        <div class="small text-secondary mt-1">
                                                            <span>ได้รับเวลาเล่น {{ $pkg->duration_hours * 60 }} นาที</span>
                                                            @if ($savings > 0)
                                                                <span class="text-success fw-semibold ms-2">
                                                                    (ประหยัด ฿{{ number_format($savings, 2) }} เทียบกับจ่ายรายชั่วโมง)
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-3 flex-shrink-0">
                                                        <div class="text-end">
                                                            <span class="fs-5 fw-bold text-white font-monospace">฿{{ number_format($pkg->price, 2) }}</span>
                                                            @if ($savings > 0)
                                                                <div class="small text-secondary text-decoration-line-through font-monospace" style="font-size: 11px;">
                                                                    ฿{{ number_format($normalCost, 2) }}
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <form method="POST" action="{{ route('customer.buy-package') }}" onsubmit="return confirm('ยืนยันการซื้อแพ็กเกจ {{ addslashes($pkg->name) }} ในราคา ฿{{ number_format($pkg->price, 2) }} หรือไม่?');" class="m-0">
                                                            @csrf
                                                            <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                                            <button
                                                                type="submit"
                                                                @disabled($user->balance < $pkg->price)
                                                                class="btn btn-danger btn-sm px-3 fw-bold rounded-pill"
                                                                title="{{ $user->balance < $pkg->price ? 'ยอดเงินในกระเป๋าไม่เพียงพอ' : 'ซื้อแพ็กเกจนี้' }}"
                                                            >
                                                                <i class="bi bi-cart-plus me-1"></i> ซื้อแพ็กเกจ
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- User Active Packages list with Days/Hours/Minutes -->
                    @if ($userPackages->isNotEmpty())
                        <div class="pt-3 border-top border-secondary-subtle">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-secondary text-uppercase fw-bold small">
                                    <i class="bi bi-collection me-1 text-danger"></i> แพ็กเกจของคุณที่พร้อมใช้งาน:
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $userPackages->count() }} รายการ</span>
                            </div>

                            <div class="d-flex flex-column gap-2">
                                @foreach ($userPackages as $mp)
                                    <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-white">{{ $mp->package->name }}</span>
                                                @if ($mp->package->zone)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                        {{ $mp->package->zone->name }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                        ทุกโซน
                                                    </span>
                                                @endif
                                            </div>
                                            <small class="text-secondary d-block mt-1">
                                                ซื้อเมื่อ {{ $mp->purchased_at ? $mp->purchased_at->format('d/m/Y H:i') : '-' }}
                                                @if ($mp->expired_at)
                                                    • หมดอายุ {{ $mp->expired_at->format('d/m/Y') }}
                                                @endif
                                            </small>
                                        </div>

                                        <div class="text-end">
                                            <span class="badge bg-danger text-white fs-7 font-monospace px-2.5 py-1.5">
                                                <i class="bi bi-hourglass-split me-1"></i> เหลือ {{ $mp->formattedRemainingTime() }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section 3: Wallet Transactions History -->
        @if ($transactions->isNotEmpty())
            <div class="card bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="bg-danger rounded" style="width: 4px; height: 20px;"></span>
                    <h3 class="h6 fw-bold text-white m-0">
                        <i class="bi bi-clock-history me-1 text-secondary"></i> ประวัติการทำรายการล่าสุด (10 รายการ)
                    </h3>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover table-sm align-middle m-0">
                        <thead class="table-active text-secondary text-uppercase small">
                            <tr>
                                <th class="py-2.5 px-3">ประเภทรายการ</th>
                                <th class="py-2.5 px-3">จำนวนเงิน</th>
                                <th class="py-2.5 px-3">วันและเวลาที่ทำรายการ</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach ($transactions as $tx)
                                <tr>
                                    <td class="py-2.5 px-3">
                                        @if ($tx->type === 'topup')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-plus-circle me-1"></i> เติมเงิน ({{ $tx->ref_type === 'qr_topup' ? 'พร้อมเพย์' : ($tx->ref_type === 'cash_topup' ? 'เงินสด' : $tx->ref_type) }})
                                            </span>
                                        @elseif ($tx->type === 'deduct')
                                            <span class="badge bg-secondary-subtle text-light border border-secondary">
                                                <i class="bi bi-dash-circle me-1"></i> หักค่าบริการ ({{ $tx->ref_type === 'package_purchase' ? 'ซื้อแพ็กเกจ' : ($tx->ref_type === 'session' ? 'ค่าเครื่อง' : ($tx->ref_type === 'order' ? 'สั่งอาหาร' : $tx->ref_type)) }})
                                            </span>
                                        @else
                                            <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> คืนเงิน
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 fw-bold font-monospace {{ $tx->type === 'topup' ? 'text-success' : 'text-light' }}">
                                        {{ $tx->type === 'topup' ? '+' : '-' }}฿{{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-secondary font-monospace small">
                                        {{ $tx->created_at->format('d/m/Y H:i') }} น.
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Bootstrap Modal จำลองการสแกน QR Code PromptPay -->
        <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
                <div class="modal-content bg-dark border border-danger-subtle rounded-4 p-4 text-center shadow-lg">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary-subtle pb-3 mb-3">
                        <div class="d-flex align-items-center gap-2" id="qrModalLabel">
                            <span class="badge bg-danger rounded-circle p-1"></span>
                            <span class="fw-bold text-white small">ชำระเงินผ่าน PromptPay QR</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="mb-3">
                        <small class="text-secondary d-block">ยอดชำระที่ต้องสแกน</small>
                        <div class="fs-3 fw-black text-warning font-monospace" id="modalQrAmount">฿100.00</div>
                    </div>

                    <!-- จำลอง QR Code Card PromptPay (สไตล์ทางการ) -->
                    <div class="bg-white rounded-4 mx-auto overflow-hidden shadow mb-3" style="width: 240px;">
                        <!-- PromptPay Header Bar -->
                        <div style="background-color: #003d6b; padding: 0.625rem 0.5rem; text-align: center;">
                            <div style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; background-color: #ffffff; border-radius: 0.375rem; padding: 0.25rem 0.75rem;">
                                <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #dc2626; display: inline-block;"></span>
                                <span style="color: #003d6b; font-size: 11px; font-weight: 900; letter-spacing: 0.05em; font-family: sans-serif;">THAI QR PAYMENT</span>
                            </div>
                        </div>

                        <!-- QR Body -->
                        <div style="padding: 1rem; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #ffffff;">
                            <svg class="text-black" style="width: 160px; height: 160px;" viewBox="0 0 100 100" fill="currentColor">
                                <rect x="0" y="0" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6"/>
                                <rect x="8" y="8" width="14" height="14"/>
                                <rect x="70" y="0" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6"/>
                                <rect x="78" y="8" width="14" height="14"/>
                                <rect x="0" y="70" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6"/>
                                <rect x="8" y="78" width="14" height="14"/>
                                <rect x="36" y="8" width="8" height="8"/>
                                <rect x="48" y="16" width="12" height="6"/>
                                <rect x="36" y="26" width="6" height="10"/>
                                <rect x="46" y="38" width="8" height="8"/>
                                <rect x="10" y="44" width="10" height="6"/>
                                <rect x="24" y="40" width="8" height="16"/>
                                <rect x="38" y="60" width="12" height="12"/>
                                <rect x="60" y="40" width="10" height="8"/>
                                <rect x="74" y="36" width="16" height="6"/>
                                <rect x="84" y="48" width="10" height="14"/>
                                <rect x="60" y="60" width="16" height="8"/>
                                <rect x="80" y="74" width="14" height="14"/>
                                <rect x="64" y="84" width="10" height="10"/>
                                <rect x="40" y="82" width="14" height="6"/>
                            </svg>
                        </div>
                    </div>

                    <div class="bg-dark-subtle p-2.5 rounded-3 border border-secondary-subtle small mb-3">
                        <div class="text-secondary" style="font-size: 12px;">
                            ชื่อผู้รับ: <span class="text-light fw-bold">LETSPLAY GAMING CAFE</span><br>
                            หมายเลขอ้างอิง: <span class="text-secondary font-monospace">089-123-4567</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary w-50 small fw-bold" data-bs-dismiss="modal">
                            ยกเลิก
                        </button>
                        <button type="button" id="confirmPaymentBtn" onclick="submitTopupForm()" class="btn btn-danger w-50 small fw-bold">
                            <i class="bi bi-check2"></i> ชำระเงินเรียบร้อย
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setAmount(val) {
            document.getElementById('inputAmount').value = val;
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.classList.remove('border-danger', 'bg-danger-subtle');
                btn.classList.add('border-secondary-subtle', 'bg-dark-subtle');
            });
            const selected = document.getElementById('preset-' + val);
            if (selected) {
                selected.classList.remove('border-secondary-subtle', 'bg-dark-subtle');
                selected.classList.add('border-danger', 'bg-danger-subtle');
            }
        }

        function handleMethodChange(method) {
            const qrLabel = document.getElementById('methodLabelQr');
            const cashLabel = document.getElementById('methodLabelCash');

            if (method === 'qr') {
                qrLabel.classList.add('border-danger', 'bg-danger-subtle');
                qrLabel.classList.remove('border-secondary-subtle', 'bg-dark-subtle');
                cashLabel.classList.remove('border-danger', 'bg-danger-subtle');
                cashLabel.classList.add('border-secondary-subtle', 'bg-dark-subtle');
            } else {
                cashLabel.classList.add('border-danger', 'bg-danger-subtle');
                cashLabel.classList.remove('border-secondary-subtle', 'bg-dark-subtle');
                qrLabel.classList.remove('border-danger', 'bg-danger-subtle');
                qrLabel.classList.add('border-secondary-subtle', 'bg-dark-subtle');
            }
        }

        function handleTopupSubmit() {
            const amountInput = document.getElementById('inputAmount');
            const amount = parseFloat(amountInput.value);

            if (!amount || amount <= 0) {
                alert('กรุณาระบุจำนวนเงินที่ถูกต้อง');
                amountInput.focus();
                return;
            }

            const selectedMethod = document.querySelector('input[name="topup_method"]:checked')?.value || 'qr';

            if (selectedMethod === 'qr') {
                document.getElementById('modalQrAmount').textContent = '฿' + amount.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const modalEl = document.getElementById('qrModal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            } else {
                submitTopupForm();
            }
        }

        function submitTopupForm() {
            const confirmBtn = document.getElementById('confirmPaymentBtn');
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.innerText = 'กำลังดำเนินการ...';
            }
            document.getElementById('topupForm').submit();
        }
    </script>
</x-layouts::app>
