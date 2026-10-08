# แผนผังการทำงานและการลดความซับซ้อนทั้งโปรเจกต์ (Game Center Architecture & Simplification)

เอกสารนี้จัดทำขึ้นเพื่อตอบ 2 คำถามสำคัญ:
1. ทำไมอ่านโค้ดส่วน Controller แล้วไม่เข้าใจว่าฟังก์ชันคุยกันตอนไหน และทำงานร่วมกันอย่างไร?
2. โปรเจกต์นี้สามารถลดความซับซ้อนส่วนไหนได้อีกบ้าง โดยไม่ให้กระทบต่อฟังก์ชันหรือระบบการทำงานเดิม?

---

## ส่วนที่ 1: ทำความเข้าใจว่า Controller ทำงานและ "คุยกัน" ตอนไหน

### 1.1 ความจริงพื้นฐานของ Laravel: Controller ไม่เคยคุยกันเองโดยตรง

ความสับสนที่พบบ่อยในการอ่านโค้ดเกิดจากความเข้าใจว่า Controller ตัวหนึ่งจะเรียกใช้ Controller อีกตัวหนึ่งเหมือนฟังก์ชันทั่วไปในโปรแกรมแบบหน้าจอเดียว (Desktop App หรือ Script เดียว)

ใน Laravel และสถาปัตยกรรมเว็บแบบ MVC:
- **ไม่มี Controller ใดที่เรียกหา Controller อื่นโดยตรง**
- ทุก Controller เป็นอิสระจากกัน (Stateless)
- **ผู้ที่สั่งให้ฟังก์ชันใน Controller ทำงาน มีเพียงคนเดียวคือ "Laravel Router" (`routes/web.php`)** เมื่อได้รับ HTTP Request (การเปิดหน้าเว็บหรือการกดปุ่มส่งฟอร์ม) จากเบราว์เซอร์ของผู้ใช้

### 1.2 แล้ว Controller แต่ละตัวส่งต่อข้อมูลให้กันได้อย่างไร?

Controller ไม่ได้ส่งตัวแปรหากัน แต่แลกเปลี่ยนข้อมูลผ่าน **"ตารางในฐานข้อมูล SQLite"** เป็นสื่อกลาง

ลองดูตัวอย่างวงจรชีวิตจริง (Life Cycle) ของลูกค้า 1 คน:

```text
[ขั้นตอนที่ 1: ลูกค้าเลือกที่นั่งและกดเช็คอิน]
เบราว์เซอร์ส่ง: POST /check-in
  ↓
routes/web.php สั่งรัน: SeatController@checkIn
  ↓
SeatController ทำงาน:
  - สร้างแถวใหม่ในตาราง `seat_sessions` (สถานะ = 'active', เริ่มเวลาปัจจุบัน)
  - อัปเดตแถวในตาราง `seats` (สถานะ = 'occupied')
  ↓
SeatController สั่งเบราว์เซอร์: redirect()->route('dashboard')

--------------------------------------------------------------

[ขั้นตอนที่ 2: เบราว์เซอร์ถูกเปลี่ยนหน้ามาที่แดชบอร์ด]
เบราว์เซอร์ส่ง: GET /dashboard
  ↓
routes/web.php สั่งรัน: DashboardController@index
  ↓
DashboardController ทำงาน:
  - "ไม่ได้คุยกับ SeatController เลยแม้แต่น้อย"
  - วิ่งไปค้นในตาราง `seat_sessions` ว่า user คนนี้มี session สถานะ 'active' อยู่หรือไม่
  - เมื่อพบแถวที่ SeatController เพิ่งเขียนไว้ ก็นำเวลานั้นมาคำนวณเงินและชั่วโมงคงเหลือ
  - ส่งตัวแปร `$activeSession`, `$estimatedCost` ไปให้ `dashboard.blade.php` แสดงผล

--------------------------------------------------------------

[ขั้นตอนที่ 3: ลูกค้านั่งเล่นแล้วหิวน้ำ กดสั่งเครื่องดื่ม]
เบราว์เซอร์ส่ง: POST /food-order
  ↓
routes/web.php สั่งรัน: FoodOrderController@placeOrder
  ↓
FoodOrderController ทำงาน:
  - สร้างแถวในตาราง `orders` และ `order_items`
  - หากชำระผ่าน Wallet ให้ลดเงินในฟิลด์ `users.balance`
  - ไม่ได้คุยกับ DashboardController หรือ SeatController
  - แต่เมื่อลูกค้าเปิด Dashboard อีกครั้ง DashboardController จะไปอ่านตาราง `orders` มาแสดงในตารางประวัติเองอัตโนมัติ

--------------------------------------------------------------

[ขั้นตอนที่ 4: ลูกค้ากดปุ่มเช็คเอาท์ออกจากเครื่อง]
เบราว์เซอร์ส่ง: POST /check-out
  ↓
routes/web.php สั่งรัน: DashboardController@checkOut
  ↓
DashboardController เรียก Service กลาง: EndSeatSession@handle
  ↓
EndSeatSession ทำงาน:
  - คำนวณเวลาที่ใช้ไปทั้งหมด
  - หักเงิน Wallet หรือหักนาทีแพ็กเกจ
  - อัปเดตตาราง `seat_sessions` (สถานะ = 'completed', บันทึก `end_time`)
  - คืนสถานะตาราง `seats` (สถานะ = 'available')
```

---

## ส่วนที่ 2: แผนผังภาพรวมของทั้ง 6 Controller ในระบบ

โปรเจกต์นี้แบ่งงานออกเป็น 6 Controller ตามบทบาทผู้ใช้งานและหน้าจอ:

| Controller | หน้าที่หลัก | ถูกเรียกจาก Route ใดบ้าง | แตะตารางฐานข้อมูลใด |
|---|---|---|---|
| **1. DashboardController** | แสดงภาพรวมบัญชีผู้ใช้, คำนวณชั่วโมงคงเหลือ, แสดง HUD ลอยมุมจอ และรับคำสั่งเช็คเอาท์ | `GET /dashboard`<br>`POST /check-out` | `users`<br>`seat_sessions`<br>`user_packages`<br>`orders`<br>`wallet_transactions` |
| **2. SeatController** | แสดงแผนผังที่นั่งคอมพิวเตอร์ และรับคำสั่งเช็คอินเปิดเครื่อง | `GET /seat-map`<br>`POST /check-in` | `zones`<br>`seats`<br>`seat_sessions`<br>`user_packages` |
| **3. FoodOrderController** | แสดงเมนูอาหารเครื่องดื่ม และรับออเดอร์พร้อมตัดสต็อก/ตัดเงิน | `GET /food-order`<br>`POST /food-order` | `products`<br>`categories`<br>`orders`<br>`order_items`<br>`users`<br>`wallet_transactions` |
| **4. WalletController** | แสดงหน้าเติมเงิน, รับเติมเงินเข้ากระเป๋า และรับซื้อแพ็กเกจชั่วโมง | `GET /topup`<br>`POST /topup`<br>`POST /buy-package` | `users`<br>`packages`<br>`user_packages`<br>`wallet_transactions` |
| **5. StaffController** | มอนิเตอร์เครื่องหน้าร้าน, บังคับปิดเครื่อง, สลับสถานะซ่อมบำรุง, ดูคิวครัว และเปลี่ยนสถานะอาหาร | `GET /staff/seat-monitor`<br>`POST /staff/force-end`<br>`POST /staff/toggle-seat`<br>`GET /staff/kitchen-queue`<br>`POST /staff/orders/status`<br>`POST /staff/orders/confirm-cash` | `seats`<br>`zones`<br>`seat_sessions`<br>`orders`<br>`order_items` |
| **6. AdminController** | จัดการสต็อกสินค้าหน้าร้าน, ปรับจำนวนสต็อก, เพิ่มสินค้าใหม่ และออกรายงานสรุปยอดขาย | `GET /admin/stock-manager`<br>`POST /admin/stock/adjust`<br>`POST /admin/products`<br>`GET /admin/sales-report` | `products`<br>`categories`<br>`orders`<br>`order_items`<br>`wallet_transactions` |

---

## ส่วนที่ 3: จุดที่สามารถลดความซับซ้อนในโปรเจกต์ได้โดยไม่กระทบระบบ

จากการตรวจทานโค้ดจริงทั้งหมด พบ 4 จุดหลักที่ลดความซับซ้อนได้ทันที:

### จุดที่ 1: ลบ Dead Code ของ Game Catalog ออกจาก `DashboardController.php` (ดำเนินการแล้ว)
- **ปัญหาเดิม:** ใน `DashboardController.php` มีฟังก์ชัน `getGameCatalog()` ความยาวถึง 174 บรรทัด (เก็บ Mock Data เกม 12 เกม) ค้างอยู่ ทั้งที่หน้า Blade ไม่ได้เรียกใช้แล้ว
- **ผลการปรับปรุง:** ลบฟังก์ชันนี้ออก ทำให้ `DashboardController.php` สั้นลงจาก 386 บรรทัด เหลือเพียง 210 บรรทัด อ่านง่ายขึ้นทันทีโดยชุดทดสอบทั้ง 45 ข้อผ่าน 100%

### จุดที่ 2: รวมการคำนวณ `calculateAvailableBalance()` ไว้ที่จุดเดียว
- **ปัญหาปัจจุบัน:** ทั้ง `DashboardController` และ `FoodOrderController` ต่างมีโค้ดคำนวณว่า "เงินในกระเป๋าหักค่าเครื่องที่กำลังเล่นอยู่เหลือใช้ได้จริงเท่าไร" ซ้ำกันเกือบ 40 บรรทัดในแต่ละไฟล์
- **แนวทางลดความซับซ้อน:** ย้ายตรรกะนี้ไปเป็น Attribute ใน `app/Models/User.php` เช่น:
  ```php
  public function getAvailableBalanceAttribute(): float
  {
      // ตรรกะคำนวณหักค่าเครื่องที่กำลังเล่นอยู่
  }
  ```
  จากนั้นในทุก Controller สามารถเรียกสั้นๆ ได้ว่า `$user->available_balance` แทนการเขียนฟังก์ชันซ้ำซ้อนในหลาย Controller

### จุดที่ 3: ปรับ `autoEndExpiredSessions()` ให้ใช้ `EndSeatSession` Service ตัวเดียวกัน
- **ปัญหาปัจจุบัน:**
  - ตอนลูกค้ากดเช็คเอาท์ (`DashboardController@checkOut`) ใช้ Service `EndSeatSession`
  - ตอนพนักงานกดสั่งปิดเครื่อง (`StaffController@forceEnd`) ใช้ Service `EndSeatSession`
  - แต่ใน `DashboardController@autoEndExpiredSessions` กลับเขียนโค้ดปิดเซสชันและเปลี่ยนสถานะที่นั่งเองโดยตรง ไม่ได้เรียกใช้ Service ดังกล่าว
- **แนวทางลดความซับซ้อน:** ให้ `autoEndExpiredSessions()` ส่ง session ที่หมดเวลาให้ `EndSeatSession->handle($session)` ทำงาน จะทำให้สูตรการคิดเงินและหักเงินมีจุดศูนย์กลางเพียงที่เดียว ไม่ต้องกังวลเรื่องสูตรคิดเงินไม่ตรงกัน

### จุดที่ 4: จัดการเอกสารซ้ำซ้อนในโฟลเดอร์ `docs/`
- **ปัญหาปัจจุบัน:** มีไฟล์ `docs/DashboardController.md` และ `docs/study/DashboardController.md` ที่เป็นไฟล์ขนาด 71KB เนื้อหาซ้ำกัน และอ้างอิงเลขบรรทัดเก่าก่อนการแก้ไข
- **แนวทางลดความซับซ้อน:** ยึดถือเอกสารสรุปสถาปัตยกรรมและ Flow ปัจจุบัน เช่น `docs/PROJECT_GUIDE_TH.md` และ `docs/pc-bang-ui-features.md` แทนการอ่านไฟล์เก่าที่มีข้อมูลคลาดเคลื่อน

---

## ส่วนที่ 4: คำศัพท์พื้นฐานของ Laravel ที่ใช้ในโปรเจกต์นี้

1. **Route (`routes/web.php`):**
   - คือสมุดโทรศัพท์หรือแผนผังเส้นทาง ทำหน้าที่จับคู่ระหว่าง URL ที่เบราว์เซอร์เรียก กับฟังก์ชันใน Controller ที่ต้องทำงาน เช่น `Route::get('/dashboard', [DashboardController::class, 'index'])`
2. **Controller (`app/Http/Controllers/`):**
   - คือผู้จัดการรับเรื่อง ตรวจสอบความถูกต้องของข้อมูลที่ส่งมา (Validate) สั่งงาน Model ให้อ่าน/บันทึกฐานข้อมูล แล้วส่งต่อผลลัพธ์ไปยัง View
3. **Model (`app/Models/`):**
   - คือตัวแทนของตารางในฐานข้อมูล (Eloquent ORM) ช่วยให้เราอ่านและเขียนข้อมูลในรูปแบบ Object ได้โดยไม่ต้องเขียนคำสั่ง SQL ดิบ เช่น `Seat::where('status', 'available')->get()` แทนการเขียน `SELECT * FROM seats WHERE status = 'available'`
4. **Blade View (`resources/views/`):**
   - คือแม่แบบไฟล์ HTML ที่ผสมคำสั่งแสดงผลของ Laravel (`{{ ... }}`, `@if`, `@foreach`) เพื่อนำข้อมูลจาก Controller มาจัดแสดงหน้าจอให้ผู้ใช้เห็น
5. **Middleware (`auth`):**
   - คือประตูกรองความปลอดภัย ทำหน้าที่ตรวจสอบว่าผู้ใช้ล็อกอินหรือยังก่อนจะยอมให้เข้าถึง Route นั้นๆ หากยังไม่ล็อกอินจะส่งกลับไปหน้าล็อกอินอัตโนมัติ
