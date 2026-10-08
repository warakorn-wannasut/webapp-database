<!DOCTYPE html>
<html lang="th" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letsplay Gaming Cafe | ร้านเกมและคาเฟ่ 24 ชม.</title>
    @include('partials.head')
</head>
<body class="app-bg min-h-screen text-slate-100 flex flex-col justify-between selection:bg-red-600 selection:text-white">
    <!-- Navbar -->
    <header class="border-b border-[#1e2430] bg-[#0a0c10]/90 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-18 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center font-bold text-white text-lg">
                    LP
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-xl text-white tracking-wide font-sans">
                            LETSPLAY <span class="text-red-500">GAMING CAFE</span>
                        </span>
                        <span class="badge-red text-[10px]">24 HRS</span>
                    </div>
                    <p class="text-[11px] text-zinc-400 -mt-1 hidden sm:block">Gaming Café & Internet Lounge</p>
                </div>
            </a>

            <!-- Search Bar Pill -->
            <div class="hidden md:flex flex-1 max-w-md mx-4">
                <div class="relative w-full">
                    <input type="text" placeholder="ค้นหาเกม, โซนคอม, กะเพรา, มาม่า, เลย์..." class="w-full bg-[#141824] border border-[#232938] rounded-full py-2 pl-10 pr-4 text-xs text-zinc-200 placeholder-zinc-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
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

                    <a href="{{ route('dashboard') }}" class="btn-primary text-xs py-2 px-4">
                        เข้าสู่แดชบอร์ด &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-medium text-zinc-300 hover:text-white transition">
                        เข้าสู่ระบบ
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary text-xs py-2 px-4">
                        สมัครสมาชิก
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex-1 space-y-10 w-full">
        <!-- Hero Promo Banner (Big Banner) -->
        <div class="relative overflow-hidden rounded-2xl border border-[#232938] bg-[#0c0f17] p-6 sm:p-10">
            <!-- Background Gaming Artwork (Valorant) -->
            <img
                src="https://cmsassets.rgpub.io/sanity/images/dsfx7636/news_live/4c679fc9b8f253d5915261338f81fe1043fded45-3440x1020.jpg?accountingTag=VAL&fit=fill&fm=jpg&q=80&h=1020"
                alt="Valorant Gaming Hero"
                class="absolute inset-0 w-full h-full object-cover object-right md:object-center opacity-40 select-none pointer-events-none"
                loading="eager"
            >
            <div class="absolute inset-0 bg-gradient-to-r from-[#0c0f17] via-[#0c0f17]/85 to-transparent pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge-red text-xs">
                        🔴 เปิดบริการ 24 ชั่วโมง • ศาลายา
                    </span>
                    <span class="badge-gold text-xs">
                        ⚡ เน็ตไฟเบอร์ 1000/1000 ปิงต่ำ 5ms
                    </span>
                    <span class="bg-blue-500/15 text-blue-400 border border-blue-500/30 text-xs px-2.5 py-0.5 rounded-full font-bold">
                        ❄️ แอร์เย็น 23°C ทั้งวันทั้งคืน
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    ร้านเกมสเปกท็อป จอ 240Hz แอร์เย็นฉ่ำ <br>
                    <span class="text-red-500">
                        พร้อมอาหารถึงโต๊ะ 24 ชม.
                    </span>
                </h1>

                <p class="text-sm sm:text-base text-zinc-300 leading-relaxed">
                    สัมผัสบรรยากาศร้านเกมยุคใหม่ย่านศาลายา สเปกแรงเล่นลื่นทุกเกม Valorant, GTA V, Apex Legends พร้อมสั่งข้าวกะเพรา มาม่าต้มยำ ขนมเลย์ และน้ำอัดลมเย็นเจี๊ยบส่งตรงถึงโต๊ะคอม ไม่ต้องลุกให้เสียจังหวะ
                </p>

                <div class="pt-2 flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ route('customer.seat-map') }}" class="btn-primary text-sm py-2 px-5">
                            🖥️ เลือกผังโต๊ะคอม
                        </a>
                        <a href="{{ route('customer.food-order') }}" class="card text-xs font-semibold px-4 py-2 text-zinc-300 hover:text-white border-[#262d3d] hover:border-red-500/50">
                            🍜 สั่งอาหาร & เครื่องดื่ม
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary text-sm py-2 px-5">
                            🚀 เข้าสู่ระบบเพื่อเปิดเครื่อง
                        </a>
                        <a href="{{ route('register') }}" class="card text-xs font-semibold px-4 py-2 text-zinc-300 hover:text-white border-[#262d3d] hover:border-red-500/50">
                            สมัครสมาชิกใหม่
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Quick Filter Category Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide text-sm">
            <button class="pill bg-red-600 text-white font-medium whitespace-nowrap">
                🔥 ยอดนิยมทั้งหมด
            </button>
            <a href="{{ route('customer.seat-map') }}" class="pill bg-[#131622] hover:bg-[#1a1f30] text-zinc-300 hover:text-white border border-[#232938] whitespace-nowrap">
                🖥️ ผังโซนคอมพิวเตอร์
            </a>
            <a href="{{ route('customer.food-order') }}" class="pill bg-[#131622] hover:bg-[#1a1f30] text-zinc-300 hover:text-white border border-[#232938] whitespace-nowrap">
                🍳 อาหารตามสั่ง & กะเพรา
            </a>
            <a href="{{ route('customer.food-order') }}" class="pill bg-[#131622] hover:bg-[#1a1f30] text-zinc-300 hover:text-white border border-[#232938] whitespace-nowrap">
                🍜 มาม่าต้มยำ & ไวไว
            </a>
            <a href="{{ route('customer.food-order') }}" class="pill bg-[#131622] hover:bg-[#1a1f30] text-zinc-300 hover:text-white border border-[#232938] whitespace-nowrap">
                🥔 ขนม เลย์ & ของทานเล่น
            </a>
            <a href="{{ route('customer.food-order') }}" class="pill bg-[#131622] hover:bg-[#1a1f30] text-zinc-300 hover:text-white border border-[#232938] whitespace-nowrap">
                🥤 นมสด ชาไทย & โค้ก
            </a>
            <a href="{{ route('customer.topup') }}" class="pill bg-[#131622] hover:bg-[#1a1f30] text-zinc-300 hover:text-white border border-[#232938] whitespace-nowrap">
                ⚡ แพ็กเกจชั่วโมงสุดคุ้ม
            </a>
        </div>

        <!-- Section 1: โซนที่นั่งเกมมิ่งยอดนิยม (PC Zones) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="step-header">
                    <span class="step-bar"></span>
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
                <div class="card p-5 flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="badge-red">STANDARD PC</span>
                            <span class="text-xs font-bold text-amber-400 bg-amber-400/10 px-2.5 py-0.5 rounded-full border border-amber-400/20">
                                ยอดนิยม • จอ 165Hz
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-white group-hover:text-red-400 transition-colors">
                            โซนคอมมาตรฐาน (Standard Zone)
                        </h3>
                        <p class="text-xs text-zinc-400 leading-relaxed">
                            Intel Core i5-13400F, RTX 4060 8GB, RAM 32GB, จอ 24.5" 165Hz Fast IPS, คีย์บอร์ด Mechanical, เก้าอี้เกมมิ่งระบายอากาศ เล่นลื่นทุกเกม FPS
                        </p>
                    </div>

                    <div class="pt-4 border-t border-[#1e2430] mt-4 flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-black text-white">฿40</span>
                            <span class="text-xs text-zinc-400">/ 1 ชม.</span>
                        </div>
                        <a href="{{ route('customer.seat-map') }}" class="btn-primary text-xs py-2 px-3.5">
                            เลือกโต๊ะนี้
                        </a>
                    </div>
                </div>

                <!-- VIP Lounge Zone -->
                <div class="card p-5 flex flex-col justify-between group border-red-500/20">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="badge-gold">VIP LOUNGE</span>
                            <span class="text-xs font-bold text-rose-400 bg-rose-500/10 px-2.5 py-0.5 rounded-full border border-rose-500/20">
                                ตัวแรง • จอ 240Hz
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-white group-hover:text-red-400 transition-colors">
                            ห้องวีไอพี เลานจ์ (VIP Lounge)
                        </h3>
                        <p class="text-xs text-zinc-400 leading-relaxed">
                            Intel Core i7-14700KF, RTX 4070 Ti 12GB, RAM 32GB DDR5, จอโค้ง 27" 240Hz 0.5ms, คีย์บอร์ด Custom Linear, หูฟัง HyperX 7.1 รอบทิศทาง
                        </p>
                    </div>

                    <div class="pt-4 border-t border-[#1e2430] mt-4 flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-black text-white">฿60</span>
                            <span class="text-xs text-zinc-400">/ 1 ชม.</span>
                        </div>
                        <a href="{{ route('customer.seat-map') }}" class="btn-primary text-xs py-2 px-3.5">
                            เลือกโต๊ะนี้
                        </a>
                    </div>
                </div>

                <!-- Pro Streamer Zone -->
                <div class="card p-5 flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="badge-red">PRO STREAMER</span>
                            <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
                                สตูดิโอเก็บเสียง
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-white group-hover:text-red-400 transition-colors">
                            ห้องสตรีมเมอร์ส่วนตัว (Pro Studio)
                        </h3>
                        <p class="text-xs text-zinc-400 leading-relaxed">
                            Intel Core i9-14900K, RTX 4090 24GB, RAM 64GB, จอคู่ 280Hz, ไมค์สตูดิโอ Shure, กล้อง Sony 4K, ไฟ Elgato, ห้องกระจกเก็บเสียงเป็นส่วนตัว
                        </p>
                    </div>

                    <div class="pt-4 border-t border-[#1e2430] mt-4 flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-black text-white">฿100</span>
                            <span class="text-xs text-zinc-400">/ 1 ชม.</span>
                        </div>
                        <a href="{{ route('customer.seat-map') }}" class="btn-primary text-xs py-2 px-3.5">
                            เลือกโต๊ะนี้
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: อาหารตามสั่ง มาม่า & ขนมเครื่องดื่ม (Thai Cyber Cafe Food & Snacks) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="step-header">
                    <span class="step-bar"></span>
                    <h2 class="text-xl font-black text-white tracking-wide font-sans">
                        เมนูยอดฮิตประจำร้านเกม เสิร์ฟร้อนถึงโต๊ะ (Food & Snacks)
                    </h2>
                </div>
                <a href="{{ route('customer.food-order') }}" class="text-xs font-semibold text-red-400 hover:text-red-300">
                    ดูเมนูทั้งหมด &rarr;
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <!-- Mama Tom Yum -->
                <div class="card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-red-950 to-zinc-900 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            🍜
                        </div>
                        <span class="badge-red text-[10px]">ฮิตตลอดกาล</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">มาม่าต้มยำหมูสับใส่ไข่</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">ต้มยำน้ำข้น หมูสับลวก ไข่เยิ้มๆ</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿45</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>

                <!-- Kaprao Kai Dao -->
                <div class="card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-amber-950 to-zinc-900 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            🍳
                        </div>
                        <span class="badge-gold text-[10px]">ขายดีอันดับ 1</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">ข้าวกะเพราหมูสับไข่ดาว</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">ผัดกะเพราแท้ โปะไข่ดาวกรอบ</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿59</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>

                <!-- Moo Kratiem -->
                <div class="card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-zinc-800 to-zinc-900 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            🍛
                        </div>
                        <span class="badge-red text-[10px]">เมนูโปรด</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">ข้าวหมูกระเทียมพริกไทย</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">หมูผัดกระเทียมหอมเจียวกรอบ</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿59</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>

                <!-- Lay's Chips -->
                <div class="card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-emerald-950 to-zinc-900 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            🥔
                        </div>
                        <span class="badge-gold text-[10px]">ขนมยอดฮิต</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">เลย์ โนริสาหร่าย (ซองใหญ่)</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">มันฝรั่งแท้กรอบอร่อยเพลิน</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿30</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>

                <!-- Red Sausage -->
                <div class="card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-rose-950 to-zinc-900 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            🌭
                        </div>
                        <span class="badge-red text-[10px]">ของทอดในตำนาน</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">ไส้กรอกแดงทอดกรอบ</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">บั้งทอดพอง จิ้มน้ำจิ้มมะขาม</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿35</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>

                <!-- Thai Tea & Cold Drinks -->
                <div class="card p-3.5 flex flex-col justify-between group">
                    <div class="space-y-2">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-br from-amber-950 to-orange-950 border border-[#232938] flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                            🧋
                        </div>
                        <span class="badge-gold text-[10px]">เย็นชื่นใจ</span>
                        <h4 class="font-bold text-xs text-white line-clamp-1 group-hover:text-red-400">ชาไทยเย็น / นมสดคาราเมล</h4>
                        <p class="text-[11px] text-zinc-400 line-clamp-1">แก้วใหญ่ 22 ออนซ์ หวานมัน</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-[#1e2430] flex items-center justify-between">
                        <span class="font-black text-sm text-white">฿35</span>
                        <a href="{{ route('customer.food-order') }}" class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center text-xs font-bold transition">
                            +
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: ข้อมูลบัญชีสำหรับทดสอบระบบ (Demo Accounts Box) -->
        <div class="card p-6 rounded-xl space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#232938] pb-3">
                <div class="flex items-center gap-2">
                    <span class="step-bar"></span>
                    <h3 class="font-bold text-base text-white font-sans">
                        บัญชีสำหรับทดสอบระบบร้านเกม (Seeded Accounts)
                    </h3>
                </div>
                <span class="badge-gold text-xs font-mono">
                    🔑 รหัสผ่านทุกบัญชี: password
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 bg-[#141824] border border-[#232938] rounded-2xl space-y-1 hover:border-red-500/40 transition">
                    <span class="badge-red text-[10px]">
                        CUSTOMER 1 (ลูกค้า)
                    </span>
                    <p class="text-sm font-bold text-white mt-1">Username: customer1</p>
                    <p class="text-xs text-zinc-400 font-mono">Email: cust1@pcbang.test</p>
                    <p class="text-xs text-emerald-400 font-semibold">ยอดเงินเริ่มต้น: ฿300.00</p>
                </div>

                <div class="p-4 bg-[#141824] border border-[#232938] rounded-2xl space-y-1 hover:border-amber-500/40 transition">
                    <span class="badge-gold text-[10px]">
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

        <!-- Section 4: บริการเด่นของ Letsplay Gaming Cafe (Cafe Perks) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="card p-5 space-y-2">
                <div class="w-10 h-10 rounded-xl bg-red-600/20 text-red-500 flex items-center justify-center font-black text-lg border border-red-500/30">
                    ⚡
                </div>
                <h4 class="font-bold text-sm text-white">เน็ตไฟเบอร์ 1000/1000 สำหรับเกมเมอร์โดยเฉพาะ</h4>
            </div>

            <div class="card p-5 space-y-2">
                <div class="w-10 h-10 rounded-xl bg-amber-600/20 text-amber-500 flex items-center justify-center font-black text-lg border border-amber-500/30">
                    🍜
                </div>
                <h4 class="font-bold text-sm text-white">พร้อมเสิร์ฟถึงหน้าจอคอมตลอด 24 ชม.</h4>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    สั่งอาหาร ขนม นม เนย น้ำ ได้จากหน้าจอ
                </p>
            </div>

            <div class="card p-5 space-y-2">
                <div class="w-10 h-10 rounded-xl bg-blue-600/20 text-blue-500 flex items-center justify-center font-black text-lg border border-blue-500/30">
                    ❄️
                </div>
                <h4 class="font-bold text-sm text-white">แอร์เย็นเจี๊ยบพร้อมเก้าอี้ Ergonomic</h4>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    ร้านสะอาดพร้อมเก้าอี้เบาะหนานุ่มรองรับสรีระ เล่นยาวข้ามคืนได้สบายหลัง
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-[#1e2430] bg-[#07090d] py-10 mt-12 text-xs text-zinc-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-600 flex items-center justify-center font-black text-white text-sm">
                        LP
                    </div>
                    <span class="font-extrabold text-base text-white">
                        Letsplay <span class="text-red-500">Gaming Cafe</span>
                    </span>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-4 text-xs text-zinc-400">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> พร้อมเพย์ QR</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> ทรูมันนี่ วอลเล็ท</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> เงินสดหน้าเคาน์เตอร์</span>
                </div>
            </div>

            <div class="border-t border-[#1e2430] pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-zinc-500">
                <p>&copy; gaming cafe</p>

            </div>
        </div>
    </footer>
</body>
</html>
