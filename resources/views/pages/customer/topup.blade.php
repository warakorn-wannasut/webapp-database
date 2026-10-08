<x-layouts::app :title="__('Wallet & Packages')">
    <div class="d-flex flex-column gap-4">
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

        <div class="row g-4">
            <!-- Section 1: Wallet Top-up -->
            <div class="col-12 col-lg-6">
                <div class="card bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm h-100">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary-subtle pb-3 mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="bg-danger rounded" style="width: 4px; height: 22px;"></span>
                            <div>
                                <h2 class="h5 fw-bold text-white m-0">เติมเงินเข้า Wallet (Top-up)</h2>
                                <small class="text-secondary">ระบบเติมเงินอัตโนมัติ เครดิตเข้าทันที</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-secondary d-block fw-semibold" style="font-size: 11px;">คงเหลือในกระเป๋า</small>
                            <span class="fs-5 fw-bold text-warning font-monospace">฿{{ number_format($user->balance, 2) }}</span>
                        </div>
                    </div>

                    <form id="topupForm" method="POST" action="{{ route('customer.do-topup') }}" class="d-flex flex-column gap-3">
                        @csrf

                        <!-- Amount presets -->
                        <div>
                            <label class="form-label text-secondary text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                                เลือกจำนวนเงินที่ต้องการเติม:
                            </label>
                            <div class="row g-2">
                                @php
                                    $presets = [
                                        ['amt' => 50, 'badge' => 'เริ่มต้น'],
                                        ['amt' => 100, 'badge' => 'HOT'],
                                        ['amt' => 200, 'badge' => 'ยอดนิยม'],
                                        ['amt' => 300, 'badge' => 'สุดคุ้ม'],
                                        ['amt' => 500, 'badge' => '+โบนัส 5%'],
                                        ['amt' => 1000, 'badge' => '+โบนัส 10%'],
                                    ];
                                @endphp
                                @foreach ($presets as $p)
                                    <div class="col-4">
                                        <button
                                            type="button"
                                            onclick="setAmount({{ $p['amt'] }})"
                                            class="btn preset-btn w-100 p-2.5 rounded-3 border border-secondary-subtle bg-dark-subtle text-white text-center position-relative transition"
                                            id="preset-{{ $p['amt'] }}"
                                        >
                                            <span class="position-absolute top-0 end-0 translate-middle-y badge bg-secondary-subtle text-warning border border-secondary" style="font-size: 9px; right: 8px;">
                                                {{ $p['badge'] }}
                                            </span>
                                            <div class="fs-5 fw-bold text-white font-monospace mt-1">
                                                ฿{{ $p['amt'] }}
                                            </div>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Custom amount input -->
                        <div>
                            <label for="inputAmount" class="form-label text-secondary small fw-semibold">หรือระบุจำนวนเงินเอง (บาท):</label>
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

                        <!-- Payment Method -->
                        <div>
                            <label class="form-label text-secondary small fw-semibold">ช่องทางการชำระเงิน:</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label id="methodLabelQr" class="form-check p-3 rounded-3 border border-danger bg-danger-subtle d-flex align-items-center gap-2 cursor-pointer transition">
                                        <input type="radio" name="topup_method" value="qr" checked onchange="handleMethodChange(this.value)" class="form-check-input mt-0">
                                        <div class="lh-sm">
                                            <span class="fw-bold text-white d-block small">สแกน QR PromptPay</span>
                                            <small class="text-secondary" style="font-size: 10px;">เข้าทันที (จำลอง)</small>
                                        </div>
                                    </label>
                                </div>

                                <div class="col-6">
                                    <label id="methodLabelCash" class="form-check p-3 rounded-3 border border-secondary-subtle bg-dark-subtle d-flex align-items-center gap-2 cursor-pointer transition">
                                        <input type="radio" name="topup_method" value="cash" onchange="handleMethodChange(this.value)" class="form-check-input mt-0">
                                        <div class="lh-sm">
                                            <span class="fw-bold text-white d-block small">เงินสดที่เคาน์เตอร์</span>
                                            <small class="text-secondary" style="font-size: 10px;">ชำระกับพนักงานร้าน</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            id="submitTopupBtn"
                            onclick="handleTopupSubmit()"
                            class="btn btn-danger w-100 py-3 fw-bold mt-2 shadow"
                        >
                            ⚡ ยืนยันการเติมเงิน
                        </button>
                    </form>
                </div>
            </div>

            <!-- Section 2: Buy Time Packages -->
            <div class="col-12 col-lg-6">
                <div class="card bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="border-bottom border-secondary-subtle pb-3 mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="bg-danger rounded" style="width: 4px; height: 22px;"></span>
                                <div>
                                    <h2 class="h5 fw-bold text-white m-0">ซื้อแพ็กเกจชั่วโมง (Time Packages)</h2>
                                    <small class="text-secondary">ซื้อเวลาเหมาชั่วโมง คุ้มกว่าเล่นแบบคิดตามจริง (หักจาก Wallet)</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-3 mb-4">
                            @foreach ($packages as $pkg)
                                <div class="p-3 bg-dark-subtle border border-secondary-subtle rounded-3 d-flex justify-content-between align-items-center transition hover-border-danger">
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold text-white small">{{ $pkg->name }}</span>
                                            <span class="badge bg-danger-subtle text-danger" style="font-size: 10px;">{{ $pkg->duration_hours }} ชม.</span>
                                        </div>
                                        <small class="text-secondary d-block" style="font-size: 11px;">
                                            เล่นได้ {{ $pkg->duration_hours * 60 }} นาที
                                            @if ($pkg->zone)
                                                <span class="text-danger fw-semibold">• โซน {{ $pkg->zone->name }}</span>
                                            @else
                                                <span class="text-success fw-semibold">• ใช้ได้ทุกโซน</span>
                                            @endif
                                        </small>
                                    </div>

                                    <div class="d-flex align-items-center gap-3">
                                        <span class="fs-6 fw-bold text-white font-monospace">฿{{ number_format($pkg->price, 2) }}</span>
                                        <form method="POST" action="{{ route('customer.buy-package') }}" onsubmit="return confirm('คุณต้องการซื้อ \'{{ addslashes($pkg->name) }}\' ในราคา ฿{{ number_format($pkg->price, 2) }} หรือไม่?');">
                                            @csrf
                                            <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                            <button
                                                type="submit"
                                                @disabled($user->balance < $pkg->price)
                                                class="btn btn-danger btn-sm px-3 fw-bold rounded-pill"
                                            >
                                                ซื้อเลย
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- User Active Packages list -->
                    @if ($userPackages->isNotEmpty())
                        <div class="pt-3 border-top border-secondary-subtle">
                            <small class="text-secondary text-uppercase fw-bold d-block mb-2" style="font-size: 10px;">
                                แพ็กเกจที่คุณมีอยู่ในขณะนี้:
                            </small>
                            <div class="d-flex flex-column gap-2">
                                @foreach ($userPackages as $mp)
                                    <div class="p-2.5 bg-dark-subtle border border-secondary-subtle rounded-3 d-flex justify-content-between align-items-center small">
                                        <span class="fw-bold text-white">{{ $mp->package->name }}</span>
                                        <span class="badge bg-danger-subtle text-danger font-monospace">
                                            {{ $mp->remaining_minutes }} นาทีคงเหลือ
                                        </span>
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
                    <h3 class="h6 fw-bold text-white m-0">ประวัติการทำรายการล่าสุด (10 รายการ)</h3>
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
                            @foreach ($transactions as $tx)
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
                                        {{ $tx->created_at->format('d/m/Y H:i') }}
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
                            <span class="fw-bold text-white small">PromptPay QR Code</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="mb-3">
                        <small class="text-secondary d-block">สแกนเพื่อชำระเงิน (ระบบจำลอง)</small>
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
                        <div class="text-secondary" style="font-size: 11px;">
                            ชื่อบัญชี: <span class="text-light fw-bold">Letsplay cafe</span><br>
                            เลขอ้างอิง: <span class="text-secondary font-monospace">089-XXX-XXXX</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary w-50 small fw-bold" data-bs-dismiss="modal">
                            ยกเลิก
                        </button>
                        <button type="button" id="confirmPaymentBtn" onclick="submitTopupForm()" class="btn btn-danger w-50 small fw-bold">
                            ✓ สแกนจ่ายเสร็จแล้ว
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
