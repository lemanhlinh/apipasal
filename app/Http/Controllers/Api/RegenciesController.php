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
        $regencies = Regencies::with(['department'])->get();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRegencies $req)
    {
        DB::beginTransaction();
        try {
            $data = $req->validated();
            Regencies::create([
                'title' => $data['title'],
                'code' =>  $data['code'],
                'department_id' => $data['department_id'],
                'permission' => $data['permission'],
                'active' => 1
            ]);

            DB::commit();
            Session::flash('success', 'Đã thêm mới chức vụ mới');
            return redirect()->back();
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            Session::flash('danger', 'Chưa thêm được chức vụ');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Regencies  $regencies
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Regencies  $regencies
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRegencies $req, $id)
    {
        DB::beginTransaction();
        try {
            $data = $req->validated();
            $department = Regencies::findOrFail($id);
            $department->update([
                'title' => $data['title'],
                'code' =>  $data['code'],
                'department_id' => $data['department_id'],
                'permission' => $data['permission'],
                'active' => 1
            ]);

            DB::commit();
            Session::flash('success', 'Cập nhật thành công chức vụ');
            return redirect()->route('admin.article.edit', $id);
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            Session::flash('danger', 'Chưa cập nhật được chức vụ');
            return back();
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
}
