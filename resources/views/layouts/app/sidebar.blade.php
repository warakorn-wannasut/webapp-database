<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen app-bg text-slate-100 selection:bg-red-600 selection:text-white">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-[#1e2430] bg-[#0c0f16]/95 backdrop-blur-md">
            <flux:sidebar.header>
                <div class="flex items-center gap-2.5 px-2 py-1">
                    <div class="w-8 h-8 rounded-lg bg-red-600 flex items-center justify-center font-bold text-white text-sm">
                        LP
                    </div>
                    <div>
                        <span class="font-extrabold text-sm text-white tracking-wide">
                            LETSPLAY <span class="text-red-500">GAMING</span>
                        </span>
                    </div>
                </div>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <!-- Wallet Widget in Sidebar -->
            <div class="mx-3 my-2 p-3 rounded-xl bg-[#131622] border border-[#1e2430]">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-400 font-medium">ยอดเงินในกระเป๋า</span>
                    <span class="badge-gold text-[10px]">WALLET</span>
                </div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-xl font-bold text-white font-mono">
                        ฿{{ number_format(auth()->user()->balance ?? 0, 2) }}
                    </span>
                    <a href="{{ route('customer.topup') }}" class="text-xs font-semibold text-red-400 hover:text-red-300">
                        + เติมเงิน
                    </a>
                </div>
            </div>

            <flux:sidebar.nav>
                <flux:sidebar.group heading="บริการลูกค้า (Customer)" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                        แดชบอร์ดส่วนตัว
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="computer-desktop" :href="route('customer.seat-map')" :current="request()->routeIs('customer.seat-map')">
                        ผังที่นั่ง (Seat Map)
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shopping-bag" :href="route('customer.food-order')" :current="request()->routeIs('customer.food-order')">
                        สั่งอาหาร/เครื่องดื่ม
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="credit-card" :href="route('customer.topup')" :current="request()->routeIs('customer.topup')">
                        เติมเงิน / ซื้อแพ็กเกจ
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if (auth()->user()->isStaff())
                    <flux:sidebar.group heading="ระบบจัดการร้าน (Staff & Admin)" class="grid mt-4">
                        <flux:sidebar.item icon="chart-bar" :href="route('staff.seat-monitor')" :current="request()->routeIs('staff.seat-monitor')">
                            มอนิเตอร์ที่นั่งหน้าร้าน
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="clock" :href="route('staff.kitchen-queue')" :current="request()->routeIs('staff.kitchen-queue')">
                            คิวออเดอร์ห้องครัว
                        </flux:sidebar.item>
                        @if (auth()->user()->isAdmin())
                            <flux:sidebar.item icon="archive-box" :href="route('admin.stock-manager')" :current="request()->routeIs('admin.stock-manager')">
                                จัดการสต็อกสินค้า
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="document-text" :href="route('admin.sales-report')" :current="request()->routeIs('admin.sales-report')">
                                รายงานสรุปยอดขาย
                            </flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
