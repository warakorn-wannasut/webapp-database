<!DOCTYPE html>
<html lang="th" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Café Management System</title>
    @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100 flex flex-col justify-between">
    <!-- Navbar -->
    <header class="border-b border-zinc-800 bg-zinc-900/80 backdrop-blur sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center font-extrabold text-white text-base shadow-lg shadow-indigo-600/30">
                    GC
                </div>
                <span class="font-extrabold text-lg text-white tracking-wide">
                    Gaming Café <span class="text-xs px-2 py-0.5 rounded bg-indigo-950 text-indigo-400 border border-indigo-800">PC Bang Style</span>
                </span>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow">
                        เข้าสู่แดชบอร์ด &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-zinc-300 hover:text-white transition">
                        เข้าสู่ระบบ
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow">
                        สมัครสมาชิก
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero & Demo Credentials Banner -->
    <main class="max-w-6xl mx-auto px-6 py-12 flex-1 space-y-12">
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <h1 class="text-4xl sm:text-5xl font-black tracking-tight text-white">
                ระบบจัดการร้านเกมและอาหารสไตล์เกาหลี
            </h1>
            <p class="text-base text-zinc-400">
                ระบบครบวงจร: เช็คอินผังที่นั่ง, คิดเงินตามเวลาจริงหรือแพ็กเกจชั่วโมง, สั่งอาหารและรามยอนส่งตรงถึงโต๊ะ พร้อมคิวห้องครัวและแดชบอร์ดผู้ดูแลร้าน
            </p>

            <div class="pt-2 flex justify-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-6 py-3 text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-lg shadow-indigo-600/30">
                        ไปยังระบบจัดการร้าน (Dashboard)
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-3 text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-lg shadow-indigo-600/30">
                        เข้าสู่ระบบทดสอบ
                    </a>
                @endauth
            </div>
        </div>

        <!-- Demo Accounts Box -->
        <div class="p-6 bg-zinc-900 border border-zinc-800 rounded-2xl max-w-4xl mx-auto shadow-xl space-y-4">
            <div class="border-b border-zinc-800 pb-2 flex justify-between items-center">
                <h3 class="font-bold text-sm text-zinc-300">บัญชีสำหรับทดสอบระบบ (Seeded Accounts):</h3>
                <span class="text-xs text-emerald-400 font-mono">รหัสผ่านทั้งหมด: password</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 bg-zinc-950 border border-zinc-800 rounded-xl space-y-1">
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-blue-950 text-blue-400 border border-blue-800">
                        CUSTOMER 1
                    </span>
                    <p class="text-sm font-bold text-white mt-1">customer1</p>
                    <p class="text-xs text-zinc-400 font-mono">cust1@pcbang.test</p>
                    <p class="text-xs text-emerald-400">ยอดเงินเริ่มต้น: ฿300.00</p>
                </div>

                <div class="p-4 bg-zinc-950 border border-zinc-800 rounded-xl space-y-1">
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-amber-950 text-amber-400 border border-amber-800">
                        STAFF
                    </span>
                    <p class="text-sm font-bold text-white mt-1">staff</p>
                    <p class="text-xs text-zinc-400 font-mono">staff@pcbang.test</p>
                    <p class="text-xs text-zinc-400">คิวครัว + มอนิเตอร์โต๊ะ</p>
                </div>

                <div class="p-4 bg-zinc-950 border border-zinc-800 rounded-xl space-y-1">
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-purple-950 text-purple-400 border border-purple-800">
                        ADMIN
                    </span>
                    <p class="text-sm font-bold text-white mt-1">admin</p>
                    <p class="text-xs text-zinc-400 font-mono">admin@pcbang.test</p>
                    <p class="text-xs text-zinc-400">จัดการสต็อก + สรุปยอดขาย</p>
                </div>
            </div>
        </div>

        <!-- Features Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-zinc-900/60 border border-zinc-800 rounded-2xl space-y-2">
                <div class="w-10 h-10 rounded-xl bg-indigo-950 text-indigo-400 flex items-center justify-center font-black">
                    01
                </div>
                <h4 class="font-bold text-base text-white">ระบบผังที่นั่ง & เช็คอิน</h4>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    แบ่งโซนชัดเจน (Standard ฿40/ชม., VIP ฿60/ชม., Duo Room ฿100/ชม.) รองรับทั้งเล่นคิดตามจริงจาก Wallet หรือใช้แพ็กเกจชั่วโมงสะสม
                </p>
            </div>

            <div class="p-6 bg-zinc-900/60 border border-zinc-800 rounded-2xl space-y-2">
                <div class="w-10 h-10 rounded-xl bg-amber-950 text-amber-400 flex items-center justify-center font-black">
                    02
                </div>
                <h4 class="font-bold text-base text-white">ครัวเกาหลี & สั่งอาหารถึงโต๊ะ</h4>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    รามยอน, ข้าวผัดกิมจิ, ต๊อกบกกี ตัดสต็อกสินค้าแบบ Atomic พร้อมช่องทางชำระเงินทั้ง Wallet, QR PromptPay และเงินสดปลายทาง
                </p>
            </div>

            <div class="p-6 bg-zinc-900/60 border border-zinc-800 rounded-2xl space-y-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-950 text-emerald-400 flex items-center justify-center font-black">
                    03
                </div>
                <h4 class="font-bold text-base text-white">แดชบอร์ดจัดการร้าน</h4>
                <p class="text-xs text-zinc-400 leading-relaxed">
                    มอนิเตอร์เวลาที่นั่งแบบเรียลไทม์ (Polling 5s), คิวออเดอร์ครัว, ยืนยันรับเงินสด, ปรับสต็อกสินค้า และรายงานสรุปยอดขายผ่าน Aggregate Queries
                </p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-zinc-800 py-6 text-center text-xs text-zinc-500">
        Gaming Café Management System &bull; พัฒนาสำหรับวิชา Web App และ Database (3NF & Oracle SQL Portable)
    </footer>
</body>
</html>
