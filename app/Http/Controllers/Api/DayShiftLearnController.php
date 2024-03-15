<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\CreateDayShiftLearn;
use App\Http\Requests\Schedule\UpdateDayShiftLearn;
use App\Models\CourseCategories;
use App\Models\DayShiftLearn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DayShiftLearnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dayShift = DayShiftLearn::all();
        return $dayShift;
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
    public function store(CreateDayShiftLearn $req)
    {
        DB::beginTransaction();
        try {
            $data = $req->validated();
            DayShiftLearn::create([
                'title' => $data['title'],
                'active' => 1,
            ]);

            DB::commit();
            Session::flash('success', 'Đã thêm mới Thời gian học');
            return redirect()->back();
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            Session::flash('danger', 'Chưa thêm được thời gian học');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DayShiftLearn  $dayShiftLearn
     * @return \Illuminate\Http\Response
     */
    public function show(DayShiftLearn $dayShiftLearn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DayShiftLearn  $dayShiftLearn
     * @return \Illuminate\Http\Response
     */
    public function edit(DayShiftLearn $dayShiftLearn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DayShiftLearn  $dayShiftLearn
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDayShiftLearn $req, $id)
    {
        DB::beginTransaction();
        try {
            $data = $req->validated();
            $cat = DayShiftLearn::findOrFail($id);
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
     * @param  \App\Models\DayShiftLearn  $dayShiftLearn
     * @return \Illuminate\Http\Response
     */
    public function destroy(DayShiftLearn $dayShiftLearn)
    {
        //
    }
}
