<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Regencies;
use App\Models\Role as RoleModel;
use App\Models\User;

use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list_permission = Permission::orderBy('display_name', 'ASC')->get();
        return $list_permission;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rolePermission()
    {
        $regency_code = request()->regency_code;
        $role = Role::where('name', $regency_code)->first();
        
        $list_permission = Permission::orderBy('display_name', 'ASC')->with('role_permission', function ($query) use ($role) {
            $query->where('role_id', $role->id);
        })->get();

        return $list_permission;
    }

    public function savePermission()
    {
        $regency_code = request()->regency_code;
        $regency = Regencies::where('code', $regency_code)->first();
        $role = Role::where('name', $regency_code)->first();
        $permissions = request()->permissions;
    
        DB::beginTransaction();
    
        try {
            foreach ($permissions as $item) {
                $edit = @$item['role']['edit'];
                $create = @$item['role']['create'];
                $delete = @$item['role']['delete'];
                $view = @$item['role']['view'];
    
                $edit_task = 'edit' . '_' . $item['module'];
                $create_task = 'create' . '_' . $item['module'];
                $delete_task = 'delete' . '_' . $item['module'];
                $view_task = 'view' . '_' . $item['module'];
    
                $array_task = [
                    $edit_task,
                    $create_task,
                    $delete_task,
                    $view_task,
                ];
                $array_permission = Permission::whereIn('name', $array_task)->get();
                $existingPermissions = RoleModel::where('role_code', $role->code)->pluck('permission_id')->toArray();
                $permissionsToAdd = [];
                $permissionsToRemove = [];
    
                foreach ($array_permission as $val) {
                    if ($edit_task == $val->name) {
                        if ($edit == 1 && !in_array($val->id, $existingPermissions)) {
                            $permissionsToAdd[] = $val->id;
                        } elseif ($edit == 0 && in_array($val->id, $existingPermissions)) {
                            $permissionsToRemove[] = $val->id;
                        }
                    }
    
                    if ($create_task == $val->name) {
                        if ($create == 1 && !in_array($val->id, $existingPermissions)) {
                            $permissionsToAdd[] = $val->id;
                        } elseif ($create == 0 && in_array($val->id, $existingPermissions)) {
                            $permissionsToRemove[] = $val->id;
                        }
                    }
    
                    if ($delete_task == $val->name) {
                        if ($delete == 1 && !in_array($val->id, $existingPermissions)) {
                            $permissionsToAdd[] = $val->id;
                        } elseif ($delete == 0 && in_array($val->id, $existingPermissions)) {
                            $permissionsToRemove[] = $val->id;
                        }
                    }
    
                    if ($view_task == $val->name) {
                        if ($view == 1 && !in_array($val->id, $existingPermissions)) {
                            $permissionsToAdd[] = $val->id;
                        } elseif ($view == 0 && in_array($val->id, $existingPermissions)) {
                            $permissionsToRemove[] = $val->id;
                        }
                    }
                }
    
                if (!empty($permissionsToAdd)) {
                    $dataToAdd = array_map(function($permissionId) use ($role) {
                        return [
                            'role_id' => $role->id,
                            'role_code' => $role->code, 
                            'permission_id' => $permissionId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }, $permissionsToAdd);
                    RoleModel::insert($dataToAdd);
                }
    
                if (!empty($permissionsToRemove)) {
                    RoleModel::where('role_code', $role->code)->whereIn('permission_id', $permissionsToRemove)->delete();
                }

                $user = User::where('regency_id', $regency->id)->first();
                $role_permission = RoleModel::where('role_id', $role->code)->get();
                $permissions = Permission::whereIn('id', $role_permission->pluck('permission_id'))->get();
                $allPermissionNames = $permissions->pluck('name')->toArray();
                $role = Role::updateOrCreate([
                    'name' => $regency->code,
                    'display_name' => $regency->title,
                    'guard_name' => 'api',
                ]);

                $role->givePermissionTo($allPermissionNames);
                if ($user) {
                    $user->assignRole($role);
                }

            }
    
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['message' => 'Lỗi xảy ra ' . $e->getMessage(), 'success' => false], 500);
        }
    }

    public function clonePermission() {
        $role_id = request()->role_id;
        $role_id_clone = request()->role_id_clone;

        $permissions = Role::where('role_id', $role_id_clone)->get();
        $data = [];
        foreach ($permissions as $permission) {
            $data[] = [
                'role_id' => $role_id,
                'permission_id' => $permission->permission_id
            ];
        }
        Role::insert($data);
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request  $request) {}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */
    public function update(Request  $request, $id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */
}
