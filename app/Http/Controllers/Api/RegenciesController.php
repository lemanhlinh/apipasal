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

class RegenciesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $regencies = Regencies::with(['department'])->orderBy('id', 'DESC')->get();
        return $regencies;
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
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $code = $request->input('code');
            $department_id = $request->input('department_id');
            $permission = $request->input('permission');
            Regencies::create([
                'title' => $title,
                'code' =>  $code,
                'department_id' => $department_id['id'],
                'permission' => $permission,
                'active' => 1
            ]);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'result' => 'Đã thêm mới chức vụ mới',
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
                'result' => 'Chưa thêm được chức vụ',
            ));
        }
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
            $title = $request->input('title');
            $code = $request->input('code');
            $department_id = $request->input('department_id');
            $permission = $request->input('permission');
            $regencies = Regencies::findOrFail($id);
            $regencies->update([
                'title' => $title,
                'code' =>  $code,
                'department_id' => $department_id,
                'permission' => $permission,
                'active' => 1
            ]);

            DB::commit();
            return response()->json(array(
                'error' => true,
                'result' => 'Cập nhật thành công chức vụ',
            ));
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'result' => 'Chưa cập nhật được chức vụ',
            ));
        }
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
