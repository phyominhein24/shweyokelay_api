<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = [
            'id' => '1',
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '755704230',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'created_by' => '1',
            'updated_by' => '1',
            'status' => 'ACTIVE'
        ];

        $role = RoleEnum::SUPER_ADMIN->value;

        try {

            $user = User::updateOrCreate($superAdmin)->assignRole($role);

        } catch (Exception $e) {

            info($e);

        }
    }
}
