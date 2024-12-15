<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Helpers\Enum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = collect(Enum::make(PermissionEnum::class)->values())->map(function ($permission, $key) {

            return [
                'name' => $permission,
                'guard_name' => 'api',
            ];
        });

        try {

            Permission::insert($permissions->toArray());

        } catch (Exception $e) {
            info($e);
        }
    }
}
