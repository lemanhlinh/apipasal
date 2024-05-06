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
use Illuminate\Support\Facades\Storage;

class DayShiftLearnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dayShift = DayShiftLearn::orderBy('id','DESC')->get();
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
    public function store(Request  $request)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            DayShiftLearn::create([
                'title' => $title,
                'active' => 1,
            ]);

            DB::commit();
            return  response()->json(array(
                'error' => false,
                'result' => 'Thêm thời gian học thành công',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return  response()->json(array(
                'error' => true,
                'result' => 'Chưa thêm được thời gian học',
            ));
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
    public function update(Request  $request, $id)
    {
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $cat = DayShiftLearn::findOrFail($id);
            $cat->update([
                'title' => $title,
                'active' => 1,
            ]);
            DB::commit();
            return  response()->json(array(
                'error' => false,
                'result' => 'Cập nhật thành công',
            ));
        } catch (\Exception $exception) {
            \Log::info([
                'message' => $exception->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return  response()->json(array(
                'error' => true,
                'result' => 'Chưa cập nhật được',
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DayShiftLearn  $dayShiftLearn
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = DayShiftLearn::findOrFail($id);
        $data->delete($id);

        return [
            'status' => true,
            'message' => trans('message.delete_page_success')
        ];
    }
}
