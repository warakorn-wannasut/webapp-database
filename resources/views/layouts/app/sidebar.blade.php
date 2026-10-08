<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-black text-light min-vh-100 d-flex flex-column">
        <!-- Mobile Top Navbar -->
        <header class="navbar navbar-dark bg-dark d-lg-none border-bottom border-secondary-subtle sticky-top px-3 py-2">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <a href="{{ route('dashboard') }}" class="navbar-brand d-flex align-items-center gap-2 m-0 fs-6 fw-bold">
                    <span class="badge bg-danger rounded-2 px-2 py-1">LP</span>
                    <span>LETSPLAY <span class="text-danger">GAMING</span></span>
                </a>
            </div>

            <!-- Quick Wallet for Mobile -->
            <a href="{{ route('customer.topup') }}" class="badge text-bg-dark border border-secondary text-decoration-none py-2 px-2.5 d-flex align-items-center gap-1">
                <span class="text-warning fw-bold">฿</span>
                <span class="fw-bold">{{ number_format(auth()->user()->balance ?? 0, 2) }}</span>
            </a>
        </header>

        <!-- Desktop Fixed Sidebar -->
        <aside class="bs-sidebar d-none d-lg-flex flex-column position-fixed top-0 bottom-0 start-0 z-3 p-3 overflow-y-auto">
            <!-- Brand -->
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none text-white mb-3 px-1">
                <div class="rounded-3 bg-danger d-flex align-items-center justify-center fw-bold text-white" style="width: 34px; height: 34px; font-size: 14px; line-height: 1;">
                    LP
                </div>
                <div class="lh-sm">
                    <div class="fw-black fs-6 tracking-wide">LETSPLAY <span class="text-danger">GAMING</span></div>
                    <small class="text-secondary" style="font-size: 10px;">Cyber Cafe & Lounge</small>
                </div>
            </a>

            <!-- Wallet Widget -->
            <div class="bs-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-secondary fw-semibold" style="font-size: 11px;">ยอดเงินในกระเป๋า</small>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 9px;">WALLET</span>
                </div>
                <div class="d-flex justify-content-between align-items-baseline">
                    <span class="fs-5 fw-bold text-white font-monospace">
                        ฿{{ number_format(auth()->user()->balance ?? 0, 2) }}
                    </span>
                    <a href="{{ route('customer.topup') }}" class="text-danger text-decoration-none fw-semibold" style="font-size: 11px;">
                        + เติมเงิน
                    </a>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="nav flex-column gap-1 flex-grow-1">
                <div class="text-secondary text-uppercase fw-bold px-2 pt-2 pb-1" style="font-size: 10px; letter-spacing: 0.05em;">
                    บริการลูกค้า (Customer)
                </div>

                <a href="{{ route('dashboard') }}" class="bs-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 fs-6"></i>
                    <span>แดชบอร์ดส่วนตัว</span>
                </a>

                <a href="{{ route('customer.seat-map') }}" class="bs-nav-link {{ request()->routeIs('customer.seat-map') ? 'active' : '' }}">
                    <i class="bi bi-display fs-6"></i>
                    <span>ผังที่นั่ง (Seat Map)</span>
                </a>

                <a href="{{ route('customer.food-order') }}" class="bs-nav-link {{ request()->routeIs('customer.food-order') ? 'active' : '' }}">
                    <i class="bi bi-cup-hot fs-6"></i>
                    <span>สั่งอาหาร/เครื่องดื่ม</span>
                </a>

                <a href="{{ route('customer.topup') }}" class="bs-nav-link {{ request()->routeIs('customer.topup') ? 'active' : '' }}">
                    <i class="bi bi-wallet2 fs-6"></i>
                    <span>เติมเงิน / ซื้อแพ็กเกจ</span>
                </a>

                @if (auth()->user()->isStaff())
                    <div class="text-secondary text-uppercase fw-bold px-2 pt-3 pb-1" style="font-size: 10px; letter-spacing: 0.05em;">
                        ระบบจัดการร้าน (Staff & Admin)
                    </div>

                    <a href="{{ route('staff.seat-monitor') }}" class="bs-nav-link {{ request()->routeIs('staff.seat-monitor') ? 'active' : '' }}">
                        <i class="bi bi-grid-3x3-gap fs-6"></i>
                        <span>มอนิเตอร์ที่นั่งหน้าร้าน</span>
                    </a>

                    <a href="{{ route('staff.kitchen-queue') }}" class="bs-nav-link {{ request()->routeIs('staff.kitchen-queue') ? 'active' : '' }}">
                        <i class="bi bi-fire fs-6"></i>
                        <span>คิวออเดอร์ห้องครัว</span>
                    </a>

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.stock-manager') }}" class="bs-nav-link {{ request()->routeIs('admin.stock-manager') ? 'active' : '' }}">
                            <i class="bi bi-box-seam fs-6"></i>
                            <span>จัดการสต็อกสินค้า</span>
                        </a>

                        <a href="{{ route('admin.sales-report') }}" class="bs-nav-link {{ request()->routeIs('admin.sales-report') ? 'active' : '' }}">
                            <i class="bi bi-graph-up-arrow fs-6"></i>
                            <span>รายงานสรุปยอดขาย</span>
                        </a>
                    @endif
                @endif
            </nav>

            <hr class="border-secondary-subtle my-3">

            <!-- Desktop User Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle p-2 rounded-3 hover-bg-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold me-2" style="width: 32px; height: 32px; font-size: 12px;">
                        {{ auth()->user()->initials() ?? 'U' }}
                    </div>
                    <div class="overflow-hidden lh-sm me-auto">
                        <div class="text-truncate fw-bold text-white" style="font-size: 13px;">{{ auth()->user()->name }}</div>
                        <small class="text-secondary text-truncate d-block" style="font-size: 11px;">{{ auth()->user()->email }}</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-secondary-subtle" style="font-size: 13px;">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                            <i class="bi bi-gear"></i> การตั้งค่าโปรไฟล์
                        </a>
                    </li>
                    <li><hr class="dropdown-divider border-secondary-subtle"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Mobile Offcanvas Sidebar -->
        <div class="offcanvas offcanvas-start bg-dark text-white border-end border-secondary-subtle d-lg-none" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
            <div class="offcanvas-header border-bottom border-secondary-subtle">
                <div class="d-flex align-items-center gap-2" id="sidebarOffcanvasLabel">
                    <span class="badge bg-danger rounded-2 px-2 py-1">LP</span>
                    <span class="fw-bold">LETSPLAY <span class="text-danger">GAMING</span></span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column p-3">
                <!-- Mobile Wallet Card -->
                <div class="bs-card p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-secondary" style="font-size: 11px;">ยอดเงินในกระเป๋า</small>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 9px;">WALLET</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline">
                        <span class="fs-5 fw-bold text-white font-monospace">฿{{ number_format(auth()->user()->balance ?? 0, 2) }}</span>
                        <a href="{{ route('customer.topup') }}" class="text-danger text-decoration-none fw-semibold" style="font-size: 11px;">+ เติมเงิน</a>
                    </div>
                </div>

                <nav class="nav flex-column gap-1 flex-grow-1">
                    <div class="text-secondary text-uppercase fw-bold px-2 pt-1 pb-1" style="font-size: 10px;">บริการลูกค้า</div>
                    <a href="{{ route('dashboard') }}" class="bs-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> แดชบอร์ด</a>
                    <a href="{{ route('customer.seat-map') }}" class="bs-nav-link {{ request()->routeIs('customer.seat-map') ? 'active' : '' }}"><i class="bi bi-display"></i> ผังที่นั่ง</a>
                    <a href="{{ route('customer.food-order') }}" class="bs-nav-link {{ request()->routeIs('customer.food-order') ? 'active' : '' }}"><i class="bi bi-cup-hot"></i> สั่งอาหาร</a>
                    <a href="{{ route('customer.topup') }}" class="bs-nav-link {{ request()->routeIs('customer.topup') ? 'active' : '' }}"><i class="bi bi-wallet2"></i> เติมเงิน/แพ็กเกจ</a>

                    @if (auth()->user()->isStaff())
                        <div class="text-secondary text-uppercase fw-bold px-2 pt-3 pb-1" style="font-size: 10px;">จัดการร้าน</div>
                        <a href="{{ route('staff.seat-monitor') }}" class="bs-nav-link {{ request()->routeIs('staff.seat-monitor') ? 'active' : '' }}"><i class="bi bi-grid-3x3-gap"></i> มอนิเตอร์ที่นั่ง</a>
                        <a href="{{ route('staff.kitchen-queue') }}" class="bs-nav-link {{ request()->routeIs('staff.kitchen-queue') ? 'active' : '' }}"><i class="bi bi-fire"></i> คิวห้องครัว</a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.stock-manager') }}" class="bs-nav-link {{ request()->routeIs('admin.stock-manager') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> จัดการสต็อก</a>
                            <a href="{{ route('admin.sales-report') }}" class="bs-nav-link {{ request()->routeIs('admin.sales-report') ? 'active' : '' }}"><i class="bi bi-graph-up-arrow"></i> สรุปยอดขาย</a>
                        @endif
                    @endif
                </nav>

                <hr class="border-secondary-subtle my-3">

                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold text-white" style="font-size: 13px;">{{ auth()->user()->name }}</div>
                        <small class="text-secondary" style="font-size: 11px;">{{ auth()->user()->email }}</small>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-box-arrow-right"></i> ออก
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-grow-1" style="margin-left: 0;">
            <div class="d-none d-lg-block" style="width: 260px; float: left; height: 1px;"></div>
            <div style="min-height: 100vh;">
                {{ $slot }}
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
