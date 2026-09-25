<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Package;
use App\Models\Product;
use App\Models\Seat;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GamingCafeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Users
        $usersData = [
            [
                'name' => 'ผู้จัดการร้าน (Admin)',
                'username' => 'admin',
                'email' => 'admin@pcbang.test',
                'phone' => '0812345678',
                'role' => 'admin',
                'balance' => 1000.00,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'พนักงานประจำร้าน (Staff)',
                'username' => 'staff',
                'email' => 'staff@pcbang.test',
                'phone' => '0823456789',
                'role' => 'staff',
                'balance' => 500.00,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'ลูกค้าประจำ 1 (Somchai)',
                'username' => 'customer1',
                'email' => 'cust1@pcbang.test',
                'phone' => '0834567890',
                'role' => 'customer',
                'balance' => 300.00,
                'password' => Hash::make('1'),
            ],
            [
                'name' => 'ลูกค้าใหม่ 2 (Somsri)',
                'username' => 'customer2',
                'email' => 'cust2@pcbang.test',
                'phone' => '0845678901',
                'role' => 'customer',
                'balance' => 50.00,
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($usersData as $data) {
            $user = User::create($data);
            // Record initial topup in audit log
            if ($user->balance > 0) {
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'topup',
                    'amount' => $user->balance,
                    'ref_type' => 'initial_seed',
                    'ref_id' => null,
                ]);
            }
        }

        // 2. Zones
        $zoneStd = Zone::create([
            'name' => 'Standard Zone (จอ 165Hz)',
            'hourly_rate' => 40.00,
            'description' => 'คอมพิวเตอร์สเปกมาตรฐาน i5 + RTX 4060 จอ 165Hz เก้าอี้เกมมิ่ง',
        ]);

        $zoneVip = Zone::create([
            'name' => 'VIP High-End Zone (จอ 240Hz)',
            'hourly_rate' => 60.00,
            'description' => 'คอมพิวเตอร์สเปกไฮเอนด์ i7 + RTX 4080 จอ 240Hz หูฟังพรีเมียม',
        ]);

        $zoneDuo = Zone::create([
            'name' => 'Duo Private Room (ห้องคู่)',
            'hourly_rate' => 100.00,
            'description' => 'ห้องกระจกส่วนตัว 2 ที่นั่ง โซฟานุ่ม เหมาะสำหรับมาเล่นกับเพื่อนหรือแฟน',
        ]);

        // 3. Seats
        for ($i = 1; $i <= 6; $i++) {
            Seat::create([
                'zone_id' => $zoneStd->id,
                'seat_number' => sprintf('STD-%02d', $i),
                'status' => 'available',
            ]);
        }

        for ($i = 1; $i <= 4; $i++) {
            Seat::create([
                'zone_id' => $zoneVip->id,
                'seat_number' => sprintf('VIP-%02d', $i),
                'status' => 'available',
            ]);
        }

        for ($i = 1; $i <= 2; $i++) {
            Seat::create([
                'zone_id' => $zoneDuo->id,
                'seat_number' => sprintf('DUO-%02d', $i),
                'status' => 'available',
            ]);
        }

        // 4. Packages
        Package::create([
            'zone_id' => null, // all zones
            'name' => 'แพ็กเกจ 2 ชั่วโมง',
            'duration_hours' => 2,
            'price' => 70.00,
        ]);

        Package::create([
            'zone_id' => null,
            'name' => 'แพ็กเกจ 5 ชั่วโมง (สุดคุ้ม)',
            'duration_hours' => 5,
            'price' => 150.00,
        ]);

        Package::create([
            'zone_id' => null,
            'name' => 'แพ็กเกจโต้รุ่ง 8 ชั่วโมง (Night Owl)',
            'duration_hours' => 8,
            'price' => 220.00,
        ]);

        // 5. Food Categories & Products
        $catRamyeon = Category::create(['name' => 'รามยอน (Korean Ramyeon)']);
        $catRice = Category::create(['name' => 'ข้าวและอาหารจานเดียว (Rice Bowls)']);
        $catSnack = Category::create(['name' => 'ของทานเล่น (Snacks)']);
        $catDrink = Category::create(['name' => 'เครื่องดื่ม (Beverages)']);

        // Products
        Product::create([
            'category_id' => $catRamyeon->id,
            'name' => 'ชินรามยอนต้มใส่ไข่และชีส',
            'description' => 'บะหมี่เกาหลีเผ็ดร้อน เสิร์ฟในหม้อทองเหลืองร้อนๆ ท็อปปิ้งไข่สดและเชดด้าชีส',
            'price' => 79.00,
            'stock_quantity' => 50,
        ]);

        Product::create([
            'category_id' => $catRamyeon->id,
            'name' => 'จาปาเก็ตตี้ไข่ดาว',
            'description' => 'บะหมี่ซอสดำเกาหลีรสเข้มข้น หอมน้ำมันงา เสิร์ฟพร้อมไข่ดาวเยิ้มๆ',
            'price' => 85.00,
            'stock_quantity' => 40,
        ]);

        Product::create([
            'category_id' => $catRice->id,
            'name' => 'ข้าวหน้าหมูผัดกิมจิไข่ดาว',
            'description' => 'สันคอหมูนุ่มผัดซอสกิมจิรสแซ่บ เสิร์ฟพร้อมข้าวสวยร้อนๆ และไข่ดาว',
            'price' => 99.00,
            'stock_quantity' => 30,
        ]);

        Product::create([
            'category_id' => $catRice->id,
            'name' => 'ข้าวหน้าไก่ทอดซอสเกาหลี',
            'description' => 'ไก่ทอดกรอบคลุกซอสยังนยอมเกาหลี หวานเผ็ดลงตัว',
            'price' => 89.00,
            'stock_quantity' => 35,
        ]);

        Product::create([
            'category_id' => $catSnack->id,
            'name' => 'ต๊อกบกกีชีสยืด',
            'description' => 'แป้งต๊อกเหนียวนุ่มในซอสโคชูจังเข้มข้น โรยชีสยืดแบบจัดเต็ม',
            'price' => 89.00,
            'stock_quantity' => 25,
        ]);

        Product::create([
            'category_id' => $catSnack->id,
            'name' => 'เชคฟรายส์คลุกผงหัวหอม',
            'description' => 'เฟรนช์ฟรายส์ทอดกรอบ คลุกผงซาวครีมหัวหอมเข้มข้น',
            'price' => 49.00,
            'stock_quantity' => 60,
        ]);

        Product::create([
            'category_id' => $catDrink->id,
            'name' => 'อเมริกาโน่เย็น (Iced Americano)',
            'description' => 'กาแฟเอสเพรสโซ่ช็อตเข้มข้น เมนูยอดฮิตประจำร้านเน็ตเกาหลี',
            'price' => 45.00,
            'stock_quantity' => 100,
        ]);

        Product::create([
            'category_id' => $catDrink->id,
            'name' => 'ชาพีชโซดาเย็น',
            'description' => 'ชาพีชหอมหวานผสมความซ่าสดชื่น ดับกระหายคลายง่วง',
            'price' => 45.00,
            'stock_quantity' => 100,
        ]);

        Product::create([
            'category_id' => $catDrink->id,
            'name' => 'โค้กกระป๋อง (325 มล.)',
            'description' => 'โค้กออริจินัลเย็นเจี๊ยบ',
            'price' => 25.00,
            'stock_quantity' => 120,
        ]);
    }
}
