<x-layouts::app :title="__('Seat Monitor')">
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

        <!-- Header & Live Status Stats -->
        <div class="card bg-dark border-secondary-subtle rounded-4 p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 shadow-sm">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="spinner-grow spinner-grow-sm text-success" style="width: 10px; height: 10px;"></span>
                    <h2 class="h4 fw-bold text-white m-0">แดชบอร์ดมอนิเตอร์ที่นั่ง (Live Seat Monitor)</h2>
                </div>
                <small class="text-secondary ps-3">มอนิเตอร์สถานะเครื่องคอมพิวเตอร์และเซสชันลูกค้าแบบเรียลไทม์</small>
            </div>

            <!-- Quick Counters -->
            <div class="d-flex flex-wrap gap-2 small fw-semibold">
                <span class="badge bg-dark-subtle border border-secondary-subtle text-light py-2 px-3">
                    ทั้งหมด: {{ $totalSeats }} เครื่อง
                </span>
                <span class="badge bg-danger-subtle border border-danger-subtle text-danger py-2 px-3">
                    เล่นอยู่: {{ $occupiedSeats }} เครื่อง
                </span>
                <span class="badge bg-success-subtle border border-success-subtle text-success py-2 px-3">
                    ว่าง: {{ $availableSeats }} เครื่อง
                </span>
                <span class="badge bg-secondary-subtle border border-secondary text-secondary py-2 px-3">
                    ซ่อมบำรุง: {{ $maintenanceSeats }} เครื่อง
                </span>
            </div>
        </div>

        <!-- Zones & Seats -->
        @foreach ($zones as $zone)
            <div class="card bg-dark border-secondary-subtle rounded-4 p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center border-bottom border-secondary-subtle pb-3 mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="bg-danger rounded" style="width: 4px; height: 20px;"></span>
                        <h3 class="h6 fw-bold text-white m-0">{{ $zone->name }}</h3>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">
                        ฿{{ number_format($zone->hourly_rate, 2) }} / ชม.
                    </span>
                </div>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                    @foreach ($zone->seats as $seat)
                        @php
                            $session = $seat->seatSessions->first();
                            $elapsedMins = 0;
                            if ($session) {
                                $startTime = \Illuminate\Support\Carbon::parse($session->start_time);
                                $elapsedSeconds = max(1, (int) $startTime->diffInSeconds(\Illuminate\Support\Carbon::now()));
                                $elapsedMins = max(1, (int) ceil($elapsedSeconds / 60));
                            }
                        @endphp

                        <div class="col">
                            <div class="card h-100 p-3 rounded-4 transition d-flex flex-column justify-content-between
                                @if($seat->status === 'occupied') bg-dark-subtle border-danger-subtle shadow-sm
                                @elseif($seat->status === 'available') bg-dark border-secondary-subtle
                                @else bg-dark border-secondary-subtle opacity-50
                                @endif
                            ">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="fs-5 fw-black text-white font-monospace">
                                            {{ $seat->seat_number }}
                                        </div>
                                        <span class="badge
                                            @if($seat->status === 'occupied') bg-danger-subtle text-danger border border-danger-subtle
                                            @elseif($seat->status === 'available') bg-success-subtle text-success border border-success-subtle
                                            @else bg-secondary-subtle text-secondary
                                            @endif
                                        " style="font-size: 10px;">
                                            {{ strtoupper($seat->status) }}
                                        </span>
                                    </div>

                                    @if ($session)
                                        <div class="pt-2 border-top border-secondary-subtle small d-flex flex-column gap-1.5">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-secondary">ผู้เล่น:</span>
                                                <strong class="text-white text-truncate" style="max-width: 120px;">{{ $session->user->name }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-secondary">เล่นไปแล้ว:</span>
                                                <strong class="text-light font-monospace">{{ \App\Models\UserPackage::formatMinutes($elapsedMins) }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-secondary">โหมด:</span>
                                                @if($session->userPackage)
                                                    <strong class="text-danger text-truncate" style="max-width: 120px;" title="{{ $session->userPackage->package?->name ?? 'แพ็กเกจชั่วโมง' }}">
                                                        {{ $session->userPackage->package?->name ?? 'แพ็กเกจชั่วโมง' }}
                                                    </strong>
                                                @else
                                                    <strong class="text-warning">Pay as you go</strong>
                                                @endif
                                            </div>
                                            @if($session->userPackage)
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-secondary">แพ็กเกจคงเหลือ:</span>
                                                    <strong class="text-danger font-monospace">{{ $session->userPackage->formattedRemainingTime() }}</strong>
                                                </div>
                                            @endif
                                            <div class="d-flex justify-content-between">
                                                <span class="text-secondary">ยอดเงินในบัญชี:</span>
                                                <strong class="text-warning font-monospace">฿{{ number_format($session->user->balance, 2) }}</strong>
                                            </div>
                                        </div>
                                    @else
                                        <div class="py-3 text-center text-secondary small">
                                            เครื่องว่าง พร้อมใช้งาน
                                        </div>
                                    @endif
                                </div>

                                <!-- Staff Action Buttons -->
                                <div class="pt-3 border-top border-secondary-subtle mt-3">
                                    @if ($session)
                                        <form method="POST" action="{{ route('staff.force-end') }}" onsubmit="return confirm('คุณต้องการสั่งปิดเครื่อง {{ $seat->seat_number }} และคิดเงินทันทีหรือไม่?');">
                                            @csrf
                                            <input type="hidden" name="session_id" value="{{ $session->id }}">
                                            <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold rounded-pill">
                                                Force End (ปิดเครื่อง)
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('staff.toggle-seat') }}">
                                            @csrf
                                            <input type="hidden" name="seat_id" value="{{ $seat->id }}">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm w-100 text-light rounded-pill">
                                                {{ $seat->status === 'maintenance' ? 'เปิดใช้งานเครื่อง' : 'แจ้งซ่อมบำรุง' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-layouts::app>
