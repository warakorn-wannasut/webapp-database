# คู่มืออ่านระบบ Game Center

เอกสารนี้เป็นแผนที่สำหรับตามว่า “ผู้ใช้กดอะไร แล้วโค้ดส่วนไหนทำงานต่อ” โดยยึดจากโค้ดในโปรเจกต์ปัจจุบัน

## จำภาพรวมแบบสั้น

เมื่อผู้ใช้ทำรายการหนึ่งครั้ง ให้ตามเส้นทางนี้:

**หน้าเว็บ (View) → Route → Controller → Model/ฐานข้อมูล → กลับไป View หรือ redirect**

- **View** คือหน้าจอและฟอร์มที่ผู้ใช้เห็น เช่น `resources/views/pages/customer/seat-map.blade.php`
- **Route** คือป้ายบอกว่า URL และชนิดคำขอนี้ส่งให้ Controller ตัวไหน เช่น `routes/web.php`
- **Controller** คือผู้รับเรื่อง ตรวจข้อมูล ตัดสินใจว่าจะทำอะไร และเรียก Model
- **Model** คือตัวแทนข้อมูลในฐานข้อมูล เช่น `Seat` แทนที่นั่ง และ `SeatSession` แทนการใช้งานเครื่องหนึ่งครั้ง
- **Service** คืองานขั้นตอนกลางที่หลาย Controller ต้องใช้กฎเดียวกัน เช่น `app/Services/EndSeatSession.php`

ชื่อ route ที่เห็นในฟอร์ม เช่น `customer.check-in` เป็นชื่อเรียกของ URL ไม่ใช่ชื่อฟังก์ชัน ต้องเปิด `routes/web.php` เพื่อดูว่าชื่อนั้นชี้ไปที่ Controller method ใด

## ตาม flow ตัวอย่าง

### 1. เปิดผังและเช็คอิน

1. เปิด `resources/views/pages/customer/seat-map.blade.php` เพื่อดูฟอร์มและข้อมูลที่ส่ง เช่น `seat_id` กับ `billing_mode`
2. ดู `routes/web.php` ที่ `customer.seat-map` และ `customer.check-in` เพื่อจับคู่ GET/POST กับ `SeatController::index()` และ `SeatController::checkIn()`
3. ใน `SeatController::index()` ระบบอ่านโซน ที่นั่ง session ปัจจุบัน และแพ็กเกจของผู้ใช้ แล้วส่งตัวแปรไปให้ View แสดง
4. เมื่อส่งฟอร์ม `SeatController::checkIn()` ตรวจสิทธิ์ใช้แพ็กเกจ/ยอดเงินและสถานะเครื่อง จากนั้นตั้งสถานะที่นั่งเป็น `occupied` และสร้าง `SeatSession` สถานะ `active`
5. หลังบันทึกสำเร็จ Controller redirect ไปหน้า `dashboard`

จำง่าย ๆ: **Seat คือเครื่อง, SeatSession คือบันทึกว่าใครเริ่มใช้เครื่องไหนเมื่อไร**

### 2. ดู Dashboard และเวลาที่เหลือ

1. `GET /dashboard` เรียก `DashboardController::index()`
2. ก่อนเตรียมข้อมูลหน้า Dashboard มีการเรียก `autoEndExpiredSessions()` เพื่อตรวจ session ที่หมดสิทธิ์ใช้
3. Controller อ่าน session ปัจจุบัน คำนวณเวลา/ค่าใช้จ่ายที่ประเมินไว้ และเตรียมประวัติรายการ
4. ข้อมูลถูกส่งเข้า `resources/views/pages/customer/dashboard.blade.php` เพื่อแสดงผล

ข้อควรจำ: การตรวจตัดจบอัตโนมัติปัจจุบันถูกเรียกเมื่อเข้า Dashboard ไม่ได้ทำงานเป็นงานตามเวลาที่ตั้งไว้ในเบื้องหลัง

### 3. สั่งอาหาร

1. `resources/views/pages/customer/food-order.blade.php` แสดงสินค้าและเก็บรายการในตะกร้า
2. `customer.place-order` ใน `routes/web.php` ส่ง POST ไป `FoodOrderController::placeOrder()`
3. Controller ตรวจสินค้า/สต็อก คำนวณยอด และตรวจเงินกรณีจ่าย Wallet
4. ระบบสร้าง `Order` เป็นหัวบิล, สร้าง `OrderItem` เป็นสินค้าแต่ละรายการ และลดสต็อก
5. ถ้าจ่าย Wallet จะลด `users.balance` และเขียนประวัติ `WalletTransaction`
6. พนักงานเห็นบิลที่ `StaffController::kitchenQueue()` และอัปเดตสถานะจากหน้าคิวครัว

จำง่าย ๆ: **Order คือหัวบิล, OrderItem คือบรรทัดสินค้าในบิล, WalletTransaction คือสมุดบันทึกการขยับเงิน**

### 4. เช็คเอาท์หรือพนักงานปิดเครื่อง

1. ลูกค้าส่งฟอร์มไป `DashboardController::checkOut()` หรือพนักงานส่งไป `StaffController::forceEnd()`
2. Controller หา `SeatSession` ที่จะปิด
3. ทั้งสองทางส่ง session ให้ `app/Services/EndSeatSession.php` ทำกฎชุดเดียวกัน: คิดนาที ลดเวลาแพ็กเกจหรือยอด Wallet บันทึกธุรกรรม ปิด session และคืนสถานะที่นั่ง
4. Controller ส่งข้อความผลลัพธ์กลับไปยังหน้าที่เรียก

จุดสำคัญ: ถ้าอยากรู้สูตรคิดเงิน ให้เปิด Service นี้เป็นจุดแรก เพราะ Controller สองทางไม่ควรมีสูตรคนละชุด

## ไฟล์หลักแบ่งตามงาน

| งาน | จุดเริ่มจาก Route | Controller | หน้าจอหลัก |
| --- | --- | --- | --- |
| Dashboard/เช็คเอาท์ | `dashboard`, `customer.check-out` | `DashboardController` | `pages/customer/dashboard.blade.php` |
| เลือกเครื่อง/เช็คอิน | `customer.seat-map`, `customer.check-in` | `SeatController` | `pages/customer/seat-map.blade.php` |
| สั่งอาหาร | `customer.food-order`, `customer.place-order` | `FoodOrderController` | `pages/customer/food-order.blade.php` |
| เติมเงิน/ซื้อแพ็กเกจ | `customer.topup`, `customer.do-topup`, `customer.buy-package` | `WalletController` | `pages/customer/topup.blade.php` |
| มอนิเตอร์เครื่อง/ครัว | route กลุ่ม `staff.*` | `StaffController` | `pages/staff/*` |
| สินค้า/รายงาน | route กลุ่ม `admin.*` | `AdminController` | `pages/admin/*` |

## วิธีอ่าน Controller โดยไม่หลง

อ่าน method หนึ่งตัวแล้วแบ่งเป็น 4 ช่วง:

1. **รับอะไรเข้ามา?** ดูพารามิเตอร์และ `$request->validate(...)`
2. **อ่านข้อมูลอะไร?** หา `Model::...`, `find()`, `where()` และ `with()`
3. **เปลี่ยนอะไร?** หา `create()`, `save()`, `update()` และตรวจว่ากระทบตารางใด
4. **ส่งผู้ใช้ไปไหน?** ดู `return view(...)` หรือ `redirect()->route(...)` / `redirect()->back()`

ถ้าเห็น `$session->seat` หรือ `$order->items` นั่นคือการเดินตามความสัมพันธ์ Model ไปยังข้อมูลที่เชื่อมกัน ไม่ใช่การเรียก Controller อีกตัว

## ศัพท์ที่เจอบ่อย

- `active`: กำลังใช้งาน
- `completed`: จบการใช้งานแล้ว
- `available`: เครื่องว่าง
- `maintenance`: เครื่องซ่อมบำรุง
- `pay_as_you_go`: คิดค่าบริการตามเวลาที่ใช้
- `package`: ใช้เวลาจากแพ็กเกจ และอาจมีค่าใช้จ่ายส่วนเกิน
- `with(...)`: โหลดข้อมูลที่สัมพันธ์กันล่วงหน้า เพื่อลดการถามฐานข้อมูลทีละรายการ
- `redirect`: สั่งให้เบราว์เซอร์เปิด URL/หน้าถัดไป
- `with('success', ...)`: ฝากข้อความไว้ให้หน้าใหม่แสดงหลัง redirect

## หมายเหตุจากการตรวจโค้ด

- ความสัมพันธ์จาก `Seat` ไปยัง session มีชื่อ `seatSessions()` ดังนั้นจุดที่โหลด/อ่าน session ต้องใช้ชื่อเดียวกัน
- เอกสารเดิม `docs/DashboardController.md` และ `docs/study/DashboardController.md` เป็นเนื้อหาซ้ำกัน และอ้างจำนวนบรรทัดเก่าของ Controller คู่มือนี้จึงเน้น flow ปัจจุบันแทนการไล่อธิบายทุกบรรทัด
