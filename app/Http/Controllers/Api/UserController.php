<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUser;
use App\Http\Requests\User\UpdateUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\System\RolePermission;


class UserController extends Controller
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
        $users = User::with(['department','regency'])->orderBy('id', 'DESC')->get();
        foreach ($users as $user){
            if ($user->department){
                $user->department->title_rename = $user->department->title;
            }
        }
        return $users;
    }

//    public function login(Request $request)
//    {
//        $request->validate([
//            'email' => 'required|email',
//            'password' => 'required',
//        ]);
//
//        $remember = $request->has('remember');
//
//        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
//            return response()->json(['success' => true, 'user' => Auth::user()]);
//        }
//        return response()->json(['success' => false, 'message' => 'Invalid credentials'], 200);
//    }


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
        DB::beginTransaction();
        try {
            $name = $request->input('name');
            $email = $request->input('email');
            $phone = $request->input('phone');
            $password = $request->input('password');
            $birthday = $request->input('birthday');
            $image = $request->input('image');
            $department_id = $request->input('department_id');
            $department = $request->input('department');
            $regency_id = $request->input('regency_id');
            $regency = $request->input('regency');
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'phone' => $phone,
                'birthday' => Carbon::parse($birthday)->toDateString(),
                'image' => $image,
                'department_id' => $department['id'],
                'regency_id' => $regency['id'],
                'used_time' => now(),
                'active' => 1
            ]);

            DB::commit();
            return response()->json(array(
                'success' => false,
                'message' => 'Đã thêm mới user',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'success' => true,
                'message' => 'Chưa thêm được user',
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $name = $request->input('name');
            $email = $request->input('email');
            $phone = $request->input('phone');
            $password = $request->input('password');
            $birthday = $request->input('birthday');
            $image = $request->input('image');
            $department_id = $request->input('department_id');
            $department = $request->input('department');
            $regency_id = $request->input('regency_id');
            $regency = $request->input('regency');
            $user = User::findOrFail($id);
            $user->update([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'phone' => $phone,
                'birthday' => Carbon::parse($birthday)->toDateString(),
                'image' => $image,
                'department_id' => $department['id'],
                'regency_id' => $regency['id'],
                'used_time' => now(),
                'active' => 1
            ]);

            $this->rolePermission->applyRolePermissionToUser($id);

            DB::commit();
            return response()->json(array(
                'success' => false,
                'message' => 'Cập nhật thành công user',
            ));
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'success' => true,
                'message' => 'Chưa cập nhật được user',
            ));
        }
    }

    public function delete ($id) {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->delete();
            DB::commit();
            return response()->json(array(
                'success' => true,
                'message' => 'Đã xóa user',
            ));
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'message' => 'Chưa xóa được user ' . $exception->getMessage(),
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param $id
     * @return array
     */
    public function changeActive($id)
    {
        $user = User::findOrFail($id);
        $user->update(['active' => !$user->active]);
        return [
            'status' => true,
            'message' => trans('message.change_active_article_success')
        ];
    }

 
}
