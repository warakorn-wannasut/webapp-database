# คำอธิบายโค้ดทุกบรรทัดใน DashboardController.php

เอกสารนี้อธิบายการทำงานของไฟล์ [app/Http/Controllers/DashboardController.php](file:///e:/WEBAPPdatabase/game-center/app/Http/Controllers/DashboardController.php) (จำนวน 272 บรรทัด) อย่างละเอียดทุกบรรทัด ตามกฎและข้อกำหนดการเรียนรู้ระบบงาน MVC บน Laravel Framework

---

## 1. การทำงานภาพรวมและความหมายของแต่ละส่วนใน Laravel

* **Route:** จุดรับและกระจายคำขอ HTTP (GET, POST) จากเว็บเบราว์เซอร์ กำหนดไว้ใน [routes/web.php](file:///e:/WEBAPPdatabase/game-center/routes/web.php) ทำหน้าที่เลือกว่าจะส่งคำขอไปให้ Controller ตัวไหนและ Method ใดทำงาน
* **Controller:** ตัวประสานงานตรรกะทางธุรกิจ (Business Logic) รับคำขอจาก Route ดึงหรือบันทึกข้อมูลผ่าน Model คำนวณยอดเงินและเวลา แล้วส่งผลลัพธ์ไปเรนเดอร์ที่ Blade View
* **Model:** คลาสตัวแทนของตารางในฐานข้อมูล SQLite ทำงานผ่านระบบ Eloquent ORM ทำให้สั่งงานฐานข้อมูลในรูปแบบ Object ได้โดยไม่ต้องเขียนคำสั่ง SQL ดั้งเดิม
* **Blade View:** ไฟล์หน้าจอแสดงผล HTML ที่มีนามสกุล `.blade.php` มีระบบ Template Engine ช่วยจัดการแสดงผลข้อมูลและวนลูป
* **Middleware:** ตัวกรองคำขอก่อนถึง Controller เช่น `auth` ที่ตรวจว่าผู้ใช้ล็อกอินหรือยัง

### เส้นทางการส่งข้อมูลข้าม Layer (Data Flow)

1. **การโหลดหน้าแดชบอร์ด:**
   > ผู้ใช้เปิดหน้าเว็บ -> GET `/dashboard` -> `routes/web.php` -> ผ่านด่าน `auth` middleware -> เรียก `DashboardController@index` -> ดึงข้อมูลจาก Model [User](file:///e:/WEBAPPdatabase/game-center/app/Models/User.php), [SeatSession](file:///e:/WEBAPPdatabase/game-center/app/Models/SeatSession.php), [UserPackage](file:///e:/WEBAPPdatabase/game-center/app/Models/UserPackage.php), [WalletTransaction](file:///e:/WEBAPPdatabase/game-center/app/Models/WalletTransaction.php), [Order](file:///e:/WEBAPPdatabase/game-center/app/Models/Order.php) -> รวมตัวแปรผ่าน `compact(...)` -> เรนเดอร์หน้าจอ [resources/views/pages/customer/dashboard.blade.php](file:///e:/WEBAPPdatabase/game-center/resources/views/pages/customer/dashboard.blade.php)

2. **การกดปุ่มเช็คเอาท์:**
   > ผู้ใช้กดปุ่มเช็คเอาท์ -> ฟอร์มส่ง POST `/check-out` พร้อม `@csrf` -> `routes/web.php` -> เรียก `DashboardController@checkOut` -> ตรวจสอบ `$session` ที่กำลังเล่น -> คำนวณเวลาและตัดเงินใน `$user->balance` หรือลดเวลาใน `$userPackage->remaining_minutes` -> เปลี่ยนสถานะ `$session->status = 'completed'` -> คืนสถานะเครื่อง `$seat->status = 'available'` -> บันทึกประวัติใน [WalletTransaction](file:///e:/WEBAPPdatabase/game-center/app/Models/WalletTransaction.php) -> สั่ง `redirect()->back()->with('success', ...)` กลับมาแสดงข้อความสำเร็จที่หน้าแดชบอร์ด

---

## 2. ข้อมูล Model ทั้ง 6 ตัวที่ Controller นี้เรียกใช้

1. **[User](file:///e:/WEBAPPdatabase/game-center/app/Models/User.php):**
   * ชื่อตาราง: `users`
   * ฟิลด์ที่กรอกได้ (`$fillable`): `name`, `username`, `email`, `phone`, `role`, `balance`, `password`
   * ความสัมพันธ์: `hasMany(WalletTransaction::class)`, `hasMany(UserPackage::class)`
2. **[SeatSession](file:///e:/WEBAPPdatabase/game-center/app/Models/SeatSession.php):**
   * ชื่อตาราง: `seat_sessions`
   * ฟิลด์ที่กรอกได้ (`$fillable`): `user_id`, `seat_id`, `user_package_id`, `rate_snapshot`, `start_time`, `end_time`, `total_cost`, `status`
   * ความสัมพันธ์: `belongsTo(User::class)`, `belongsTo(Seat::class)`, `belongsTo(UserPackage::class)`, `hasMany(Order::class, 'session_id')`
3. **[Seat](file:///e:/WEBAPPdatabase/game-center/app/Models/Seat.php):**
   * ชื่อตาราง: `seats`
   * ฟิลด์ที่กรอกได้ (`$fillable`): `zone_id`, `seat_number`, `status`
   * ความสัมพันธ์: `belongsTo(Zone::class)`, `hasMany(SeatSession::class)`, `hasMany(Order::class)`
4. **[UserPackage](file:///e:/WEBAPPdatabase/game-center/app/Models/UserPackage.php):**
   * ชื่อตาราง: `user_packages`
   * ฟิลด์ที่กรอกได้ (`$fillable`): `user_id`, `package_id`, `remaining_minutes`, `purchased_at`, `expired_at`
   * ความสัมพันธ์: `belongsTo(User::class)`, `belongsTo(Package::class)`, `hasMany(SeatSession::class)`
5. **[WalletTransaction](file:///e:/WEBAPPdatabase/game-center/app/Models/WalletTransaction.php):**
   * ชื่อตาราง: `wallet_transactions`
   * ฟิลด์ที่กรอกได้ (`$fillable`): `user_id`, `type`, `amount`, `ref_type`, `ref_id`
   * ความสัมพันธ์: `belongsTo(User::class)`
6. **[Order](file:///e:/WEBAPPdatabase/game-center/app/Models/Order.php):**
   * ชื่อตาราง: `orders`
   * ฟิลด์ที่กรอกได้ (`$fillable`): `user_id`, `seat_id`, `session_id`, `total_amount`, `payment_method`, `payment_status`, `order_status`
   * ความสัมพันธ์: `belongsTo(User::class)`, `belongsTo(Seat::class)`, `belongsTo(SeatSession::class, 'session_id')`, `hasMany(OrderItem::class)`

---

## 3. สูตรคำนวณเงินและตัวอย่างตัวเลขจริง

### สูตรที่ 1: การแปลงเวลาเป็นนาที (ปัดเศษขึ้นเสมอ)
$$\text{elapsedMinutes} = \lceil \frac{\text{usedSeconds}}{60} \rceil$$
* **ตัวอย่าง:** เริ่มเล่น 14:00:00 น. ปัจจุบัน 14:15:20 น. (ใช้ไป 920 วินาที)
  $$\frac{920}{60} = 15.33 \rightarrow \text{ปัดขึ้นเป็น } 16 \text{ นาที}$$

### สูตรที่ 2: โหมดคิดตามจริง (Pay-as-you-go)
$$\text{Cost} = \text{round}\left( \left(\frac{\text{usedMinutes}}{60}\right) \times \text{rate\_snapshot}, 2 \right)$$
* **ตัวอย่าง:** เครื่องโซน Standard ราคาชั่วโมงละ 60.00 บาท เล่นไป 45 นาที
  $$\text{Cost} = \frac{45}{60} \times 60.00 = 0.75 \times 60.00 = 45.00 \text{ บาท}$$
  * หักเงินจากกระเป๋าผู้ใช้ 45.00 บาท

### สูตรที่ 3: โหมดใช้แพ็กเกจชั่วโมง (Package Mode)
* **กรณีไม่เกินเวลา:** ถ้าเล่นไป 50 นาที และแพ็กเกจมี 120 นาที
  * เวลาแพ็กเกจคงเหลือใหม่: $120 - 50 = 70$ นาที
  * ค่าบริการส่วนเพิ่ม: 0.00 บาท
* **กรณีเกินเวลา (Overtime):** ถ้าเล่นไป 90 นาที แต่แพ็กเกจมีเหลือแค่ 60 นาที (เกินมา 30 นาที) โซนราคา 60.00 บาท/ชม.
  * เวลาแพ็กเกจคงเหลือใหม่: 0 นาที
  * ค่าบริการส่วนเกิน: $(30 / 60) \times 60.00 = 30.00$ บาท
  * หักเงินสดจาก Wallet เพิ่ม 30.00 บาท

### สูตรที่ 4: ยอดเงินคงเหลือที่ใช้สั่งอาหารได้จริง (`availableBalance`)
$$\text{availableBalance} = \max(0.00, \text{balance} - \text{estimatedCost})$$
* **ตัวอย่าง:** ผู้ใช้มีเงินในกระเป๋า 200.00 บาท กำลังเปิดเครื่องเล่นแบบ Pay-as-you-go ไปแล้ว 60 นาที (ค่าเครื่องสะสม 60.00 บาท)
  $$\text{availableBalance} = 200.00 - 60.00 = 140.00 \text{ บาท}$$
  * ผู้ใช้จะสั่งอาหารได้ไม่เกิน 140.00 บาท เพื่อป้องกันเงินไม่พอจ่ายค่าเครื่อง

---

## 4. ตารางอธิบายโค้ดทุกบรรทัดตั้งแต่บรรทัดที่ 1 ถึง 272

### ส่วนที่ 1: ส่วนหัวไฟล์ การประกาศ Namespace และการนำเข้าคลาส (บรรทัดที่ 1-16)

| บรรทัด | โค้ดในไฟล์ | หน้าที่และการทำงาน | ตัวแปร: ที่มา / ชนิดและค่าตัวอย่าง / ส่งต่อไปไหน |
| :---: | :--- | :--- | :--- |
| 1 | `<?php` | แท็กเปิดภาษา PHP สำหรับแจ้งให้เซิร์ฟเวอร์ทราบว่าโค้ดต่อจากนี้คือภาษา PHP | ไม่มีตัวแปร |
| 2 | *(บรรทัดว่าง)* | เว้นวรรคเพื่อความเป็นระเบียบในการอ่านโค้ด | ไม่มีตัวแปร |
| 3 | `namespace App\Http\Controllers;` | กำหนด Namespace เพื่อจัดกลุ่มไฟล์นี้ว่าอยู่ในโฟลเดอร์ `app/Http/Controllers` ป้องกันชื่อคลาสชนกับไฟล์อื่น | ไม่มีตัวแปร |
| 4 | *(บรรทัดว่าง)* | เว้นวรรคแบ่งกลุ่มคำสั่ง | ไม่มีตัวแปร |
| 5 | `use App\Models\Order;` | นำเข้าคลาส Model Order เข้ามาใช้งานในไฟล์นี้ | นำเข้าคลาส |
| 6 | `use App\Models\Seat;` | นำเข้าคลาส Model Seat เข้ามาใช้งานในไฟล์นี้ | นำเข้าคลาส |
| 7 | `use App\Models\SeatSession;` | นำเข้าคลาส Model SeatSession เข้ามาใช้งานในไฟล์นี้ | นำเข้าคลาส |
| 8 | `use App\Models\User;` | นำเข้าคลาส Model User เข้ามาใช้งานในไฟล์นี้ | นำเข้าคลาส |
| 9 | `use App\Models\UserPackage;` | นำเข้าคลาส Model UserPackage เข้ามาใช้งานในไฟล์นี้ | นำเข้าคลาส |
| 10 | `use App\Models\WalletTransaction;` | นำเข้าคลาส Model WalletTransaction เข้ามาใช้งานในไฟล์นี้ | นำเข้าคลาส |
| 11 | `use Illuminate\Http\Request;` | นำเข้าคลาส Request ของ Laravel สำหรับอ่านข้อมูลที่ส่งมาจาก HTTP Request | นำเข้าคลาส |
| 12 | `use Illuminate\Support\Carbon;` | นำเข้าคลาส Carbon สำหรับจัดการและคำนวณวันเวลา | นำเข้าคลาส |
| 13 | `use Illuminate\Support\Facades\Auth;` | นำเข้า Facade Auth สำหรับจัดการและตรวจสอบข้อมูลผู้ใช้ที่กำลังล็อกอิน | นำเข้าคลาส |
| 14 | *(บรรทัดว่าง)* | เว้นวรรคแบ่งกลุ่มคำสั่ง | ไม่มีตัวแปร |
| 15 | `class DashboardController extends Controller` | ประกาศคลาสชื่อ DashboardController โดยสืบทอดคุณสมบัติ (extends) มาจากคลาสแม่ Controller | ประกาศคลาส |
| 16 | `{` | เครื่องหมายปีกกาเปิดขอบเขตการทำงานของคลาส DashboardController | ขอบเขตคลาส |

---

### ส่วนที่ 2: Method `index()` (บรรทัดที่ 17-87)
* **หน้าที่:** แสดงหน้าแดชบอร์ดหลักของลูกค้า คำนวณเวลาและค่าเครื่องปัจจุบัน พร้อมดึงสรุปข้อมูล
* **รับอะไรเข้ามา:** ไม่มีพารามิเตอร์ (ดึงข้อมูลผู้ใช้จาก `Auth::user()`)
* **คืนค่าอะไร:** แสดงผลหน้า View `pages.customer.dashboard` พร้อมส่งตัวแปร 8 ตัวผ่าน `compact(...)`
* **ถูกเรียกจาก Route ไหน:** `GET /dashboard` (ชื่อ route: `dashboard`) ใน [routes/web.php](file:///e:/WEBAPPdatabase/game-center/routes/web.php)
* **แตะตารางไหนในฐานข้อมูล:** `users`, `seat_sessions`, `seats`, `zones`, `user_packages`, `packages`, `wallet_transactions`, `orders`

| บรรทัด | โค้ดในไฟล์ | หน้าที่และการทำงาน | ตัวแปร: ที่มา / ชนิดและค่าตัวอย่าง / ส่งต่อไปไหน |
| :---: | :--- | :--- | :--- |
| 17 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 18 | `    //  แสดงหน้าแดชบอร์ดหลักของลูกค้า` | คอมเมนต์อธิบายหน้าที่ของ Method ด้านล่าง | ไม่มีตัวแปร |
| 19 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 20 | `    public function index()` | ประกาศ Method index แบบ Public เพื่อให้ Route เรียกเข้ามาทำงานได้ | ไม่มีพารามิเตอร์ |
| 21 | `    {` | ปีกกาเปิดขอบเขตของ Method index | ขอบเขต Method |
| 22 | `        $user = Auth::user();` | ดึงข้อมูลผู้ใช้ที่กำลังล็อกอินอยู่ใน Session ปัจจุบัน | `$user`:<br>- ที่มา: `Auth::user()` ของ Laravel<br>- ชนิด: Model User เช่น `id: 3, name: 'Somchai'`<br>- ส่งต่อไป: บรรทัด 29, 60, 64, 74, 75, 78 |
| 23 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 24 | `        // 1. ตรวจสอบและตัดจบเซสชันที่เวลาหรือเงินหมดอัตโนมัติ` | คอมเมนต์ขั้นตอนที่ 1 | ไม่มีตัวแปร |
| 25 | `        $this->autoEndExpiredSessions();` | เรียกฟังก์ชันภายในคลาส เพื่อตัดจบเซสชันของเครื่องที่เงินหมดก่อนคำนวณข้อมูลใหม่ | `$this`: อ้างอิงอ็อบเจกต์ Controller ปัจจุบัน |
| 26 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 27 | `        // 2. ดึงข้อมูลเครื่องที่กำลังเปิดใช้งานอยู่` | คอมเมนต์ขั้นตอนที่ 2 | ไม่มีตัวแปร |
| 28 | `        $activeSession = SeatSession::with(['seat.zone', 'userPackage.package'])` | เริ่มต้น Query ตาราง `seat_sessions` พร้อมทำ Eager Loading โหลดตารางลูก `seats`, `zones`, `user_packages`, และ `packages` มาพร้อมกัน | คลาส `SeatSession` |
| 29 | `            ->where('user_id', $user->id)` | เพิ่มเงื่อนไข SQL: เฉพาะเซสชันของผู้ใช้คนนี้ (`WHERE user_id = ?`) | ตัวแปร `$user->id` ชนิด `int` |
| 30 | `            ->where('status', 'active')` | เพิ่มเงื่อนไข SQL: เฉพาะเซสชันที่ยังเปิดเล่นอยู่ (`WHERE status = 'active'`) | สตริง `'active'` |
| 31 | `            ->latest()` | เพิ่มเงื่อนไข SQL: เรียงลำดับจากรายการใหม่สุดมาก่อน (`ORDER BY created_at DESC`) | คำสั่ง Query Builder |
| 32 | `            ->first();` | สั่งยิงคำสั่ง SQL และหยิบเรคคอร์ดแรกขึ้นมา | `$activeSession`:<br>- ที่มา: ตาราง `seat_sessions`<br>- ชนิด: Model SeatSession หรือ `null`<br>- ส่งต่อไป: บรรทัด 38, 39, 46, 51, 55, 79 |
| 33 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 34 | `        // 3. คำนวณเวลาและค่าบริการที่ใช้ไปในเซสชันปัจจุบัน` | คอมเมนต์ขั้นตอนที่ 3 | ไม่มีตัวแปร |
| 35 | `        $elapsedMinutes = 0;` | กำหนดค่าตัวแปรเริ่มต้นของจำนวนนาทีที่เล่นไปเป็น 0 | `$elapsedMinutes`:<br>- ชนิด: `int` ค่าเริ่มต้น `0`<br>- ส่งต่อไป: บรรทัด 41, 48, 54, 80 |
| 36 | `        $estimatedCost = 0.00;` | กำหนดค่าตัวแปรเริ่มต้นของค่าบริการสะสมเป็น 0.00 | `$estimatedCost`:<br>- ชนิด: `float` ค่าเริ่มต้น `0.00`<br>- ส่งต่อไป: บรรทัด 51, 55, 81 |
| 37 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 38 | `        if ($activeSession != null) {` | ตรวจสอบว่าผู้ใช้มีเครื่องที่กำลังเปิดเล่นอยู่จริงหรือไม่ | ตรวจสอบ `$activeSession` |
| 39 | `            $startTime = Carbon::parse($activeSession->start_time);` | แปลงเวลาเริ่มเล่นให้อยู่ในรูปของ Carbon Object เพื่อใช้ฟังก์ชันคำนวณเวลา | `$startTime`:<br>- ที่มา: ฟิลด์ `start_time`<br>- ชนิด: Carbon Object<br>- ส่งต่อไป: บรรทัด 40 |
| 40 | `            $usedSeconds = $startTime->diffInSeconds(Carbon::now());` | คำนวณหาผลต่างเวลาระหว่างเวลาเริ่มเล่นกับเวลาปัจจุบัน ออกมาเป็นวินาที | `$usedSeconds`:<br>- ที่มา: คำนวณผ่าน Carbon<br>- ชนิด: `int` เช่น `1800`<br>- ส่งต่อไป: บรรทัด 41 |
| 41 | `            $elapsedMinutes = (int) ceil($usedSeconds / 60);` | นำวินาทีมาหาร 60 เพื่อแปลงเป็นนาที และใช้ `ceil` ปัดเศษขึ้นเสมอ | `$elapsedMinutes`:<br>- ชนิด: `int` เช่น `30`<br>- ส่งต่อไป: บรรทัด 42, 48, 54, 80 |
| 42 | `            if ($elapsedMinutes < 1) {` | ตรวจสอบว่าจำนวนนาทีน้อยกว่า 1 หรือไม่ (เพิ่งเปิดเครื่องไม่กี่วินาที) | ตรวจสอบ `$elapsedMinutes` |
| 43 | `                $elapsedMinutes = 1;` | ถ้าเล่นน้อยกว่า 1 นาที ให้คิดเวลาขั้นต่ำเป็น 1 นาที | กำหนดค่า 1 ให้ `$elapsedMinutes` |
| 44 | `            }` | ปิดเงื่อนไขขั้นต่ำ | ขอบเขตเงื่อนไข |
| 45 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 46 | `            if ($activeSession->user_package_id != null && $activeSession->userPackage != null) {` | ตรวจสอบว่าเซสชันนี้ใช้แพ็กเกจชั่วโมงเล่นหรือไม่ | ตรวจสอบฟิลด์ `user_package_id` และ relation `userPackage` |
| 47 | `                $packageMins = $activeSession->userPackage->remaining_minutes;` | อ่านจำนวนนาทีคงเหลือของแพ็กเกจนั้น | `$packageMins`:<br>- ที่มา: ฟิลด์ `remaining_minutes`<br>- ชนิด: `int` เช่น `60`<br>- ส่งต่อไป: บรรทัด 48, 49 |
| 48 | `                if ($elapsedMinutes > $packageMins) {` | ตรวจสอบว่าเวลาที่เล่นไป เกินเวลานาทีในแพ็กเกจหรือไม่ | เปรียบเทียบ `$elapsedMinutes` กับ `$packageMins` |
| 49 | `                    $excessMinutes = $elapsedMinutes - $packageMins;` | คำนวณนาทีส่วนเกินที่เล่นเกินเวลาแพ็กเกจ | `$excessMinutes`:<br>- ชนิด: `int` นาทีส่วนเกิน<br>- ส่งต่อไป: บรรทัด 50 |
| 50 | `                    $excessHours = $excessMinutes / 60;` | แปลงนาทีส่วนเกินให้เป็นทศนิยมของชั่วโมง | `$excessHours`:<br>- ชนิด: `float`<br>- ส่งต่อไป: บรรทัด 51 |
| 51 | `                    $estimatedCost = round($excessHours * (float) $activeSession->rate_snapshot, 2);` | คำนวณเงินค่าชั่วโมงส่วนเกินตามอัตราโซน และปัดเศษทศนิยม 2 ตำแหน่ง | `$estimatedCost`:<br>- ชนิด: `float` เช่น `15.00`<br>- ส่งต่อไป: บรรทัด 81 |
| 52 | `                }` | ปิดเงื่อนไขตรวจสอบเวลาเกิน | ขอบเขตเงื่อนไข |
| 53 | `            } else {` | กรณีไม่ได้ใช้แพ็กเกจ (เป็นการเล่นแบบจ่ายตามจริง Pay-as-you-go) | ทางเลือกเงื่อนไข |
| 54 | `                $hours = $elapsedMinutes / 60;` | แปลงนาทีทั้งหมดให้เป็นทศนิยมของชั่วโมง | `$hours`:<br>- ชนิด: `float` เช่น `0.5`<br>- ส่งต่อไป: บรรทัด 55 |
| 55 | `                $estimatedCost = round($hours * (float) $activeSession->rate_snapshot, 2);` | คำนวณค่าบริการทั้งหมดโดยนำชั่วโมงคูณราคาของโซนที่นั่ง | `$estimatedCost`:<br>- ชนิด: `float` เช่น `30.00`<br>- ส่งต่อไป: บรรทัด 81 |
| 56 | `            }` | ปิดเงื่อนไขการคิดเงิน | ขอบเขตเงื่อนไข |
| 57 | `        }` | ปิดเงื่อนไข `if ($activeSession != null)` | ขอบเขตเงื่อนไข |
| 58 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 59 | `        // 4. คำนวณยอดเงินคงเหลือที่ใช้ได้จริง (หักค่าชั่วโมงที่กำลังเล่นอยู่)` | คอมเมนต์ขั้นตอนที่ 4 | ไม่มีตัวแปร |
| 60 | `        $availableBalance = $this->calculateAvailableBalance($user);` | เรียก Method ภายใน เพื่อคำนวณหายอดเงินสดที่สั่งอาหารได้จริง | `$availableBalance`:<br>- ที่มา: ผลลัพธ์จาก Method ภายใน<br>- ชนิด: `float` เช่น `150.00`<br>- ส่งต่อไป: บรรทัด 82 |
| 61 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 62 | `        // 5. ดึงรายการแพ็กเกจที่ผู้ใช้ซื้อไว้` | คอมเมนต์ขั้นตอนที่ 5 | ไม่มีตัวแปร |
| 63 | `        $userPackages = UserPackage::with('package')` | เริ่ม Query ตาราง `user_packages` พร้อม Eager Loading ข้อมูลแม่จากตาราง `packages` | คลาส `UserPackage` |
| 64 | `            ->where('user_id', $user->id)` | เงื่อนไข: เฉพาะแพ็กเกจที่เป็นของ User คนนี้ | `$user->id` |
| 65 | `            ->where('remaining_minutes', '>', 0)` | เงื่อนไข: ต้องมีเวลาเหลือมากกว่า 0 นาที | เงื่อนไขจำนวนนาที |
| 66 | `            ->where(function ($query) {` | เปิดกลุ่มเงื่อนไขวันหมดอายุ | Closure ฟังก์ชัน |
| 67 | `                $query->whereNull('expired_at')` | เงื่อนไข: ไม่มีวันหมดอายุ (ใช้ได้ตลอดไป) | ฟิลด์ `expired_at` |
| 68 | `                    ->orWhere('expired_at', '>', Carbon::now());` | หรือวันที่หมดอายุต้องมากกว่าเวลาปัจจุบัน (ยังไม่ถึงวันหมดอายุ) | ฟิลด์ `expired_at` |
| 69 | `            })` | ปิดกลุ่มเงื่อนไขวันหมดอายุ | จบ Closure |
| 70 | `            ->latest()` | เรียงลำดับจากแพ็กเกจที่ซื้อล่าสุด | คำสั่ง Query Builder |
| 71 | `            ->get();` | ดึงข้อมูลออกมาเป็น Collection ของ UserPackage | `$userPackages`:<br>- ที่มา: ตาราง `user_packages`<br>- ชนิด: Collection<br>- ส่งต่อไป: บรรทัด 83 |
| 72 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 73 | `        // 6. ดึงประวัติการเติมเงินและสั่งอาหารล่าสุด` | คอมเมนต์ขั้นตอนที่ 6 | ไม่มีตัวแปร |
| 74 | `        $transactions = WalletTransaction::where('user_id', $user->id)->latest()->take(5)->get();` | ค้นหาประวัติการเงินล่าสุด 5 รายการของผู้ใช้นี้ | `$transactions`:<br>- ที่มา: ตาราง `wallet_transactions`<br>- ชนิด: Collection 5 รายการ<br>- ส่งต่อไป: บรรทัด 84 |
| 75 | `        $recentOrders = Order::with('seat')->where('user_id', $user->id)->latest()->take(5)->get();` | ค้นหาประวัติการสั่งอาหารล่าสุด 5 รายการพร้อมเบอร์เครื่องที่นั่ง | `$recentOrders`:<br>- ที่มา: ตาราง `orders`<br>- ชนิด: Collection 5 รายการ<br>- ส่งต่อไป: บรรทัด 85 |
| 76 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 77 | `        return view('pages.customer.dashboard', compact(` | สั่งเรนเดอร์หน้าจอ Blade และรวมตัวแปรด้วยฟังก์ชัน `compact` | สั่งแสดงผล View |
| 78 | `            'user',` | ส่งตัวแปร `$user` ไปยังหน้า View | ข้อมูลผู้ใช้ |
| 79 | `            'activeSession',` | ส่งตัวแปร `$activeSession` ไปยังหน้า View | ข้อมูลเซสชัน |
| 80 | `            'elapsedMinutes',` | ส่งตัวแปร `$elapsedMinutes` ไปยังหน้า View | นาทีที่เล่นไป |
| 81 | `            'estimatedCost',` | ส่งตัวแปร `$estimatedCost` ไปยังหน้า View | ค่าเครื่องสะสม |
| 82 | `            'availableBalance',` | ส่งตัวแปร `$availableBalance` ไปยังหน้า View | เงินที่สั่งอาหารได้ |
| 83 | `            'userPackages',` | ส่งตัวแปร `$userPackages` ไปยังหน้า View | แพ็กเกจของผู้ใช้ |
| 84 | `            'transactions',` | ส่งตัวแปร `$transactions` ไปยังหน้า View | รายการประวัติเงิน |
| 85 | `            'recentOrders'` | ส่งตัวแปร `$recentOrders` ไปยังหน้า View | ประวัติสั่งอาหาร |
| 86 | `        ));` | ปิดฟังก์ชัน compact และ view | ปิดคำสั่ง return |
| 87 | `    }` | ปิดขอบเขต Method index | สิ้นสุด Method |

---

### ส่วนที่ 3: Method `checkOut(Request $request)` (บรรทัดที่ 88-185)
* **หน้าที่:** เช็คเอาท์และปิดเครื่อง คำนวณเวลาจริง ตัดเงินจาก Wallet หรือหักนาทีจากแพ็กเกจ คืนสถานะที่นั่งเป็นว่าง
* **รับอะไรเข้ามา:** `Request $request` (รับค่า HTTP POST ส่งมาจากฟอร์มเช็คเอาท์)
* **คืนค่าอะไร:** `redirect()->back()->with(...)` สั่งรีเฟรชกลับหน้าเดิมพร้อม Flash message
* **ถูกเรียกจาก Route ไหน:** `POST /check-out` (ชื่อ route: `customer.check-out`) ใน [routes/web.php](file:///e:/WEBAPPdatabase/game-center/routes/web.php)
* **แตะตารางไหนในฐานข้อมูล:** `seat_sessions`, `seats`, `users`, `user_packages`, `wallet_transactions`

| บรรทัด | โค้ดในไฟล์ | หน้าที่และการทำงาน | ตัวแปร: ที่มา / ชนิดและค่าตัวอย่าง / ส่งต่อไปไหน |
| :---: | :--- | :--- | :--- |
| 88 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 89 | `    /**` | เริ่มต้นบล็อกคอมเมนต์ DocBlock | ไม่มีตัวแปร |
| 90 | `     * ฟังก์ชันเช็คเอาท์และปิดเครื่อง` | ข้อความอธิบาย | ไม่มีตัวแปร |
| 91 | `     * สมาชิกคนที่ 1: ระบบแดชบอร์ดและโปรไฟล์ลูกค้า (Member 1)` | ข้อความอธิบายผู้รับผิดชอบโมดูล | ไม่มีตัวแปร |
| 92 | `     */` | สิ้นสุดคอมเมนต์ DocBlock | ไม่มีตัวแปร |
| 93 | `    public function checkOut(Request $request)` | ประกาศ Method checkOut รับพารามิเตอร์ Request | `$request`: Object HTTP Request อัตโนมัติ |
| 94 | `    {` | ปีกกาเปิดขอบเขต Method checkOut | ขอบเขต Method |
| 95 | `        $user = Auth::user();` | ดึงข้อมูลผู้ใช้ปัจจุบันที่กดปุ่มเช็คเอาท์ | `$user`:<br>- ที่มา: `Auth::user()`<br>- ชนิด: Model User<br>- ส่งต่อไป: บรรทัด 98, 107 |
| 96 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 97 | `        // 1. ค้นหาเครื่องที่ผู้ใช้กำลังเปิดใช้งานอยู่` | คอมเมนต์ขั้นตอนที่ 1 | ไม่มีตัวแปร |
| 98 | `        $session = SeatSession::where('user_id', $user->id)` | ค้นหาในตาราง `seat_sessions` เฉพาะของผู้ใช้คนนี้ | `$user->id` |
| 99 | `            ->where('status', 'active')` | กรองเฉพาะเซสชันที่สถานะยังเปิดเล่นอยู่ | สตริง `'active'` |
| 100 | `            ->first();` | หยิบเรคคอร์ดแรกที่ตรงเงื่อนไข | `$session`:<br>- ที่มา: ตาราง `seat_sessions`<br>- ชนิด: Model SeatSession หรือ `null`<br>- ส่งต่อไป: บรรทัด 102, 106, 110, 122, 123, 135, 147, 155, 167, 173-176 |
| 101 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 102 | `        if ($session == null) {` | ตรวจสอบว่าไม่พบเซสชันที่เล่นอยู่หรือไม่ | ตรวจสอบตัวแปร `$session` |
| 103 | `            return redirect()->back()->with('error', 'ไม่พบเครื่องที่กำลังใช้งานอยู่');` | สั่งดีดกลับหน้าเดิมพร้อมแนบข้อความแจ้งเตือน error | สั่ง Redirect |
| 104 | `        }` | ปิดเงื่อนไข | ขอบเขตเงื่อนไข |
| 105 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 106 | `        $seat = Seat::find($session->seat_id);` | ค้นหาข้อมูลเครื่องคอมพิวเตอร์ที่เซสชันนี้ใช้อยู่ | `$seat`:<br>- ที่มา: ตาราง `seats`<br>- ชนิด: Model Seat<br>- ส่งต่อไป: บรรทัด 179-181 |
| 107 | `        $freshUser = User::find($user->id);` | ค้นหาผู้ใช้จากฐานข้อมูลใหม่ เพื่อให้ได้ยอดเงินในกระเป๋าล่าสุดแบบเรียลไทม์ | `$freshUser`:<br>- ที่มา: ตาราง `users`<br>- ชนิด: Model User<br>- ส่งต่อไป: บรรทัด 139, 140, 143, 159, 160, 163 |
| 108 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 109 | `        // 2. คำนวณเวลาที่เล่นไป` | คอมเมนต์ขั้นตอนที่ 2 | ไม่มีตัวแปร |
| 110 | `        $startTime = Carbon::parse($session->start_time);` | แปลงเวลาเริ่มต้นเล่นให้อยู่ในรูป Carbon Object | `$startTime`:<br>- ชนิด: Carbon Object<br>- ส่งต่อไป: บรรทัด 112 |
| 111 | `        $endTime = Carbon::now();` | บันทึกเวลาปัจจุบันไว้เป็นเวลาสิ้นสุดการเล่น | `$endTime`:<br>- ชนิด: Carbon Object (เวลาปัจจุบัน)<br>- ส่งต่อไป: บรรทัด 112, 173 |
| 112 | `        $usedSeconds = $startTime->diffInSeconds($endTime);` | หาผลต่างเวลาทั้งหมดเป็นวินาที | `$usedSeconds`:<br>- ชนิด: `int`<br>- ส่งต่อไป: บรรทัด 114 |
| 113 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 114 | `        $usedMinutes = (int) ceil($usedSeconds / 60);` | แปลงวินาทีเป็นนาทีโดยปัดเศษขึ้น | `$usedMinutes`:<br>- ชนิด: `int` เช่น `45`<br>- ส่งต่อไป: บรรทัด 115, 125, 126, 130, 154 |
| 115 | `        if ($usedMinutes < 1) {` | ตรวจสอบว่าเวลาที่เล่นน้อยกว่า 1 นาทีหรือไม่ | ตรวจสอบ `$usedMinutes` |
| 116 | `            $usedMinutes = 1;` | กำหนดเวลาขั้นต่ำเป็น 1 นาที | `$usedMinutes` มีค่าอย่างน้อย 1 |
| 117 | `        }` | ปิดเงื่อนไขขั้นต่ำ | ขอบเขตเงื่อนไข |
| 118 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 119 | `        $totalCost = 0.00;` | กำหนดค่าเริ่มต้นของยอดเงินที่ต้องคิดเป็น 0.00 บาท | `$totalCost`:<br>- ชนิด: `float`<br>- ส่งต่อไป: บรรทัด 128, 136, 138, 145, 156, 158, 165, 174 |
| 120 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 121 | `        // 3. ตรวจสอบว่าเป็นแบบใช้แพ็กเกจหรือแบบคิดตามจริง` | คอมเมนต์ขั้นตอนที่ 3 | ไม่มีตัวแปร |
| 122 | `        if ($session->user_package_id != null) {` | เช็คว่าเซสชันนี้ผูกกับแพ็กเกจชั่วโมงหรือไม่ | ฟิลด์ `user_package_id` |
| 123 | `            $userPackage = UserPackage::find($session->user_package_id);` | ดึงข้อมูลแพ็กเกจของผู้ใช้ที่นำมาใช้ | `$userPackage`:<br>- ที่มา: ตาราง `user_packages`<br>- ชนิด: Model UserPackage หรือ `null`<br>- ส่งต่อไป: บรรทัด 124, 125, 126, 127, 130, 131, 132 |
| 124 | `            if ($userPackage != null) {` | ตรวจสอบว่าพบข้อมูลแพ็กเกจจริงในระบบ | ตรวจสอบ `$userPackage` |
| 125 | `                if ($userPackage->remaining_minutes >= $usedMinutes) {` | กรณีที่ 3.1: เวลาในแพ็กเกจมีมากกว่าหรือเท่ากับเวลาที่เล่นไป | เปรียบเทียบนาที |
| 126 | `                    $userPackage->remaining_minutes = $userPackage->remaining_minutes - $usedMinutes;` | หักนาทีที่เล่นออกจากนาทีคงเหลือของแพ็กเกจ | อัปเดตฟิลด์ `remaining_minutes` |
| 127 | `                    $userPackage->save();` | สั่งบันทึกการเปลี่ยนแปลงนาทีคงเหลือลงฐานข้อมูล | บันทึกตาราง `user_packages` |
| 128 | `                    $totalCost = 0.00;` | กำหนดยอดเงินที่ต้องจ่ายเพิ่มเป็น 0 บาท | `$totalCost = 0.00` |
| 129 | `                } else {` | กรณีที่ 3.2: เล่นเกินเวลาในแพ็กเกจ (Overtime) | ทางเลือกเงื่อนไข |
| 130 | `                    $excessMinutes = $usedMinutes - $userPackage->remaining_minutes;` | หานาทีส่วนเกินที่เล่นเกินจากแพ็กเกจ | `$excessMinutes`:<br>- ชนิด: `int`<br>- ส่งต่อไป: บรรทัด 134 |
| 131 | `                    $userPackage->remaining_minutes = 0;` | ปรับนาทีคงเหลือของแพ็กเกจให้เป็น 0 (ใช้หมดแล้ว) | กำหนดค่า 0 |
| 132 | `                    $userPackage->save();` | สั่งบันทึกลงฐานข้อมูล | บันทึกตาราง `user_packages` |
| 133 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 134 | `                    $excessHours = $excessMinutes / 60;` | แปลงนาทีส่วนเกินให้เป็นทศนิยมของชั่วโมง | `$excessHours`:<br>- ชนิด: `float`<br>- ส่งต่อไป: บรรทัด 136 |
| 135 | `                    $hourlyRate = (float) $session->rate_snapshot;` | อ่านราคาต่อชั่วโมงที่บันทึกไว้ ณ วันที่เปิดเครื่อง | `$hourlyRate`:<br>- ชนิด: `float`<br>- ส่งต่อไป: บรรทัด 136 |
| 136 | `                    $totalCost = round($excessHours * $hourlyRate, 2);` | คำนวณเงินค่าชั่วโมงส่วนเกิน ปัดเศษ 2 ตำแหน่ง | `$totalCost`:<br>- ชนิด: `float` เช่น `20.00`<br>- ส่งต่อไป: บรรทัด 138, 139, 145, 174 |
| 137 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 138 | `                    if ($totalCost > 0) {` | ถ้ามียอดเงินส่วนเกินที่ต้องจ่ายจริง | ตรวจสอบ `$totalCost > 0` |
| 139 | `                        $freshUser->balance = $freshUser->balance - $totalCost;` | หักเงินค่าบริการส่วนเกินออกจากกระเป๋าเงินผู้ใช้ | อัปเดตฟิลด์ `balance` ใน Memory |
| 140 | `                        $freshUser->save();` | สั่งบันทึกยอดเงินคงเหลือใหม่ลงตาราง `users` | บันทึกตาราง `users` |
| 141 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 142 | `                        WalletTransaction::create([` | เรียก Eloquent สั่ง Insert แถวใหม่ลงตาราง `wallet_transactions` | บันทึกประวัติการเงิน |
| 143 | `                            'user_id' => $freshUser->id,` | บันทึก ID ผู้ใช้ที่ถูกหักเงิน | `$freshUser->id` |
| 144 | `                            'type' => 'deduct',` | บันทึกประเภทธุรกรรมเป็น `deduct` (หักเงิน) | สตริง `'deduct'` |
| 145 | `                            'amount' => $totalCost,` | บันทึกจำนวนเงินที่ถูกหัก | `$totalCost` |
| 146 | `                            'ref_type' => 'session_overtime',` | บันทึกประเภทอ้างอิงเป็น `session_overtime` (ค่าเล่นเกินเวลา) | สตริง `'session_overtime'` |
| 147 | `                            'ref_id' => $session->id,` | บันทึก ID ของเซสชันที่เกี่ยวข้อง | `$session->id` |
| 148 | `                        ]);` | ปิดคำสั่ง create ของ WalletTransaction | จบคำสั่ง Insert |
| 149 | `                    }` | ปิดเงื่อนไขตัดเงินส่วนเกิน | ขอบเขตเงื่อนไข |
| 150 | `                }` | ปิดเงื่อนไขตรวจสอบนาทีแพ็กเกจ | ขอบเขตเงื่อนไข |
| 151 | `            }` | ปิดเงื่อนไข `if ($userPackage != null)` | ขอบเขตเงื่อนไข |
| 152 | `        } else {` | กรณีเล่นแบบคิดตามจริง (Pay-as-you-go) | ทางเลือกเงื่อนไข |
| 153 | `            // กรณีเล่นแบบคิดตามจริง (Pay as you go)` | คอมเมนต์อธิบาย | ไม่มีตัวแปร |
| 154 | `            $hours = $usedMinutes / 60;` | แปลงนาทีทั้งหมดให้เป็นทศนิยมของชั่วโมง | `$hours`:<br>- ชนิด: `float`<br>- ส่งต่อไป: บรรทัด 156 |
| 155 | `            $hourlyRate = (float) $session->rate_snapshot;` | ดึงราคาต่อชั่วโมงที่บันทึกไว้ในเซสชัน | `$hourlyRate`:<br>- ชนิด: `float`<br>- ส่งต่อไป: บรรทัด 156 |
| 156 | `            $totalCost = round($hours * $hourlyRate, 2);` | คำนวณเงินค่าชั่วโมงทั้งหมด ปัดเศษทศนิยม 2 ตำแหน่ง | `$totalCost`:<br>- ชนิด: `float` เช่น `45.00`<br>- ส่งต่อไป: บรรทัด 158, 159, 165, 174 |
| 157 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 158 | `            if ($totalCost > 0) {` | ถ้ามียอดเงินที่ต้องหักจริง | ตรวจสอบ `$totalCost > 0` |
| 159 | `                $freshUser->balance = $freshUser->balance - $totalCost;` | หักเงินค่าชั่วโมงออกจากยอดเงินในกระเป๋าผู้ใช้ | อัปเดตฟิลด์ `balance` ใน Memory |
| 160 | `                $freshUser->save();` | สั่งบันทึกยอดเงินใหม่ลงตาราง `users` | บันทึกตาราง `users` |
| 161 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 162 | `                WalletTransaction::create([` | เรียก Eloquent สั่ง Insert ประวัติการเงินลงตาราง `wallet_transactions` | บันทึกประวัติการเงิน |
| 163 | `                    'user_id' => $freshUser->id,` | บันทึก ID ผู้ใช้ที่ถูกหักเงิน | `$freshUser->id` |
| 164 | `                    'type' => 'deduct',` | ประเภทธุรกรรมเป็น `deduct` (หักเงิน) | สตริง `'deduct'` |
| 165 | `                    'amount' => $totalCost,` | บันทึกจำนวนเงินที่หัก | `$totalCost` |
| 166 | `                    'ref_type' => 'session',` | ประเภทอ้างอิงเป็น `session` (ค่าเล่นคอมพิวเตอร์) | สตริง `'session'` |
| 167 | `                    'ref_id' => $session->id,` | บันทึก ID ของเซสชันการเล่นนี้ | `$session->id` |
| 168 | `                ]);` | ปิดคำสั่ง create ของ WalletTransaction | จบคำสั่ง Insert |
| 169 | `            }` | ปิดเงื่อนไขหักเงิน | ขอบเขตเงื่อนไข |
| 170 | `        }` | ปิดเงื่อนไขโหมดคิดเงิน | ขอบเขตเงื่อนไข |
| 171 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 172 | `        // 4. บันทึกข้อมูลการสิ้นสุดการใช้งานลงตาราง seat_sessions` | คอมเมนต์ขั้นตอนที่ 4 | ไม่มีตัวแปร |
| 173 | `        $session->end_time = $endTime;` | อัปเดตฟิลด์ `end_time` ด้วยเวลาที่ปิดเครื่องจริง | `$endTime` |
| 174 | `        $session->total_cost = $totalCost;` | อัปเดตฟิลด์ `total_cost` ด้วยยอดเงินสุทธิที่คำนวณได้ | `$totalCost` |
| 175 | `        $session->status = 'completed';` | เปลี่ยนสถานะเซสชันเป็น `'completed'` (เล่นจบแล้ว) | สตริง `'completed'` |
| 176 | `        $session->save();` | สั่งบันทึกการเปลี่ยนแปลงทั้งหมดลงตาราง `seat_sessions` | บันทึกตาราง `seat_sessions` |
| 177 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 178 | `        // 5. คืนสถานะที่นั่งเป็น available (ว่าง)` | คอมเมนต์ขั้นตอนที่ 5 | ไม่มีตัวแปร |
| 179 | `        if ($seat != null) {` | ตรวจสอบว่าพบข้อมูลเครื่องในตาราง `seats` | ตรวจสอบ `$seat` |
| 180 | `            $seat->status = 'available';` | เปลี่ยนสถานะเครื่องกลับมาเป็น `'available'` (ว่าง) | สตริง `'available'` |
| 181 | `            $seat->save();` | สั่งบันทึกสถานะใหม่ลงตาราง `seats` | บันทึกตาราง `seats` |
| 182 | `        }` | ปิดเงื่อนไขคืนที่นั่ง | ขอบเขตเงื่อนไข |
| 183 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 184 | `        return redirect()->back()->with('success', 'เช็คเอาท์ออกจากเครื่องสำเร็จ');` | สั่งเปลี่ยนเส้นทางกลับหน้าเดิมพร้อมส่ง Flash message สีเขียว | ส่งข้อความกลับไปที่ View |
| 185 | `    }` | ปิดขอบเขต Method checkOut | สิ้นสุด Method |

---

### ส่วนที่ 4: Method `calculateAvailableBalance(User $user)` (บรรทัดที่ 186-223)
* **หน้าที่:** ฟังก์ชันภายใน (`private`) คำนวณเงินในกระเป๋าที่เหลือใช้สั่งอาหารได้จริง โดยหักเงินสำรองค่าชั่วโมงเล่นคอมพิวเตอร์ที่กำลังวิ่งอยู่ ณ วินาทีนั้นออกก่อน
* **รับอะไรเข้ามา:** `User $user` (Model User ของผู้ใช้)
* **คืนค่าอะไร:** ตัวเลขทศนิยม (`float`) ยอดเงินที่สั่งอาหารได้จริง
* **ถูกเรียกจากที่ไหน:** ถูกเรียกจาก Method `index()` บรรทัดที่ 60
* **แตะตารางไหนในฐานข้อมูล:** `users`, `seat_sessions`, `user_packages`

| บรรทัด | โค้ดในไฟล์ | หน้าที่และการทำงาน | ตัวแปร: ที่มา / ชนิดและค่าตัวอย่าง / ส่งต่อไปไหน |
| :---: | :--- | :--- | :--- |
| 186 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 187 | `    // คำนวณยอดเงินที่ใช้ได้จริงหลังหักค่าเครื่องที่กำลังเล่นอยู่` | คอมเมนต์อธิบายหน้าที่ของ Method | ไม่มีตัวแปร |
| 188 | `    private function calculateAvailableBalance(User $user): float` | ประกาศฟังก์ชัน Private รับพารามิเตอร์ `$user` และกำหนดคืนค่าเป็น `float` | `$user`: Model User ที่ส่งเข้ามา |
| 189 | `    {` | ปีกกาเปิดขอบเขตฟังก์ชัน | ขอบเขตฟังก์ชัน |
| 190 | `        $freshUser = User::find($user->id);` | ค้นหาข้อมูลผู้ใช้จากฐานข้อมูลเพื่อดึงยอดเงินสดล่าสุด | `$freshUser`:<br>- ที่มา: ตาราง `users`<br>- ชนิด: Model User<br>- ส่งต่อไป: บรรทัด 191, 195, 201, 222 |
| 191 | `        if ($freshUser == null) {` | ถ้าไม่พบผู้ใช้ในระบบ | ตรวจสอบ `$freshUser` |
| 192 | `            return 0.00;` | คืนค่าเงินเป็น 0.00 | คืนค่า `0.00` |
| 193 | `        }` | ปิดเงื่อนไข | ขอบเขตเงื่อนไข |
| 194 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 195 | `        $activeSession = SeatSession::where('user_id', $freshUser->id)` | ค้นหาเซสชันที่เล่นอยู่ของผู้ใช้คนนี้ | `$freshUser->id` |
| 196 | `            ->where('status', 'active')` | กรองสถานะ active | สตริง `'active'` |
| 197 | `            ->latest()` | เรียงจากรายการล่าสุด | คำสั่ง Query Builder |
| 198 | `            ->first();` | เอาเรคคอร์ดแรก | `$activeSession`:<br>- ชนิด: Model SeatSession หรือ `null`<br>- ส่งต่อไป: บรรทัด 200, 204, 212, 217, 220 |
| 199 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 200 | `        if ($activeSession == null) {` | ถ้าผู้ใช้ไม่ได้เปิดเครื่องเล่นอยู่ | ตรวจสอบ `$activeSession` |
| 201 | `            return max(0.00, (float) $freshUser->balance);` | คืนค่ายอดเงินในกระเป๋าทั้งหมดได้ทันที (ใช้ `max` ป้องกันเงินติดลบ) | คืนค่า `float` ยอดเงินสด |
| 202 | `        }` | ปิดเงื่อนไข | ขอบเขตเงื่อนไข |
| 203 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 204 | `        $startTime = Carbon::parse($activeSession->start_time);` | แปลงเวลาเริ่มเล่นเป็น Carbon Object | `$startTime`: Carbon Object |
| 205 | `        $usedSeconds = $startTime->diffInSeconds(Carbon::now());` | คำนวณวินาทีที่เล่นไปแล้วจนถึงปัจจุบัน | `$usedSeconds`: `int` วินาที |
| 206 | `        $usedMinutes = (int) ceil($usedSeconds / 60);` | ปัดเศษวินาทีเป็นนาที | `$usedMinutes`: `int` นาทีสะสม |
| 207 | `        if ($usedMinutes < 1) {` | ตรวจสอบขั้นต่ำ | เปรียบเทียบ `$usedMinutes` |
| 208 | `            $usedMinutes = 1;` | กำหนดขั้นต่ำเป็น 1 นาที | `$usedMinutes = 1` |
| 209 | `        }` | ปิดเงื่อนไขขั้นต่ำ | ขอบเขตเงื่อนไข |
| 210 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 211 | `        $estimatedCost = 0.00;` | กำหนดค่าเริ่มต้นค่าเครื่องสะสมเป็น 0.00 บาท | `$estimatedCost`: `float` |
| 212 | `        if ($activeSession->user_package_id != null && $activeSession->userPackage != null) {` | ตรวจสอบว่าเล่นด้วยแพ็กเกจหรือไม่ | ตรวจสอบแพ็กเกจ |
| 213 | `            $remainingMinutes = $activeSession->userPackage->remaining_minutes;` | อ่านนาทีคงเหลือของแพ็กเกจ | `$remainingMinutes`: `int` |
| 214 | `            if ($usedMinutes > $remainingMinutes) {` | ถ้าเล่นเกินเวลาในแพ็กเกจ | เปรียบเทียบนาที |
| 215 | `                $excess = $usedMinutes - $remainingMinutes;` | หานาทีส่วนเกิน | `$excess`: `int` นาทีเกิน |
| 216 | `                $estimatedCost = round(($excess / 60) * (float) $activeSession->rate_snapshot, 2);` | คำนวณเงินค่าล่วงเวลาสะสม | `$estimatedCost`: `float` |
| 217 | `            }` | ปิดเงื่อนไข | ขอบเขตเงื่อนไข |
| 218 | `        } else {` | กรณีเล่นแบบจ่ายตามจริง | ทางเลือกเงื่อนไข |
| 219 | `            $estimatedCost = round(($usedMinutes / 60) * (float) $activeSession->rate_snapshot, 2);` | คำนวณค่าเครื่องสะสมตามนาทีจริงคูณราคาโซน | `$estimatedCost`: `float` |
| 220 | `        }` | ปิดเงื่อนไข | ขอบเขตเงื่อนไข |
| 221 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 222 | `        return max(0.00, round((float) $freshUser->balance - $estimatedCost, 2));` | นำยอดเงินสดลบค่าเครื่องสะสม และคืนค่ากลับไป (ไม่ให้ต่ำกว่า 0.00) | คืนค่า `float` เงินสั่งอาหารได้จริง |
| 223 | `    }` | ปิดขอบเขต Method calculateAvailableBalance | สิ้นสุด Method |

---

### ส่วนที่ 5: Method `autoEndExpiredSessions()` (บรรทัดที่ 224-272)
* **หน้าที่:** ฟังก์ชันภายใน (`private`) ตรวจจับและตัดจบเซสชันของเครื่องที่เงินในกระเป๋าหมด หรือแพ็กเกจหมดเวลาอัตโนมัติ
* **รับอะไรเข้ามา:** ไม่มีพารามิเตอร์
* **คืนค่าอะไร:** ไม่มี (`void`)
* **ถูกเรียกจากที่ไหน:** ถูกเรียกที่บรรทัด 25 ใน Method `index()` ทุกครั้งที่มีการเปิดหน้าแดชบอร์ด
* **แตะตารางไหนในฐานข้อมูล:** `seat_sessions`, `users`, `user_packages`, `seats`

| บรรทัด | โค้ดในไฟล์ | หน้าที่และการทำงาน | ตัวแปร: ที่มา / ชนิดและค่าตัวอย่าง / ส่งต่อไปไหน |
| :---: | :--- | :--- | :--- |
| 224 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 225 | `    // ตรวจสอบและตัดจบเซสชันอัตโนมัติเมื่อเงินหมด` | คอมเมนต์อธิบายหน้าที่ของ Method | ไม่มีตัวแปร |
| 226 | `    private function autoEndExpiredSessions(): void` | ประกาศฟังก์ชัน Private ระบุการคืนค่าแบบ void (ไม่มีการ return ค่า) | ไม่มีพารามิเตอร์ |
| 227 | `    {` | ปีกกาเปิดขอบเขตฟังก์ชัน | ขอบเขตฟังก์ชัน |
| 228 | `        $activeSessions = SeatSession::where('status', 'active')->with(['user', 'userPackage'])->get();` | ดึงเซสชันที่กำลังเล่นอยู่ทั้งหมดในร้านขึ้นมาตรวจ พร้อมโหลดตาราง `users` และ `user_packages` | `$activeSessions`:<br>- ที่มา: ตาราง `seat_sessions`<br>- ชนิด: Collection<br>- ส่งต่อไป: บรรทัด 230 |
| 229 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 230 | `        foreach ($activeSessions as $session) {` | เริ่มต้นลูปวนตรวจเซสชันทีละเครื่อง | `$session`: เซสชันของรอบปัจจุบัน |
| 231 | `            $user = $session->user;` | ดึงข้อมูลผู้ใช้ของเซสชันรอบนี้ | `$user`: Model User |
| 232 | `            if ($user == null) {` | ถ้าไม่มีข้อมูลผู้ใช้ | ตรวจสอบ `$user` |
| 233 | `                continue;` | ให้ข้ามรอบลูปนี้ไปตรวจเครื่องถัดไปทันที | คำสั่ง `continue` ของ PHP |
| 234 | `            }` | ปิดเงื่อนไข | ขอบเขตเงื่อนไข |
| 235 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 236 | `            $startTime = Carbon::parse($session->start_time);` | แปลงเวลาเริ่มเล่นเป็น Carbon Object | `$startTime`: Carbon Object |
| 237 | `            $usedSeconds = $startTime->diffInSeconds(Carbon::now());` | คำนวณวินาทีที่เล่นไปแล้วของเครื่องนี้ | `$usedSeconds`: `int` วินาที |
| 238 | `            $usedMinutes = (int) ceil($usedSeconds / 60);` | ปัดเศษวินาทีเป็นนาที | `$usedMinutes`: `int` นาที |
| 239 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 240 | `            $isExpired = false;` | ตั้งสถานะเริ่มต้นว่าเครื่องนี้ยังไม่หมดเวลา (`false`) | `$isExpired`:<br>- ชนิด: `bool` ค่า `false`<br>- ส่งต่อไป: บรรทัด 244, 249, 253, 258 |
| 241 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 242 | `            if ($session->user_package_id != null && $session->userPackage != null) {` | ตรวจสอบโหมดแพ็กเกจ | ตรวจสอบเงื่อนไข |
| 243 | `                if ($usedMinutes >= $session->userPackage->remaining_minutes && $user->balance <= 0) {` | ถ้าเล่นเกินเวลาในแพ็กเกจ และเงินในกระเป๋าผู้ใช้ก็หมดเกลี้ยง (<= 0) | เปรียบเทียบเวลาและยอดเงิน |
| 244 | `                    $isExpired = true;` | ปรับสถานะเป็นหมดเวลา | `$isExpired = true` |
| 245 | `                }` | ปิดเงื่อนไข | ขอบเขตเงื่อนไข |
| 246 | `            } else {` | กรณีเล่นแบบจ่ายตามจริง | ทางเลือกเงื่อนไข |
| 247 | `                $cost = round(($usedMinutes / 60) * (float) $session->rate_snapshot, 2);` | คำนวณค่าบริการสะสม ณ ปัจจุบัน | `$cost`: `float` ค่าเครื่องสะสม |
| 248 | `                if ($cost >= $user->balance && $user->balance <= 0) {` | ถ้ามีค่าเครื่องเกิดขึ้นและยอดเงินในกระเป๋าติดลบหรือเป็น 0 | ตรวจสอบยอดเงิน |
| 249 | `                    $isExpired = true;` | ปรับสถานะเป็นหมดเวลา | `$isExpired = true` |
| 250 | `                } elseif ($user->balance > 0) {` | ถ้ายังมีเงินในกระเป๋าเหลืออยู่ | ตรวจสอบยอดเงินบวก |
| 251 | `                    $maxMinutes = (int) floor(($user->balance / (float) $session->rate_snapshot) * 60);` | คำนวณหานาทีสูงสุดที่เงินในกระเป๋าจะเล่นได้ ปัดเศษลง (`floor`) | `$maxMinutes`: `int` นาทีสูงสุด |
| 252 | `                    if ($usedMinutes >= $maxMinutes && $maxMinutes > 0) {` | ถ้าเวลาที่เล่นไป เกินจำนวนนาทีสูงสุดที่เงินมี | เปรียบเทียบนาที |
| 253 | `                        $isExpired = true;` | ปรับสถานะเป็นหมดเวลา | `$isExpired = true` |
| 254 | `                    }` | ปิดเงื่อนไข | ขอบเขตเงื่อนไข |
| 255 | `                }` | ปิดเงื่อนไข | ขอบเขตเงื่อนไข |
| 256 | `            }` | ปิดเงื่อนไขโหมดคิดเงิน | ขอบเขตเงื่อนไข |
| 257 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 258 | `            if ($isExpired) {` | ตรวจสอบว่าเครื่องนี้เข้าเงื่อนไขหมดเวลาหรือไม่ | ตรวจสอบ `$isExpired` |
| 259 | `                $seat = Seat::find($session->seat_id);` | ค้นหาข้อมูลเครื่องคอมพิวเตอร์ที่เซสชันนี้ใช้อยู่ | `$seat`: Model Seat |
| 260 | `                $session->end_time = Carbon::now();` | บันทึกเวลาสิ้นสุดเป็นเวลาปัจจุบัน | เวลาปัจจุบัน |
| 261 | `                $session->status = 'completed';` | ปรับสถานะเซสชันเป็นเสร็จสิ้น (`completed`) | สตริง `'completed'` |
| 262 | `                $session->save();` | สั่งบันทึกการปิดเซสชันลงตาราง `seat_sessions` | บันทึกตาราง `seat_sessions` |
| 263 | *(บรรทัดว่าง)* | เว้นวรรค | ไม่มีตัวแปร |
| 264 | `                if ($seat != null) {` | ถ้าพบข้อมูลเครื่องคอมพิวเตอร์ | ตรวจสอบ `$seat` |
| 265 | `                    $seat->status = 'available';` | เปลี่ยนสถานะเครื่องกลับเป็นว่าง (`available`) ทันที | สตริง `'available'` |
| 266 | `                    $seat->save();` | สั่งบันทึกสถานะใหม่ลงตาราง `seats` | บันทึกตาราง `seats` |
| 267 | `                }` | ปิดเงื่อนไขอัปเดตเครื่อง | ขอบเขตเงื่อนไข |
| 268 | `            }` | ปิดเงื่อนไข `$isExpired` | ขอบเขตเงื่อนไข |
| 269 | `        }` | ปิดลูป `foreach` | จบลูป |
| 270 | `    }` | ปิดขอบเขต Method autoEndExpiredSessions | สิ้นสุด Method |
| 271 | `}` | ปิดขอบเขตคลาส DashboardController | สิ้นสุดคลาส |
| 272 | *(บรรทัดว่าง)* | สิ้นสุดไฟล์ (Trailing Line) | ไม่มีตัวแปร |
