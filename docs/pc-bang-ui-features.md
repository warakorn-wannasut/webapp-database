# เอกสารระบบ UI คอมพิวเตอร์ร้านเกม (PC Bang Client Interface)

เอกสารนี้อธิบายระบบจำลองหน้าจอคอมพิวเตอร์ร้านเกม (PC Bang / Gaming Cafe Client UI) ที่ใช้งานในโปรเจกต์ จำนวน 3 ส่วนหลัก

---

## 1. ภาพรวมระบบที่เปิดใช้งานในปัจจุบัน

1. **Marquee Bar (แถบข้อความวิ่ง):** แถบวิ่งตัวหนังสือข่าวสาร โปรโมชันเหมาดึก ทัวร์นาเมนต์ และเมนูครัว อยู่ส่วนบนสุดของหน้าแดชบอร์ด หยุดวิ่งอัตโนมัติเมื่อนำเมาส์ไปชี้ (Pause on hover)
2. **Time Warning Alert (ระบบป๊อปอัปแจ้งเตือนก่อนเวลาหมด 15 นาที):** เมื่อเวลาใช้งานของลูกค้าเหลือไม่ถึงหรือเท่ากับ 15 นาที ระบบจะแสดงแบนเนอร์สีแดงเตือนบนแดชบอร์ด พร้อมเปิดป๊อปอัป Modal แจ้งเตือนยอดเงินและเครื่อง เพื่อให้กดเติมเงินหรือซื้อแพ็กเกจต่อเวลาได้ทันที
3. **Floating Status Bar (แถบเวลาลอยมุมจอ / PC Bang Client HUD):** วิดเจ็ตลอยอยู่มุมขวาล่างของจอ แสดงหมายเลขเครื่อง เวลาใช้งานหรือเวลานับถอยหลังแบบเรียลไทม์ (นับวินาทีจริงในหน้าเว็บ) ค่าบริการสะสม พร้อมปุ่มลัดสั่งอาหาร เติมเงิน เช็คเอาท์ และปุ่มย่อ/ขยายขนาด

---

## 2. ไฟล์ที่เกี่ยวข้องในระบบ

| ลำดับ | ไฟล์ | ประเภทไฟล์ | หน้าที่ในระบบ |
|---|---|---|---|
| 1 | `app/Http/Controllers/DashboardController.php` | Controller | คำนวณเวลาคงเหลือ `$sessionRemainingMinutes` จากแพ็กเกจหรือยอดเงิน ส่งไปยัง View |
| 2 | `resources/css/app.css` | Stylesheet | กำหนด `@keyframes marquee-scroll`, `.animate-marquee` และคลาสเอฟเฟกต์แสง `.pulse-glow-red` |
| 3 | `resources/views/pages/customer/dashboard.blade.php` | Blade View | แสดงผล Marquee, Time Warning (Banner & Modal) และ Floating Client HUD |
| 4 | `tests/Feature/DashboardTest.php` | Feature Test | ตรวจสอบความถูกต้องของการแสดงผล Marquee, การแสดง HUD เมื่อมีเซสชัน และการเตือนเมื่อเวลาใกล้หมด |

---

## 3. การทำงานและเส้นทางข้อมูลข้าม Layer (Data Flow)

### 3.1 เส้นทางของ Time Warning Alert (เตือน 15 นาที)
```text
เบราว์เซอร์ส่ง GET /dashboard
  → DashboardController@index
  → ตรวจสอบ $activeSession (ถ้ามีเซสชันที่กำลังเล่นอยู่)
  → คำนวณเวลาที่ใช้ไป: $elapsedMinutes = ceil($usedSeconds / 60)
  → คำนวณเวลาคงเหลือ:
      - ถ้าใช้แพ็กเกจ: $sessionRemainingMinutes = max(0, $packageMins - $elapsedMinutes)
      - ถ้าใช้เงินสด: $sessionRemainingMinutes = max(0, floor(($balance / $rate) * 60) - $elapsedMinutes)
  → ส่ง $sessionRemainingMinutes สู่ View
  → Blade ตรวจสอบ: @if ($sessionRemainingMinutes !== null && $sessionRemainingMinutes <= 15)
  → แสดงแบนเนอร์แจ้งเตือนสีแดงกะพริบ + แสดง Modal แจ้งเตือนผ่าน Alpine.js (showWarningModal = true)
```

### 3.2 เส้นทางของ Floating Status Bar (Client HUD)
```text
DashboardController ส่งข้อมูล $activeSession, $elapsedMinutes, $estimatedCost, $sessionRemainingMinutes เข้าสู่ View
  → Blade ตรวจสอบ: @if ($activeSession)
  → Alpine.js สร้าง State:
      - elapsedSeconds = $elapsedMinutes * 60
      - remainingSeconds = $sessionRemainingMinutes * 60
      - setInterval นับเวลาเพิ่ม/ลด ทุก 1 วินาทีในฝั่งเบราว์เซอร์
      - minimized = false (สลับย่อเป็นแคปซูลเม็ดเล็กหรือขยายเป็นการ์ดเต็ม)
  → ปุ่มสั่งอาหาร: ลิงก์ตรงไปที่ route('customer.food-order', ['seat_id' => $activeSession->seat_id])
  → ปุ่มเติมเงิน: ลิงก์ตรงไปที่ route('customer.topup')
  → ปุ่มเช็คเอาท์: ส่งฟอร์ม POST ไปที่ route('customer.check-out')
```

---

## 4. รายละเอียดตัวแปรที่ใช้งาน

| ชื่อตัวแปร | มาจากไหน | ชนิดและรูปแบบข้อมูล | ถูกส่งต่อไปที่ไหน |
|---|---|---|---|
| `$sessionRemainingMinutes` | คำนวณใน `DashboardController@index` | `int` หรือ `null` เช่น `10`, `45`, `null` (กรณีไม่ได้เปิดเครื่อง) | ส่งผ่าน `compact()` เข้า View เพื่อตัดสินใจว่าจะแสดงกล่องแจ้งเตือน 15 นาทีหรือไม่ |
| `showWarningModal` | Alpine.js กำหนดค่าเริ่มต้นจาก Blade `{{ $sessionRemainingMinutes <= 15 ? 'true' : 'false' }}` | `boolean` | ควบคุมการเปิด/ปิดหน้าต่างป๊อปอัปเตือนเวลาใกล้หมด |
| `minimized` | ตัวแปร State ของ Alpine.js ใน Floating HUD | `boolean` (`true` = ย่อเป็นเม็ดแคปซูล, `false` = แสดงการ์ดเต็ม) | ควบคุมการสลับหน้าตา HUD ลอยมุมจอ |
| `elapsedSeconds` / `remainingSeconds` | รับค่าเริ่มต้นเป็นวินาทีจาก Blade แล้วนับต่อใน JavaScript | `number` | ใช้แสดงตัวเลขนับถอยหลังหรือนับเวลาเดินหน้าแบบเรียลไทม์ทุก 1 วินาที |

---

## 5. คำสั่งและเครื่องมือของ Laravel ที่ใช้

1. `compact('sessionRemainingMinutes', ...)`
   - มาจาก: PHP Standard Function (ที่ Laravel นิยมใช้ส่งตัวแปรไปยัง View)
   - หน้าที่: สร้าง Associative Array จากชื่อตัวแปรที่ระบุ
   - หากไม่มี Laravel: ต้องเขียนเป็น `['sessionRemainingMinutes' => $sessionRemainingMinutes]` ส่งให้ Template Engine
2. `Carbon::parse($activeSession->start_time)->diffInSeconds(Carbon::now())`
   - มาจาก: แพ็กเกจ `Nesbot\Carbon` ที่ถูกห่อหุ้มใน Laravel ผ่าน `Illuminate\Support\Carbon`
   - หน้าที่: คำนวณส่วนต่างระหว่างสองเวลาออกมาเป็นหน่วยวินาที
   - หากไม่มี Laravel: ต้องใช้ `strtotime($now) - strtotime($startTime)` ใน PHP ดั้งเดิม
3. `route('customer.food-order', ['seat_id' => $activeSession->seat_id])`
   - มาจาก: Helper function ของ Laravel (`Illuminate\Routing\UrlGenerator`)
   - หน้าที่: แปลงชื่อ Route ให้กลายเป็น URL เต็มพร้อมแนบ Query Parameter เช่น `/food-order?seat_id=1`
   - หากไม่มี Laravel: ต้องต่อสตริง URL เอง เช่น `"/food-order.php?seat_id=" . urlencode($seatId)`
