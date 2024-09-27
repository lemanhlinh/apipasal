<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Regencies\CreateRegencies;
use App\Http\Requests\Regencies\UpdateRegencies;
use App\Models\Campuses;
use App\Models\Regencies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use App\Models\Role as RoleModel;
use App\Models\User;
use App\Models\Permission as PermissionModel;
use App\Services\System\RolePermission;

class RegenciesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $rolePermission;

    public function __construct(RolePermission $rolePermission)
    {
        $this->rolePermission = $rolePermission;
    }

    public function index()
    {
        $regencies = Regencies::with(['department'])->orderBy('id', 'DESC')->get();
        return response()->json([
            'success' => true,
            'data' => $regencies,
            'message' => 'Lấy dữ liệu thành công'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $title = $request->input('title');
        $code = $request->input('code');
        $check_exists = Regencies::where('code', $code)->first();
        if ($check_exists) {
            return response()->json(array(
                'success' => false,
                'message' => 'Mã chức vụ đã tồn tại',
            ));
        }
        if (@$request->input('permission'))
            $permission = $request->input('permission');

        if (@$permission) {
            $role = Role::where('name', $permission['code'])->first();
            $role_permission = RoleModel::where('role_id', $role->id)->get();

            $regency = Regencies::create([
                'title' => $title,
                'code' =>  $code,
                'active' => 1
            ]);

            $new_role = Role::updateOrCreate(
                ['name' => $regency->code],
                [
                    'display_name' => $regency->title,
                    'guard_name' => 'api',
                ]
            );
            if ($role_permission) {
                foreach ($role_permission as $item) {
                    RoleModel::firstOrCreate(
                        [
                            'permission_id' => $item->permission_id,
                            'role_id' => $new_role->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ],
                        [
                            'permission_id' => $item->permission_id,
                            'role_id' => $new_role->id
                        ]
                    );
                }
            }
        } else {
            $regency = Regencies::create([
                'title' => $title,
                'code' =>  $code,
                'active' => 1
            ]);

            $role = Role::updateOrCreate([
                'name' => $regency->code,
                'display_name' => $regency->title,
                'guard_name' => 'api',
            ]);
        }

        return response()->json(array(
            'success' => true,
            'message' => 'Đã thêm mới chức vụ mới',
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Regencies $regencies
     * @return \Illuminate\Http\Response
     */
    public function show(Regencies $regencies)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Regencies  $regencies
     * @return \Illuminate\Http\Response
     */
    public function edit(Regencies $regencies)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Regencies $regencies
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $code = $request->input('code');
            $title = $request->input('title');

            $check_exists = Regencies::where('code', $code)->where('id', '!=', $id)->first();
            if ($check_exists) {
                return response()->json(array(
                    'success' => false,
                    'message' => 'Mã chức vụ đã tồn tại',
                ));
            }
            
            $regency = Regencies::find($id);

            if ($regency) {
                Role::where('name', $regency->code)->delete();
            
                $newRegency = Regencies::create([
                    'title' => $title,
                    'code' => $code,
                ]);
            
                Role::create([
                    'name' => $newRegency->code,
                    'display_name' => $newRegency->title,
                    'guard_name' => 'api',
                ]);
            
                $regency->delete();
            }

            if (@$request->input('permission'))
                $permission = $request->input('permission');

            if (@$permission) {
                $current_role = Role::where('name', $code)->first();
                $role_clone = Role::where('name', $permission['code'])->first();
                $role_permission = RoleModel::where('role_id', $role_clone->id)->get();
                if (!empty($role_permission)) {
                    RoleModel::where('role_id', @$current_role->id)->delete();

                    foreach ($role_permission as $item) {
                        RoleModel::firstOrCreate(
                            [
                                'permission_id' => $item->permission_id,
                                'role_id' => $current_role->id
                            ],
                            [
                                'permission_id' => $item->permission_id,
                                'role_id' => $current_role->id
                            ]
                        );
                    }
                }
            }
            DB::commit();
            return response()->json(array(
                'success' => true,
                'message' => 'Cập nhật chức vụ thành công',
            ));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array(
                'success' => false,
                'message' => 'Có lỗi xảy ra ' . $e->getMessage(),
            ));
        }

    }

    public function delete() {
        $id = request()->input('id');
        $regency = Regencies::find($id);
        Role::where('name', $regency->code)->delete();
        $regency->delete();
        return response()->json(array(
            'success' => true,
            'message' => 'Xóa chức vụ thành công',
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Regencies  $regencies
     * @return \Illuminate\Http\Response
     */
    public function destroy(Regencies $regencies)
    {
        //
    }

    /**
     * @param $id
     * @return array
     */
    public function changeActive($id)
    {
        $regencies = Regencies::findOrFail($id);
        $regencies->update(['active' => !$regencies->active]);
        return [
            'status' => true,
            'message' => trans('message.change_active_article_success')
        ];
    }
}
