<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\CreateTimeStudy;
use App\Http\Requests\Schedule\UpdateTimeStudy;
use App\Models\CourseCategories;
use App\Models\TimeStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TimeStudyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $timeStudy = TimeStudy::all();
        return $timeStudy;
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
    public function store(CreateTimeStudy $req)
    {
        DB::beginTransaction();
        try {
            $data = $req->validated();
            TimeStudy::create([
                'title' => $data['title'],
                'active' => 1,
            ]);

            DB::commit();
            Session::flash('success', 'Đã thêm mới Ca học');
            return redirect()->back();
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            Session::flash('danger', 'Chưa thêm được Ca học');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */
    public function show(TimeStudy $timeStudy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeStudy $timeStudy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimeStudy $req, $id)
    {
        DB::beginTransaction();
        try {
            $data = $req->validated();
            $cat = TimeStudy::findOrFail($id);
            $cat->update([
                'title' => $data['title'],
                'active' => 1,
            ]);
            DB::commit();
            Session::flash('success', 'Cập nhật thành công');
            return redirect()->back();
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            Session::flash('danger', 'Chưa cập nhật được');
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TimeStudy  $timeStudy
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimeStudy $timeStudy)
    {
        //
    }
}
