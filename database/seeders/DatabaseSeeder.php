<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            RoleHasPermissionSeeder::class,
            SuperAdminSeeder::class,

            CountersSeeder::class,
            VehiclesTypesSeeder::class,
            RoutesSeeder::class,
            PaymentsSeeder::class,
            PaymentHistoriesSeeder::class,
        
        ]);

        // \App\Models\User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        // ]);
    }
}

// 1️⃣ Counters Seeder
class CountersSeeder extends Seeder
{
    public function run()
    {
        DB::table('counters')->insert([
            [
                'name' => 'Yangon Counter',
                'phone' => '09987654321',
                'city' => 'Yangon',
                'terminal' => 'Aung Mingalar Terminal',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mandalay Counter',
                'phone' => '09912345678',
                'city' => 'Mandalay',
                'terminal' => 'Chan Mya Thar Si Terminal',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

// 2️⃣ Vehicles Types Seeder
class VehiclesTypesSeeder extends Seeder
{
    public function run()
    {
        DB::table('vehicles_types')->insert([
            [
                'name' => 'VIP Bus',
                'seat_layout' => '2-1',
                'total_seat' => 30,
                'facilities' => "[\"fdsafd\",\"fdsafdf\"]",
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Economy Bus',
                'seat_layout' => '2-2',
                'total_seat' => 40,
                'facilities' => "[\"fdsafd\",\"fdsafdf\"]",
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

// 3️⃣ Routes Seeder
class RoutesSeeder extends Seeder
{
    public function run()
    {
        DB::table('routes')->insert([
            [
                'name' => 'Yangon to Mandalay',
                'vehicles_type_id' => 1,
                'starting_point' => 1,
                'ending_point' => 2,
                'distance' => '620km',
                'duration' => '8 hours',
                'is_ac' => true,
                'day_off' => json_encode(['Sunday']),
                // 'start_date' => now(),
                'price' => '20000',
                'fprice' => '18000',
                'last_min' => '30',
                'cancle_booking' => '2',
                'departure' => Carbon::now()->addHours(2),
                'arrivals' => Carbon::now()->addHours(10),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}

// 4️⃣ Payments Seeder
class PaymentsSeeder extends Seeder
{
    public function run()
    {
        DB::table('payments')->insert([
            [
                'name' => 'KBZ Pay',
                'photo' => 'kbzpay.png',
                'acc_name' => 'John Doe',
                'acc_number' => '1234567890',
                'acc_qr' => 'kbz_qr_code.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}

// 5️⃣ Payment Histories Seeder
class PaymentHistoriesSeeder extends Seeder
{
    public function run()
    {
        DB::table('payment_histories')->insert([
            [
                'member_id' => null,
                'route_id' => 1,
                'payment_id' => 1,
                'screenshot' => 'receipt.png',
                'phone' => '09987654321',
                'nrc' => '12/ABC(N)123456',
                'seat' => json_encode(['A1', 'A2']),
                'total' => 40000,
                'note' => 'Paid in full',
                'start_time' => Carbon::now(),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}