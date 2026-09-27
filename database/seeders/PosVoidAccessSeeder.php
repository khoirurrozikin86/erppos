<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PosVoidAccessSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(['void', 'void-closed-session'])->map(function (string $action) {
            $permission = Permission::firstOrCreate([
                'name' => "pos.{$action}",
                'guard_name' => 'web',
            ]);
            if (Schema::hasColumn($permission->getTable(), 'group_name') && $permission->group_name !== 'pos') {
                $permission->group_name = 'pos';
                $permission->save();
            }

            return $permission;
        });

        Role::query()->where('guard_name', 'web')->whereIn('name', ['admin', 'super_admin'])
            ->get()->each(fn (Role $role) => $role->givePermissionTo($permissions));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}