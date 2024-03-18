<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\CreateDepartment;
use App\Http\Requests\Department\UpdateDepartment;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::with(['user_manage','users','regencies'])->orderBy('id', 'DESC')->get();
        return $departments;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request  $request)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $code = $request->input('code');
            $campuses = $request->input('campuses');
            $ids_campuses = collect($campuses)->pluck('id')->toArray();
            $type_office = $request->input('type_office');
            $user_id = $request->input('user_id');
            if ($type_office == 1){
                $type_office = 0;
            }else{
                $type_office = 1;
            }
            Department::create([
                'title' => $title,
                'code' => $code,
                'type_office' => $type_office,
                'campuses' => $ids_campuses,
                'user_id' => $user_id['id'],
                'active' => 1
            ]);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Đã thêm mới phòng ban',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'result' => 'Chưa thêm được phòng ban',
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request  $request, $id)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $code = $request->input('code');
            $campuses = $request->input('campuses');
            $ids_campuses = collect($campuses)->pluck('id')->toArray();
            $type_office = $request->input('type_office');
            $user_id = $request->input('user_id');
            if ($type_office == 1){
                $type_office = 0;
            }else{
                $type_office = 1;
            }
            $department = Department::findOrFail($id);
            $department->update([
                'title' => $title,
                'code' => $code,
                'type_office' => $type_office,
                'campuses' => $ids_campuses,
                'user_id' => $user_id['id'],
                'active' => 1
            ]);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Cập nhật thành công phòng ban',
            ));
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'result' => 'Chưa cập nhật được phòng ban',
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        //
    }
}
