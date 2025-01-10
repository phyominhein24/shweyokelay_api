<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Helpers\Enum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleHasPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Enum::make(PermissionEnum::class)->values();

        $roles = Role::all()->map(function ($role) use ($permissions) {

            if ($role->name === RoleEnum::SUPER_ADMIN->value) {

                $role->syncPermissions($permissions);
            }

        });
    }
}
