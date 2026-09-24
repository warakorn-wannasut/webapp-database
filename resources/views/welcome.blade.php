<!DOCTYPE html>
<html lang="th" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salai Gaming Café | บริการร้านเกมและอาหารสไตล์เกาหลี 24 ชม.</title>
    @include('partials.head')
</head>
<body class="salai-bg min-h-screen text-slate-100 flex flex-col justify-between selection:bg-red-600 selection:text-white">
    <!-- Navbar (Salai Termgames Style) -->
    <header class="border-b border-[#1e2430] bg-[#0a0c10]/90 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-18 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-rose-700 flex items-center justify-center font-black text-white text-lg shadow-[0_0_18px_rgba(220,38,38,0.6)] group-hover:scale-105 transition-transform">
                    SG
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-xl text-white tracking-wide font-sans">
                            SALAI <span class="text-red-500">GAMING</span>
                        </span>
                        <span class="salai-badge-red text-[10px]">24 HRS</span>
                    </div>
                    <p class="text-[11px] text-zinc-400 -mt-1 hidden sm:block">PC Bang & Korean Café</p>
                </div>
            </a>

            <!-- Search Bar Pill (Salai Style) -->
            <div class="hidden md:flex flex-1 max-w-md mx-4">
                <div class="relative w-full">
                    <input type="text" placeholder="ค้นหาเกม, โซนคอม, หรือเมนูอาหาร..." class="w-full bg-[#141824] border border-[#232938] rounded-full py-2 pl-10 pr-4 text-xs text-zinc-200 placeholder-zinc-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                    <svg class="w-4 h-4 text-zinc-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 1114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Nav Actions & Auth -->
            <div class="flex items-center gap-3">
                @auth
                    <!-- Quick Wallet Pill -->
                    <a href="{{ route('customer.topup') }}" class="hidden sm:flex items-center gap-2 bg-[#161a25] border border-[#262d3d] hover:border-red-500/50 rounded-full px-3.5 py-1.5 transition group">
                        <div class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs font-bold">
                            ฿
                        </div>
                        <span class="text-xs font-bold text-zinc-200 group-hover:text-red-400">
                            {{ number_format(auth()->user()->balance, 2) }}
                        </span>
                        <span class="text-[10px] bg-red-600 text-white font-bold px-1.5 py-0.5 rounded-full">+ เติม</span>
                    </a>

                    <a href="{{ route('dashboard') }}" class="salai-btn-primary text-xs py-2 px-4">
                        เข้าสู่แดชบอร์ด &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-medium text-zinc-300 hover:text-white transition">
                        เข้าสู่ระบบ
                    </a>
                    <a href="{{ route('register') }}" class="salai-btn-primary text-xs py-2 px-4">
                        สมัครสมาชิก
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex-1 space-y-10 w-full">
        <!-- Hero Promo Banner (Salai Termgames Big Banner) -->
        <div class="relative overflow-hidden rounded-3xl border border-[#232938] bg-gradient-to-r from-[#17080a] via-[#131622] to-[#0a0c10] p-6 sm:p-10 shadow-2xl">
            <!-- Background Decorative Glow -->
            <div class="absolute -right-16 -top-16 w-80 h-80 bg-red-600/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-rose-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="salai-badge-red text-xs">
                        🔥 เว็บจัดการร้านเกม & คาเฟ่อันดับ 1
                    </span>
                    <span class="salai-badge-gold text-xs">
                        ⚡ ปรับลดราคาเกมมิ่งเกียร์ใหม่
                    </span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white leading-tight">
                    เติมเวลาเล่นเกม & สั่งของอร่อย <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-rose-400 to-amber-400">
                        ส่งตรงถึงโต๊ะคอมทันที
                    </span>
                </h1>

                <p class="text-sm sm:text-base text-zinc-400 leading-relaxed">
                    ระบบร้านเกมสไตล์เกาหลี (PC Bang) ครบวงจร คิดค่าบริการอัตโนมัติตามเวลาจริงหรือซื้อแพ็กเกจชั่วโมงสุดคุ้ม พร้อมรามยอนและอาหารร้อนเสิร์ฟถึงที่นั่ง
                </p>

                <div class="pt-2 flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ route('customer.seat-map') }}" class="salai-btn-primary text-sm py-2.5 px-6">
                            🖥️ เลือกผังที่นั่งเล่นเกม
                        </a>
                        <a href="{{ route('customer.food-order') }}" class="salai-card text-xs font-semibold px-5 py-2.5 text-zinc-300 hover:text-white border-[#262d3d] hover:border-red-500/50">
                            🍜 เมนูอาหารเกาหลี
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="salai-btn-primary text-sm py-2.5 px-6">
                            🚀 เข้าสู่ระบบเพื่อเริ่มใช้งาน
                        </a>
                        <a href="{{ route('register') }}" class="salai-card text-xs font-semibold px-5 py-2.5 text-zinc-300 hover:text-white border-[#262d3d] hover:border-red-500/50">
                            สมัครสมาชิกใหม่
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Quick Filter Category Pills (Salai Style) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide text-sm">
            <button class="salai-pill bg-red-600 text-white font-semibold shadow-lg shadow-red-600/30 whitespace-nowrap">
                🔥 ยอดนิยมทั้งหมด
            </button>
            <a href="{{ route('customer.seat-map') }}" class="salai-pill bg-[#131622] hover:bg-[#1a1f30] text-zinc-300 hover:text-white border border-[#232938] whitespace-nowrap">
                🖥️ โซน PC Gaming
            </a>
            <a href="{{ route('customer.food-order') }}" class="salai-pill bg-[#131622] hover:bg-[#1a1f30] text-zinc-300 hover:text-white border border-[#232938] whitespace-nowrap">
                🍜 รามยอน & ของทานเล่น
            </a>
            <a href="{{ route('customer.topup') }}" class="salai-pill bg-[#131622] hover:bg-[#1a1f30] text-zinc-300 hover:text-white border border-[#232938] whitespace-nowrap">
                ⚡ แพ็กเกจชั่วโมงสุดคุ้ม
            </a>
            <a href="{{ route('customer.topup') }}" class="salai-pill bg-[#131622] hover:bg-[#1a1f30] text-zinc-300 hover:text-white border border-[#232938] whitespace-nowrap">
                💳 เติมเงิน Wallet อัตโนมัติ
            </a>
        </div>

        <!-- Section 1: โซนที่นั่งเกมมิ่งยอดนิยม (PC Bang Zones) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h2 class="text-xl font-black text-white tracking-wide font-sans">
                        โซนที่นั่งเกมมิ่งระดับพรีเมียม (PC Zones)
                    </h2>
                </div>
                <a href="{{ route('customer.seat-map') }}" class="text-xs font-semibold text-red-400 hover:text-red-300">
                    ดูผังที่นั่งทั้งหมด &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Standard Zone -->
                <div class="salai-card p-5 flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="salai-badge-red">STANDARD PC</span>
                            <span class="text-xs font-bold text-amber-400 bg-amber-400/10 px-2.5 py-0.5 rounded-full border border-amber-400/20">
                                ยอดนิยม
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-white group-hover:text-red-400 transition-colors">
                            โซนคอมมาตรฐาน (Standard Zone)
                        </h3>
                        <p class="text-xs text-zinc-400 leading-relaxed">
                            สเปกเกมมิ่งลื่นไหล i5 Gen 13, RTX 4060, จอ 165Hz พร้อมเก้าอี้เกมมิ่งระบายอากาศ
                        </p>
                    </div>

                    <div class="pt-4 border-t border-[#1e2430] mt-4 flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-black text-white">฿20</span>
                            <span class="text-xs text-zinc-400">/ 1 ชม.</span>
                        </div>
                        <a href="{{ route('customer.seat-map') }}" class="salai-btn-primary text-xs py-2 px-3.5">
                            เลือกโต๊ะนี้
                        </a>
                    </div>
                </div>

                <!-- VIP Lounge Zone -->
                <div class="salai-card p-5 flex flex-col justify-between group border-red-500/20">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="salai-badge-gold">VIP LOUNGE</span>
                            <span class="text-xs font-bold text-rose-400 bg-rose-500/10 px-2.5 py-0.5 rounded-full border border-rose-500/20">
                                HOT DEAL
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-white group-hover:text-red-400 transition-colors">
                            ห้องวีไอพี เลานจ์ (VIP Lounge)
                        </h3>
                        <p class="text-xs text-zinc-400 leading-relaxed">
                            สเปกคอมไฮเอนด์ i7 Gen 14, RTX 4070 Ti, จอ 240Hz โค้ง คีย์บอร์ด Custom และหูฟัง 7.1
                        </p>
                    </div>

                    <div class="pt-4 border-t border-[#1e2430] mt-4 flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-black text-white">฿35</span>
                            <span class="text-xs text-zinc-400">/ 1 ชม.</span>
                        </div>
                        <a href="{{ route('customer.seat-map') }}" class="salai-btn-primary text-xs py-2 px-3.5">
                            เลือกโต๊ะนี้
                        </a>
                    </div>
                </div>

                <!-- Pro Streamer Zone -->
                <div class="salai-card p-5 flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="salai-badge-red">PRO STREAMER</span>
                            <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
                                เก็บเสียง
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-white group-hover:text-red-400 transition-colors">
                            ห้องสตรีมเมอร์ส่วนตัว (Pro Studio)
                        </h3>
                        <p class="text-xs text-zinc-400 leading-relaxed">
                            สเปกตัวท็อป RTX 4090, ไมค์ Shure, กล้อง Sony 4K, ไฟสตูดิโอ และห้องเก็บเสียงส่วนตัว
                        </p>
                    </div>

                    <div class="pt-4 border-t border-[#1e2430] mt-4 flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-black text-white">฿50</span>
                            <span class="text-xs text-zinc-400">/ 1 ชม.</span>
                        </div>
                        <a href="{{ route('customer.seat-map') }}" class="salai-btn-primary text-xs py-2 px-3.5">
                            เลือกโต๊ะนี้
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: เมนูอาหารเกาหลีและของทานเล่น (Korean Food & Drinks) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="salai-step-header">
                    <span class="salai-step-bar"></span>
                    <h2 class="text-xl font-black text-white tracking-wide font-sans">
                        เมนูอาหารเกาหลีพร้อมเสิร์ฟถึงโต๊ะ (Food & Drink)
                    </h2>
                </div>
                <a href="{{ route('customer.food-order') }}" class="text-xs font-semibold text-red-400 hover:text-red-300">
                    ดูเมนูทั้งหมด &rarr;
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <!-- Shin Ramyun -->
                <div class="salai-card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-red-950 to-zinc-900 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            🍜
                        </div>
                        <span class="salai-badge-red text-[10px]">ซิกเนเจอร์</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">ชินรามยอนไข่ชีส</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">รามยอนเกาหลีเข้มข้น</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿79</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>

                <!-- Korean Fried Chicken -->
                <div class="salai-card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-amber-950 to-zinc-900 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            🍗
                        </div>
                        <span class="salai-badge-gold text-[10px]">ขายดี</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">ไก่ทอดซอสเผ็ดเกาหลี</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">คลุกซอสเข้มข้น 4 ชิ้น</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿89</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>

                <!-- Tteokbokki -->
                <div class="salai-card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-red-950 to-zinc-900 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            🍲
                        </div>
                        <span class="salai-badge-red text-[10px]">รสจัดจ้าน</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">ต๊อกบกกีชีสยืด</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">แป้งเหนียวนุ่มชีสเยิ้ม</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿89</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>

                <!-- Kimbap -->
                <div class="salai-card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-emerald-950 to-zinc-900 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            🍱
                        </div>
                        <span class="salai-badge-gold text-[10px]">ทานง่าย</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">คิมบับไส้ทูน่ามาโย</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">ข้าวห่อสาหร่ายสไตล์เกาหลี</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿69</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>

                <!-- Iced Americano -->
                <div class="salai-card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-zinc-800 to-zinc-900 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            ☕
                        </div>
                        <span class="salai-badge-red text-[10px]">ตื่นเต็มตา</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">อเมริกาโน่เย็นคั่วเข้ม</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">กาแฟสดแท้สำหรับเล่นเกมดึก</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿45</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>

                <!-- Sparkling Soda -->
                <div class="salai-card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-sky-950 to-zinc-900 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            🥤
                        </div>
                        <span class="salai-badge-gold text-[10px]">สดชื่น</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">ยุสุโซดาซ่าส์</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">ส้มยุสุเกาหลีโซดาเย็นเจี๊ยบ</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿40</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: ข้อมูลบัญชีสำหรับทดสอบระบบ (Demo Accounts Box) -->
        <div class="salai-card-glow p-6 rounded-3xl space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#232938] pb-3">
                <div class="flex items-center gap-2">
                    <span class="salai-step-bar"></span>
                    <h3 class="font-bold text-base text-white font-sans">
                        บัญชีสำหรับทดสอบระบบร้านเกม (Seeded Accounts)
                    </h3>
                </div>
                <span class="salai-badge-gold text-xs font-mono">
                    🔑 รหัสผ่านทุกบัญชี: password
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 bg-[#141824] border border-[#232938] rounded-2xl space-y-1 hover:border-red-500/40 transition">
                    <span class="salai-badge-red text-[10px]">
                        CUSTOMER 1 (ลูกค้า)
                    </span>
                    <p class="text-sm font-bold text-white mt-1">Username: customer1</p>
                    <p class="text-xs text-zinc-400 font-mono">Email: cust1@pcbang.test</p>
                    <p class="text-xs text-emerald-400 font-semibold">ยอดเงินเริ่มต้น: ฿300.00</p>
                </div>

                <div class="p-4 bg-[#141824] border border-[#232938] rounded-2xl space-y-1 hover:border-amber-500/40 transition">
                    <span class="salai-badge-gold text-[10px]">
                        STAFF (พนักงานร้าน)
                    </span>
                    <p class="text-sm font-bold text-white mt-1">Username: staff</p>
                    <p class="text-xs text-zinc-400 font-mono">Email: staff@pcbang.test</p>
                    <p class="text-xs text-zinc-400">หน้าที่: คิวครัว + มอนิเตอร์โต๊ะ</p>
                </div>

                <div class="p-4 bg-[#141824] border border-[#232938] rounded-2xl space-y-1 hover:border-purple-500/40 transition">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/15 text-purple-400 border border-purple-500/30">
                        ADMIN (ผู้ดูแลร้าน)
                    </span>
                    <p class="text-sm font-bold text-white mt-1">Username: admin</p>
                    <p class="text-xs text-zinc-400 font-mono">Email: admin@pcbang.test</p>
                    <p class="text-xs text-zinc-400">หน้าที่: คลังสินค้า + สรุปยอดขาย</p>
                </div>
            </div>
        </div>

        <!-- Section 4: ทำไมต้อง Salai Gaming Café (Trust & Features) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="salai-card p-5 space-y-2">
                <div class="w-10 h-10 rounded-xl bg-red-600/20 text-red-500 flex items-center justify-center font-black text-lg border border-red-500/30">
                    ⚡
                </div>
                <h4 class="font-bold text-sm text-white">เช็คอินและคิดเงินอัตโนมัติ</h4>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    ระบบคำนวณค่าบริการตามเวลาจริงระดับวินาที รองรับการหักจากยอด Wallet หรือตัดเวลาจากแพ็กเกจชั่วโมงสะสม
                </p>
            </div>

            <div class="salai-card p-5 space-y-2">
                <div class="w-10 h-10 rounded-xl bg-amber-600/20 text-amber-500 flex items-center justify-center font-black text-lg border border-amber-500/30">
                    🍜
                </div>
                <h4 class="font-bold text-sm text-white">สั่งอาหารเสิร์ฟร้อนถึงโต๊ะ</h4>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    ตัดสต็อกทันทีด้วย Atomic Database Locks รองรับการชำระผ่าน Wallet, สแกน QR PromptPay หรือเงินสดปลายทาง
                </p>
            </div>

            <div class="salai-card p-5 space-y-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-600/20 text-emerald-500 flex items-center justify-center font-black text-lg border border-emerald-500/30">
                    🛡️
                </div>
                <h4 class="font-bold text-sm text-white">ฐานข้อมูลมาตรฐาน 3NF & ปลอดภัย</h4>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    ออกแบบตารางครบ 3rd Normal Form พร้อมบันทึกประวัติการเงิน (Audit Trail) และ Transaction ปลอดภัยสูงสุด
                </p>
            </div>
        </div>
    </main>

    <!-- Footer (Salai Termgames Style) -->
    <footer class="border-t border-[#1e2430] bg-[#07090d] py-10 mt-12 text-xs text-zinc-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-600 flex items-center justify-center font-black text-white text-sm">
                        SG
                    </div>
                    <span class="font-extrabold text-base text-white">
                        SALAI <span class="text-red-500">GAMING CAFÉ</span>
                    </span>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-4 text-xs text-zinc-400">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> พร้อมเพย์ QR</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> ทรูมันนี่ วอลเล็ท</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> เงินสดหน้าเคาน์เตอร์</span>
                </div>
            </div>

            <div class="border-t border-[#1e2430] pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-zinc-500">
                <p>&copy; 2026 Salai Gaming Café Management System. สไตล์ร้านเกมและคาเฟ่ครบวงจร</p>
                <p>พัฒนาด้วย Laravel 13 &bull; Livewire 4 &bull; SQLite &bull; Tailwind CSS</p>
            </div>
        </div>
    </footer>
</body>
</html>
