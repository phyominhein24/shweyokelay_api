<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Helpers\Enum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = collect(Enum::make(RoleEnum::class)->values())->map(function ($role) {
            try {

                $createRole = Role::create([
                    'name' => $role,
                    'description' => 'hello',
                    'guard_name' => 'api',
                    'created_by' => '1',
                    'updated_by' => '1',
                ]);

            } catch (Exception $e) {
                info($e);
            }
        });
    }
}
