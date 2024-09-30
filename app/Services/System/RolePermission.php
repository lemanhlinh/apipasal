<?php 

namespace App\Services\System;
use Spatie\Permission\Models\Role;
use App\Models\Role as RoleModel;
use App\Models\User;
use App\Models\Permission as PermissionModel;
use App\Models\Regencies;

class RolePermission {
    public function __construct()
    {
        
    }

    public function applyRolePermissionToUser($user_id)
    {
        $user = User::with('regency')->find($user_id);
        $role = Role::where('name', $user->regency->code)->first();
        if ($role) {
            $permissions = PermissionModel::whereIn('id', RoleModel::where('role_id', $role->id)->pluck('permission_id'))->pluck('name')->toArray();
            $user->syncRoles([]);
            $user->assignRole($role);
            $role->syncPermissions($permissions);
        }

    }
}